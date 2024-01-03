<?php

namespace App\Http\Livewire\Pengajuan;

use App\Models\Pengajuan;
use App\Models\Kepesertaan;
use Livewire\Component;
use App\Models\PengajuanHistory;
use App\Models\Rate;
use App\Models\Polis as ModelPolis;
use App\Models\UnderwritingLimit;
use App\Models\Finance\Income;
use App\Models\Finance\Polis;
use App\Models\Finance\SyariahUnderwriting;
use Livewire\WithFileUploads;
use App\Jobs\PengajuanCalculate;
use App\Mail\EmailSpk;

class Edit extends Component
{
    use WithFileUploads;
    public $data,$no_pengajuan,$kepesertaan=[],$kepesertaan_proses,$kepesertaan_approve,$kepesertaan_reject,$note_edit;
    public $check_all=0,$check_id=[],$check_arr,$selected,$status_reject=2,$note,$tab_active='tab_postpone';
    protected $listeners = ['reload-page'=>'$refresh','set_calculate'=>'set_calculate'];
    public $total_nilai_manfaat=0,$total_dana_tabbaru=0,$total_dana_ujrah=0,$total_kontribusi=0,$total_em=0,$total_ek=0,$total_total_kontribusi=0;
    public $show_peserta = 1,$filter_ul,$filter_ul_arr=[],$transaction_id,$file,$is_calculate=false,$is_draft=false;
    public function render()
    {
        $this->kepesertaan_proses = Kepesertaan::where(['pengajuan_id'=>$this->data->id])->where(function($table){
            if($this->show_peserta==2) $table->where('is_double',1);
            if($this->filter_ul) $table->where('ul',$this->filter_ul);
        })->orderBy('id','ASC')->get();
        $this->kepesertaan_approve = Kepesertaan::where(['pengajuan_id'=>$this->data->id,'status_akseptasi'=>1])->where(function($table){
            if($this->show_peserta==2) $table->where('is_double',1);
            if($this->filter_ul) $table->where('ul',$this->filter_ul);
        })->orderBy('id','ASC')->get();
        $this->kepesertaan_reject = Kepesertaan::where(['pengajuan_id'=>$this->data->id,'status_akseptasi'=>2])->where(function($table){
            if($this->show_peserta==2) $table->where('is_double',1);
            if($this->filter_ul) $table->where('ul',$this->filter_ul);
        })->get();
        $this->data->total_akseptasi = $this->kepesertaan_proses->count();
        $this->data->total_approve = $this->kepesertaan_approve->count();
        $this->data->total_reject = $this->kepesertaan_reject->count();
        $this->data->save();

        return view('livewire.pengajuan.edit');
    }

    public function mount(Pengajuan $data)
    {
        $this->data = $data;
        $this->no_pengajuan = $data->no_pengajuan;
        $this->filter_ul_arr = Kepesertaan::where('pengajuan_id',$this->data->id)->groupBy('ul')->get();
        $this->transaction_id = $this->data->id;
    }

    public function set_calculate($condition=false)
    {
        $this->is_calculate = $condition;
        $this->emit('reload-row');
        $this->total_pengajuan = Kepesertaan::where(['polis_id'=>$this->data->polis_id,'is_temp'=>1])->count();
    }
    public function calculate()
    {
        $this->is_calculate = true;
        PengajuanCalculate::dispatch($this->data->polis_id,$this->data->perhitungan_usia,$this->data->masa_asuransi,$this->transaction_id,'draft');
    }

    public function submit()
    {
        \LogActivity::add('[web] Submit Pengajuan - '. $this->data->no_pengajuan);

        $this->data->status = 0;
        $this->data->save();

        foreach(Kepesertaan::where(['polis_id'=>$this->data->polis_id,'pengajuan_id'=>$this->data->id])->get() as $item){
            $item->is_temp = 0;
            $item->status_akseptasi = 0;
            $item->status_polis = 'Akseptasi';
            $item->save();
        }

        session()->flash('message-success',__('Pengajuan berhasil disubmit, silahkan menunggu persetujuan'));

        return redirect()->route('pengajuan.index');
        
    }

    public function updated($propertyName)
    {
        if($propertyName=='check_all' and $this->check_all==1){
            foreach($this->data->kepesertaan as $k => $item){
                $this->check_id[$k] = $item->id;
            }
        }elseif($propertyName=='check_all' and $this->check_all==0){
            $this->check_id = [];
        }

        if($propertyName=='note') $this->note_edit = $this->note;
    }

    
    public function approve_pusat()
    {
        \LogActivity::add("[web][Pengajuan][{$this->data->no_pengajuan}] Approve Pusat");

        $this->data->status = 1;
        $this->data->save();

        $peserta = Kepesertaan::where(['pengajuan_id'=>$this->data->id])->first();
    

        \Mail::to(['doni.enginer@gmail.com',
        'sutarto@reliance-life.com',
        'bagus.prakoso@reliance-life.com',
        'erna.rafika@reliance-life.com',
        'hari.prasetyo@reliance-life.com'])->send(new EmailSpk('Asuransi Jiwa Reliance - Surat Pernyataan Kesehatan (SPK) - '.$peserta->nama,$peserta));
        
        $this->emit('message-success','Data berhasil di proses');
        $this->emit('reload-page');
    }

    public function upload()
    {
        $this->validate([
            'file'=>'required|mimes:xlsx|max:51200', // 50MB maksimal
        ]);
        
        Kepesertaan::where('pengajuan_id',$this->data->id)->delete();

        $path = $this->file->getRealPath();
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(true);
        $xlsx = $reader->load($path);
        $sheetData = $xlsx->getActiveSheet()->toArray();
        $total_data = 0;
        $total_double = 0;
        $total_success = 0;
        // Kepesertaan::where(['polis_id'=>$this->data->polis_id,'is_temp'=>1,'is_double'=>1])->delete();
        $insert = [];
        foreach($sheetData as $key => $item){
            if($key<=1) continue;
            /**
             * Skip
             * Nama, Tanggal lahir
             */
            if($item[1]=="" || $item[10]=="") continue;
            $insert[$total_data]['polis_id'] = $this->data->polis_id;
            $insert[$total_data]['nama'] = $item[1];
            $insert[$total_data]['no_ktp'] = $item[2];
            $insert[$total_data]['alamat'] = $item[3];
            $insert[$total_data]['no_telepon'] = $item[4];
            $insert[$total_data]['pekerjaan'] = $item[5];
            $insert[$total_data]['bank'] = $item[6];
            $insert[$total_data]['cab'] = $item[7];
            $insert[$total_data]['no_closing'] = $item[8];
            $insert[$total_data]['no_akad_kredit'] = $item[9];
            $insert[$total_data]['tanggal_lahir'] = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item[10])->format('Y-m-d');
            $insert[$total_data]['jenis_kelamin'] = $item[11];
            if($item[12]) $insert[$total_data]['tanggal_mulai'] = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item[12])->format('Y-m-d');
            if($item[13]) $insert[$total_data]['tanggal_akhir'] = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item[13])->format('Y-m-d');
            $insert[$total_data]['basic'] = $item[14];
            $insert[$total_data]['tinggi_badan'] = $item[15];
            $insert[$total_data]['berat_badan'] = $item[16];
            $insert[$total_data]['kontribusi'] = 0;
            $insert[$total_data]['is_temp'] = 1;
            $insert[$total_data]['is_double'] = 2;
            $insert[$total_data]['pengajuan_id'] = $this->data->id;
            $insert[$total_data]['status_polis'] = 'Akseptasi';
            $total_data++;
        }

        if(count($insert)>0)  {
            Kepesertaan::insert($insert);
        }

        $this->emit('reload-row');
        $this->emit('attach-file');
    }

    public function hitung()
    {
        // $this->is_calculate = true;
        // PengajuanCalculate::dispatch($this->data->polis_id,$this->data->perhitungan_usia,$this->data->masa_asuransi,$this->transaction_id);
        foreach($this->data->kepesertaan as $data){
            $data->usia = $data->tanggal_lahir ? hitung_umur($data->tanggal_lahir,$this->data->perhitungan_usia,$data->tanggal_mulai) : '0';
            $data->masa = hitung_masa($data->tanggal_mulai,$data->tanggal_akhir);
            $data->masa_bulan = hitung_masa_bulan($data->tanggal_mulai,$data->tanggal_akhir,$this->data->masa_asuransi);

            if($data->is_double){
                $sum =  Kepesertaan::where(['nama'=>$data->nama,'tanggal_lahir'=>$data->tanggal_lahir,'status_polis'=>'Inforce'])->sum('basic');
                $data->akumulasi_ganda = $sum+$data->basic;
            }
            $nilai_manfaat_asuransi = $data->basic;

            // find rate
            $rate = Rate::where(['tahun'=>$data->usia,'bulan'=>$data->masa_bulan,'polis_id'=>$this->data->polis_id])->first();

            //$rate = Rate::where(['tahun'=>$data->usia,'bulan'=>$data->masa_bulan,'polis_id'=>$this->data->polis_id])->first();
            $data->rate = $rate ? $rate->rate : 0;
            $data->kontribusi = $nilai_manfaat_asuransi * $data->rate/1000;

            // find rate
            if(!$rate || $rate->rate ==0 || $rate->rate ==""){
                $data->rate = 0;
                $data->kontribusi = 0;
            }else{
                $data->rate = $rate ? $rate->rate : 0;
                $data->kontribusi = $nilai_manfaat_asuransi * $data->rate/1000;
            }

            $data->dana_tabarru = ($data->kontribusi*$data->polis->iuran_tabbaru)/100; // persen ngambil dari daftarin polis
            $data->dana_ujrah = ($data->kontribusi*$data->polis->ujrah_atas_pengelolaan)/100;
            $data->extra_mortalita = $data->rate_em*$nilai_manfaat_asuransi/1000;

            if($data->akumulasi_ganda)
                $uw = UnderwritingLimit::whereRaw("{$data->akumulasi_ganda} BETWEEN min_amount and max_amount")->where(['usia'=>$data->usia,'polis_id'=>$this->data->polis_id])->first();
            else
                $uw = UnderwritingLimit::whereRaw("{$nilai_manfaat_asuransi} BETWEEN min_amount and max_amount")->where(['usia'=>$data->usia,'polis_id'=>$this->data->polis_id])->first();

            if(!$uw) $uw = UnderwritingLimit::where(['usia'=>$data->usia,'polis_id'=>$this->data->polis_id])->orderBy('max_amount','ASC')->first();
            if($uw){
                $data->uw = $uw->keterangan;
                $data->ul = $uw->keterangan;
            }
            $data->save();
        }

        $this->emit('message-success','Data berhasil dikalkukasi');
        $this->emit('reload-page');
    }

    public function submit_head_syariah()
    {
        \LogActivity::add("[web][Pengajuan][{$this->data->no_pengajuan}] Submit Admin Ajri");
        // generate DN Number
        $running_number_dn = $this->data->polis->running_number_dn+1;
        $dn_number = $this->data->polis->no_polis ."/". str_pad($running_number_dn,4, '0', STR_PAD_LEFT)."/AJRIUS-DN/".numberToRomawi(date('m'))."/".date('Y');
        $this->data->dn_number = $dn_number;

        $running_no_surat = get_setting('running_surat')+1;

        $this->data->no_surat = str_pad($running_no_surat,6, '0', STR_PAD_LEFT).'/UWS-M/AJRI-US/'.numberToRomawi(date('m')).'/'.date('Y');

        update_setting('running_surat',$running_no_surat);

        $this->data->status = 3;
        $this->data->total_akseptasi = $this->kepesertaan_proses->count();
        $this->data->total_approve = $this->kepesertaan_approve->count();
        $this->data->total_reject = $this->kepesertaan_reject->count();
        $this->data->head_syariah_submit = date('Y-m-d');
        $this->data->save();

        // generate no peserta
        $running_number = $this->data->polis->running_number_peserta;

        $key=0;
        foreach($this->data->kepesertaan->where('status_akseptasi',1) as $peserta){
            $running_number++;
            $no_peserta = (isset($this->data->polis->produk->id) ? $this->data->polis->produk->id : '0') ."-". date('ym').str_pad($running_number,7, '0', STR_PAD_LEFT).'-'.str_pad($this->data->polis->running_number,3, '0', STR_PAD_LEFT);
            $peserta->no_peserta = $no_peserta;
            $peserta->status_polis = 'Inforce';

            if($peserta->ul=='GOA'){
                if(isset($peserta->polis->waiting_period) and $peserta->polis->waiting_period !="")
                    $peserta->tanggal_stnc = date('Y-m-d',strtotime(" +{$peserta->polis->waiting_period} month", strtotime($this->data->head_syariah_submit)));
                else{
                    if(countDay($this->data->head_syariah_submit,$peserta->tanggal_mulai) > $peserta->polis->retroaktif){
                        $peserta->tanggal_stnc = date('Y-m-d');
                    }elseif(countDay($this->data->head_syariah_submit,$peserta->tanggal_mulai) < $peserta->polis->retroaktif){
                        $peserta->tanggal_stnc = null;
                    }
                }
            }

            if(in_array($peserta->ul,['NM','A','B','C'])) $peserta->tanggal_stnc = date('Y-m-d');

            $peserta->save();

            $key++;
        }

        $get_peserta_awal =  Kepesertaan::where(['pengajuan_id'=>$this->data->id,'status_akseptasi'=>1])->orderBy('no_peserta','ASC')->first();
        if($get_peserta_awal) $this->data->no_peserta_awal = $get_peserta_awal->no_peserta;

        $no_peserta_akhir =  Kepesertaan::where(['pengajuan_id'=>$this->data->id,'status_akseptasi'=>1])->orderBy('no_peserta','DESC')->first();
        if($no_peserta_akhir) $this->data->no_peserta_akhir = $no_peserta_akhir->no_peserta;

        // save running number
        ModelPolis::where('id',$this->data->polis->id)->update(
            [
                'running_number_dn' => $running_number_dn,
                'running_number_peserta' => $running_number
            ]);

        if(isset($this->data->polis->masa_leluasa)) $this->data->tanggal_jatuh_tempo = date('Y-m-d',strtotime("+{$this->data->polis->masa_leluasa} days"));

        $this->data->save();

        $select = Kepesertaan::select(\DB::raw("SUM(basic) as total_nilai_manfaat"),
                                        \DB::raw("SUM(dana_tabarru) as total_dana_tabbaru"),
                                        \DB::raw("SUM(dana_ujrah) as total_dana_ujrah"),
                                        \DB::raw("SUM(kontribusi) as total_kontribusi"),
                                        \DB::raw("SUM(extra_kontribusi) as total_extra_kontribusi"),
                                        \DB::raw("SUM(extra_mortalita) as total_extra_mortalita")
                                        )->where(['pengajuan_id'=>$this->data->id,'status_akseptasi'=>1])->first();

        $nilai_manfaat = $select->total_nilai_manfaat;
        $dana_tabbaru = $select->total_dana_tabbaru;
        $dana_ujrah = $select->total_dana_ujrah;
        $kontribusi = $select->total_kontribusi;
        $ektra_kontribusi = $select->total_extract_kontribusi;
        $extra_mortalita = $select->total_extra_mortalita;

        $this->data->nilai_manfaat = $nilai_manfaat;
        $this->data->dana_tabbaru = $dana_tabbaru;
        $this->data->dana_ujrah = $dana_ujrah;
        $this->data->kontribusi = $kontribusi;
        $this->data->extra_kontribusi = $ektra_kontribusi;
        $this->data->extra_mortalita = $extra_mortalita;

        if($this->data->polis->potong_langsung){
            $this->data->potong_langsung_persen = $this->data->polis->potong_langsung;
            $this->data->potong_langsung = $kontribusi*($this->data->polis->potong_langsung/100);
        }

        /**
         * Hitung PPH
         */
        if($this->data->polis->pph){
            $this->data->pph_persen =  $this->data->polis->pph;
            if($this->data->potong_langsung)
                $this->data->pph = (($this->data->polis->pph/100) * $this->data->potong_langsung);
            else
                $this->data->pph = $kontribusi*($this->data->polis->pph/100);
        }

         /**
         * Hitung PPN
         */
        if($this->data->polis->ppn){
            $this->data->ppn_persen =  $this->data->polis->ppn;
            if($this->data->potong_langsung)
                $this->data->ppn = (($this->data->polis->ppn/100) * $this->data->potong_langsung);
            else
                $this->data->ppn = $kontribusi*($this->data->polis->ppn/100);
        }

        /**
         * Biaya Polis dan Materai
         * jika pengajuan baru pertama kali ada biaya polis dan materia 100.000
         * */
        if($running_number==0){
            $this->data->biaya_polis_materai = $this->data->polis->biaya_polis_materai;
            $this->data->biaya_sertifikat = $this->data->polis->biaya_sertifikat;
        }

        $total = $kontribusi+$ektra_kontribusi+$extra_mortalita+$this->data->biaya_sertifikat+$this->data->biaya_polis_materai+$this->data->pph-($this->data->ppn+$this->data->potong_langsung);
        $this->data->net_kontribusi = $total;
        $this->data->save();

        $select_tertunda =  Kepesertaan::select(\DB::raw("SUM(basic) as total_nilai_manfaat"),
                                        \DB::raw("SUM(dana_tabarru) as total_dana_tabbaru"),
                                        \DB::raw("SUM(dana_ujrah) as total_dana_ujrah"),
                                        \DB::raw("SUM(kontribusi) as total_kontribusi"),
                                        \DB::raw("SUM(extra_kontribusi) as total_extra_kontribusi"),
                                        \DB::raw("SUM(extra_mortalita) as total_extra_mortalita")
                                        )->where(['pengajuan_id'=>$this->data->id,'status_akseptasi'=>2])->first();

        $manfaat_Kepesertaan_tertunda = $select_tertunda->total_nilai_manfaat;
        $kontribusi_kepesertaan_tertunda =  $select_tertunda->total_kontribusi;

        $this->emit('message-success','Data berhasil di proses');
        $this->emit('reload-page');
    }

    public function set_id(Kepesertaan $data)
    {
        $this->selected = $data;
    }

    public function approve(Kepesertaan $data)
    {
        \LogActivity::add("[web][Pengajuan][{$this->data->no_pengajuan}] Approve");

        $data->status_akseptasi = 1;
        $data->save();

        PengajuanHistory::insert([
            'pengajuan_id' => $data->pengajuan_id,
            'kepesertaan_id' => $data->id,
            'user_id' => \Auth::user()->id,
            'status' => 1
        ]);
        $this->emit('message-success','Data berhasil di setujui');
        $this->emit('reload-page');
    }

    public function submit_rejected()
    {
        $this->validate([
            'note_edit' => 'required'
        ],[
            'note_edit.required' => 'Note required'
        ]);

        if(\Auth::user()->user_access_id==3){
            $this->data->status = 2; // Reject by Pusat
            \LogActivity::add("[web][Pengajuan][{$this->data->no_pengajuan}] Reject Pusat");
        }else{
            $this->data->status = 4; // Reject by Ajri
            \LogActivity::add("[web][Pengajuan][{$this->data->no_pengajuan}] Reject Ajri");
        }

        $this->data->save();

        $tolak = Kepesertaan::where('pengajuan_id',$this->data->id)->get();
        foreach($tolak as $item){
            $item->reason_reject = $this->note_edit;
            $item->status_akseptasi = 2;
            $item->save();

            PengajuanHistory::insert([
                'pengajuan_id' => $item->pengajuan_id,
                'kepesertaan_id' => $item->id,
                'reason' => $this->note,
                'user_id' => \Auth::user()->id,
                'status' => 2
            ]);
        }

        $this->emit('message-success','Data berhasil di proses');

        $this->emit('reload-page');
        $this->emit('modal','hide');
    }

    public function resubmit()
    {
        \LogActivity::add('[web] Resubmit Pengajuan - '. $this->data->no_pengajuan);

        $this->data->status = 0;
        $this->data->save();

        foreach(Kepesertaan::where(['polis_id'=>$this->data->polis_id,'pengajuan_id'=>$this->data->id])->get() as $item){
            $item->is_temp = 0;
            $item->status_akseptasi = 0;
            $item->status_polis = 'Akseptasi';
            $item->save();
        }

        session()->flash('message-success',__('Pengajuan berhasil disubmit, silahkan menunggu persetujuan'));

        return redirect()->route('pengajuan.index');
    }
}
