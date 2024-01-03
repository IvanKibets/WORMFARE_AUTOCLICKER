<?php

namespace App\Http\Livewire\PengajuanHarian;

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
use App\Models\Finance\Journal;
use App\Jobs\PengajuanCalculate;

class Edit extends Component
{
    public $data,$no_pengajuan,$kepesertaan=[],$kepesertaan_proses,$kepesertaan_approve,$kepesertaan_reject,$note_edit;
    public $check_all=0,$check_id=[],$check_arr,$selected,$status_reject=2,$note,$tab_active='tab_postpone';
    protected $listeners = ['reload-page'=>'$refresh'];
    public $total_nilai_manfaat=0,$total_dana_tabbaru=0,$total_dana_ujrah=0,$total_kontribusi=0,$total_em=0,$total_ek=0,$total_total_kontribusi=0;
    public $show_peserta = 1,$filter_ul,$filter_ul_arr=[],$transaction_id;
    public function render()
    {
        $this->kepesertaan_proses = Kepesertaan::where(['pengajuan_id'=>$this->data->id,'status_akseptasi'=>0])->where(function($table){
            if($this->show_peserta==2) $table->where('is_double',1);
            if($this->filter_ul) $table->where('ul',$this->filter_ul);
        })->get();
        $this->kepesertaan_approve = Kepesertaan::where(['pengajuan_id'=>$this->data->id,'status_akseptasi'=>1])->where(function($table){
            if($this->show_peserta==2) $table->where('is_double',1);
            if($this->filter_ul) $table->where('ul',$this->filter_ul);
        })->orderBy('no_peserta','ASC')->get();
        $this->kepesertaan_reject = Kepesertaan::where(['pengajuan_id'=>$this->data->id,'status_akseptasi'=>2])->where(function($table){
            if($this->show_peserta==2) $table->where('is_double',1);
            if($this->filter_ul) $table->where('ul',$this->filter_ul);
        })->get();

        $this->data->total_akseptasi = $this->kepesertaan_proses->count();
        $this->data->total_approve = $this->kepesertaan_approve->count();
        $this->data->total_reject = $this->kepesertaan_reject->count();
        $this->data->save();

        return view('livewire.pengajuan-harian.edit');
    }

    public function mount(Pengajuan $data)
    {
        $this->data = $data;
        $this->no_pengajuan = $data->no_pengajuan;
        $this->filter_ul_arr = Kepesertaan::where('pengajuan_id',$this->data->id)->groupBy('ul')->get();
        $this->transaction_id = date('dmyHis');
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

    public function submit_underwriting()
    {
        \LogActivity::add("[web][Pengajuan][{$this->data->no_pengajuan}] Submit Head Underwriting");

        $this->data->total_akseptasi = $this->kepesertaan_proses->count();
        $this->data->total_approve = $this->kepesertaan_approve->count();
        $this->data->total_reject = $this->kepesertaan_reject->count();
        $this->data->status = 1;
        $this->data->save();

        $this->emit('message-success','Data berhasil di proses');
        $this->emit('reload-page');
    }

    public function submit_head_teknik()
    {
        \LogActivity::add("[web][Pengajuan][{$this->data->no_pengajuan}] Submit Head Teknik");

        $this->data->total_akseptasi = $this->kepesertaan_proses->count();
        $this->data->total_approve = $this->kepesertaan_approve->count();
        $this->data->total_reject = $this->kepesertaan_reject->count();
        $this->data->status = 2;
        $this->data->save();

        $this->emit('message-success','Data berhasil di proses');
        $this->emit('reload-page');
    }

    public function hitung()
    {
        // $this->is_calculate = true;
        // PengajuanCalculate::dispatch($this->data->polis_id,$this->data->perhitungan_usia,$this->data->masa_asuransi,$this->transaction_id);

        // /*
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
            $data->rate = $rate ? $rate->rate : 0;
            $data->kontribusi = $nilai_manfaat_asuransi * $data->rate/1000;

            // find rate
            $rate = Rate::where(['tahun'=>$data->usia,'bulan'=>$data->masa_bulan,'polis_id'=>$this->data->polis_id])->first();
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

        /**
         *
            mstnc
            tanggal akseptasi - tanggal mulai > retroaktif dihitung today
            tanggal akseptasi - tanggal mulai  < retroaktif value 0

            jika ada waiting periode
            - tanggal akseptasi + waiting period

            jika ada waiting period dan retroaktif maka yg dipakai waiting period
         */

        // foreach($this->data->kepesertaan->where('status_akseptasi',1) as $peserta){
        //     if($peserta->ul=='GOA'){
        //         if(isset($peserta->polis->waiting_period) and $peserta->polis->waiting_period !="")
        //             $peserta->tanggal_stnc = date('Y-m-d',strtotime(" +{$peserta->polis->waiting_period} month", strtotime($this->data->head_syariah_submit)));
        //         else{
        //             if(countDay($this->data->head_syariah_submit,$peserta->tanggal_mulai) > $peserta->polis->retroaktif){
        //                 $peserta->tanggal_stnc = $this->data->head_syariah_submit;
        //             }elseif(countDay($this->data->head_syariah_submit,$peserta->tanggal_mulai) < $peserta->polis->retroaktif){
        //                 $peserta->tanggal_stnc = null;
        //             }
        //         }
        //     }

        //     if(in_array($peserta->ul,['NM','A','B','C'])) $peserta->tanggal_stnc = $this->data->head_syariah_submit;

        //     $peserta->save();
        // }

        // $key=0;
        // $running_number = 823;
        // foreach($this->data->kepesertaan->where('status_akseptasi',1) as $peserta){
        //     $running_number++;
        //     $no_peserta = (isset($this->data->polis->produk->id) ? $this->data->polis->produk->id : '0') ."-". date('ym').str_pad($running_number,7, '0', STR_PAD_LEFT).'-'.str_pad($this->data->polis->running_number,3, '0', STR_PAD_LEFT);
        //     $peserta->no_peserta = $no_peserta;

        //     $peserta->save();

        //     $key++;
        // }

        $this->emit('message-success','Data berhasil dikalkukasi');
        $this->emit('reload-page');
    }

    public function submit_head_syariah()
    {
        \LogActivity::add("[web][Pengajuan][{$this->data->no_pengajuan}] Submit Head Syariah");
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

        SyariahUnderwriting::insert([
            'bulan' => date('F'),
            'user_memo' => \Auth::user()->name,
            'user_akseptasi' => \Auth::user()->name,
            'transaksi_id' => $this->data->no_pengajuan,
            'tanggal_produksi'=> date('Y-m-d'),
            'no_debit_note' => $this->data->dn_number,
            'no_polis' => $this->data->polis->no_polis,
            'pemegang_polis' => $this->data->polis->nama,
            'alamat' => $this->data->polis->alamat,
            'jenis_produk' => isset($this->data->polis->produk->nama) ? $this->data->polis->produk->nama : '-',
            'jml_kepesertaan_tertunda' => $this->data->total_reject,
            'manfaat_Kepesertaan_tertunda' => $manfaat_Kepesertaan_tertunda,
            'kontribusi_kepesertaan_tertunda' => $kontribusi_kepesertaan_tertunda,
            'jml_kepesertaan' => $this->data->total_approve,
            // 'no_kepesertaan_awal' => $no_kepesertaan_awal,
            // $data->no_kepesertaan_akhir = $no_kepesertaan_akhir;
            // $data->masa_awal_asuransi = $masa_awal_asuransi;
            // $data->masa_akhir_asuransi = $masa_akhir_asuransi;
            'nilai_manfaat' => $nilai_manfaat,
            'dana_tabbaru' => $dana_tabbaru,
            'dana_ujrah' => $dana_ujrah,
            'kontribusi' => $kontribusi,
            'ektra_kontribusi' => $ektra_kontribusi,
            'total_kontribusi' => $kontribusi,
            'pot_langsung' => $this->data->potong_langsung_persen,
            'jumlah_diskon' => $this->data->potong_langsung,
            // $data->status_potongan = $status_potongan;
            // $data->handling_fee = $handling_fee;
            // $data->jumlah_fee = $jumlah_fee;
            'pph' => $this->data->pph_persen,
            'jumlah_pph' => $this->data->pph,
            'ppn' => $this->data->ppn_persen,
            'jumlah_ppn' => $this->data->ppn,
            // $data->biaya_polis = $biaya_polis;
            // $data->biaya_sertifikat = $biaya_sertifikat;
            // $data->extpst = $extpst;
            'net_kontribusi' => $total,
            // $data->terbilang = $terbilang;
            // if($tgl_update_database) $data->tgl_update_database = date('Y-m-d',($tgl_update_database));
            'tgl_update_sistem' => date('Y-m-d'),
            // $data->no_berkas_sistem = $no_berkas_sistem;
            // if($tgl_posting_sistem) $data->tgl_posting_sistem = date('Y-m-d',($tgl_posting_sistem));
            // $data->ket_posting = $ket_posting;
            // $data->grace_periode = $grace_periode;
            // $data->grace_periode_number = $grace_periode_number;
            // if($tgl_jatuh_tempo) $data->tgl_jatuh_tempo = date('Y-m-d',($tgl_jatuh_tempo));
            // if($tgl_lunas) $data->tgl_lunas = date('Y-m-d',($tgl_lunas));
            // $data->pembayaran = $pembayaran;
            // $data->piutang = $piutang;
            // $data->total_peserta = $total_peserta;
            // $data->outstanding_peserta = $outstanding_peserta;
            // $data->produksi_cash_basis = $produksi_cash_basis;
            // $data->ket_lampiran = $ket_lampiran;
            // $data->pengeluaran_ujroh = $pengeluaran_ujroh;
            'status' => 1,
            'user_id' => \Auth::user()->id
        ]);

        // find polis
        $polis = Polis::where('no_polis',$this->data->polis->no_polis)->first();
        if(!$polis){
            $polis = new Polis();
            $polis->no_polis = $this->data->polis->no_polis;
            $polis->pemegang_polis = $this->data->polis->nama;
            $polis->alamat = $this->data->polis->alamat;
            $polis->save();
        }

        // insert finance
        $income = new Income();
        $income->user_id = \Auth::user()->id;
        $income->reference_no = $this->data->dn_number;
        $income->reference_date = date('Y-m-d');
        $income->nominal = $total;
        $income->client = $this->data->polis->no_polis .'/'. $this->data->polis->nama;
        $income->reference_type = 'Premium Receivable';
        $income->transaction_table = 'syariah_underwriting';
        $income->transaction_id = $this->data->id;
        $income->type = 2; // Syariah
        $income->policy_id = $polis->id;
        if($this->data->tanggal_jatuh_tempo) $income->due_date = $this->data->tanggal_jatuh_tempo;
        $income->save();

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

        \LogActivity::add("[web][Pengajuan][{$this->data->no_pengajuan}] Reject Peserta");

        $this->selected->reason_reject = $this->note_edit;
        $this->selected->status_akseptasi = 2;
        $this->selected->save();

        PengajuanHistory::insert([
            'pengajuan_id' => $this->selected->pengajuan_id,
            'kepesertaan_id' => $this->selected->id,
            'reason' => $this->note,
            'user_id' => \Auth::user()->id,
            'status' => 2
        ]);

        $this->emit('message-success','Data berhasil di proses');

        $this->emit('reload-page');
        $this->emit('modal','hide');
    }

    public function approveAll()
    {
        foreach($this->data->kepesertaan as $item){
            $item->status_akseptasi = 1;
            $item->save();

            PengajuanHistory::insert([
                'pengajuan_id' => $item->pengajuan_id,
                'kepesertaan_id' => $item->id,
                'user_id' => \Auth::user()->id,
                'status' => 1
            ]);
        }
        $this->check_id = []; $this->check_all = 0;

        \LogActivity::add("[web][Pengajuan][{$this->data->no_pengajuan}] Approve All");

        $this->emit('message-success','Data berhasil di setujui');
        $this->emit('reload-page');
    }

    public function rejectAll()
    {
        foreach($this->data->kepesertaan as $item){
            $item->status_akseptasi = 2;
            $item->save();

            PengajuanHistory::insert([
                'pengajuan_id' => $item->pengajuan_id,
                'kepesertaan_id' => $item->id,
                'user_id' => \Auth::user()->id,
                'status' => 2
            ]);
        }
        $this->check_id = [];$this->check_all = 0;

        $this->emit('reload-page');
        $this->emit('message-success','Data berhasil diproses');
    }
}
