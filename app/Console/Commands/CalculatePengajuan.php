<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Kepesertaan;
use App\Models\Pengajuan;
use App\Models\Polis;
use App\Models\Rate;
use App\Models\UnderwritingLimit;
use App\Events\RequestPengajuan;

class CalculatePengajuan extends Command
{
    public $polis_id=10,$perhitungan_usia=1,$masa_asuransi=1;
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pengajuan:calculate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ini_set('memory_limit', '-1');
        foreach(Pengajuan::withCount(['akseptasi','diterima','ditolak'])
            ->withSum('diterima','total_kontribusi_dibayar')->where('status',3)->get() as $k => $item){
            // $item->net_kontribusi = Kepesertaan::where('pengajuan_id',$item->id)->sum('total_kontribusi_dibayar');

            echo "{$k} => No Pengajuan : {$item->no_pengajuan} => ". format_idr($item->net_kontribusi) ."\n";
            // if($item->payment_date) $item->status_invoice = 1;
            // $peserta = $item->kepesertaan->first();
            // $item->created_at = $peserta->tanggal_mulai;
            // $item->total_akseptasi = $item->akseptasi_count;
            // $item->total_approve = $item->diterima_count;
            // $item->total_reject = $item->ditolak_count;
            // $item->net_kontribusi = $item->diterima_sum_total_kontribusi_dibayar;

            $select = Kepesertaan::select(\DB::raw("SUM(basic) as total_nilai_manfaat"),
                        \DB::raw("SUM(dana_tabarru) as total_dana_tabbaru"),
                        \DB::raw("SUM(dana_ujrah) as total_dana_ujrah"),
                        \DB::raw("SUM(kontribusi) as total_kontribusi"),
                        \DB::raw("SUM(extra_kontribusi) as total_extra_kontribusi"),
                        \DB::raw("SUM(extra_mortalita) as total_extra_mortalita")
                        )->where(['pengajuan_id'=>$item->id,'status_akseptasi'=>1])->first();

            $nilai_manfaat = $select->total_nilai_manfaat;
            $dana_tabbaru = $select->total_dana_tabbaru;
            $dana_ujrah = $select->total_dana_ujrah;
            $kontribusi = $select->total_kontribusi;
            $ektra_kontribusi = $select->total_extract_kontribusi;
            $extra_mortalita = $select->total_extra_mortalita;

            $item->nilai_manfaat = $nilai_manfaat;
            $item->dana_tabbaru = $dana_tabbaru;
            $item->dana_ujrah = $dana_ujrah;
            $item->kontribusi = $kontribusi;
            $item->extra_kontribusi = $ektra_kontribusi;
            $item->extra_mortalita = $extra_mortalita;

            if($item->polis->potong_langsung){
                $item->potong_langsung_persen = $item->polis->potong_langsung;
                $item->potong_langsung = $kontribusi*($item->polis->potong_langsung/100);
            }

            /**
            * Hitung PPH
            */
            if($item->polis->pph){
                $item->pph_persen =  $item->polis->pph;
                if($item->potong_langsung)
                    $item->pph = (($item->polis->pph/100) * $item->potong_langsung);
                else
                    $item->pph = $kontribusi*($item->polis->pph/100);
            }

            /**
            * Hitung PPN
            */
            if($item->polis->ppn){
                $item->ppn_persen =  $item->polis->ppn;
                $item->ppn = $kontribusi*($item->polis->ppn/100);
            }

            /**
            * Biaya Polis dan Materai
            * jika pengajuan baru pertama kali ada biaya polis dan materia 100.000
            * */ 
            // if($running_number==0){
            //     $item->biaya_polis_materai = $item->polis->biaya_polis_materai;
            //     $item->biaya_sertifikat = $item->polis->biaya_sertifikat;
            // }

            $total = $kontribusi+$ektra_kontribusi+$extra_mortalita+$item->biaya_sertifikat+$item->pph+$item->ppn-$item->potong_langsung;
            $item->net_kontribusi = $total;
            $item->save();
        }

        // foreach(Kepesertaan::whereNull('pengajuan_id')->groupBy('no_debit_note')->get() as $k => $item){
        //     echo "{$k} => No Debit Note : {$item->no_debit_note}\n";
        //     $pengajuan = Pengajuan::where('dn_number',$item->no_debit_note)->first();
        //     if($item->no_debit_note=='MANUAL' || $item->no_debit_note =="") continue;
        //     if($pengajuan) continue;

        //     $pengajuan = new Pengajuan;
        //     $pengajuan->dn_number = $item->no_debit_note;
        //     $pengajuan->no_pengajuan = date('dmy').str_pad((Pengajuan::count()+1),6, '0', STR_PAD_LEFT);
        //     $pengajuan->status=4;
        //     $pengajuan->polis_id = $item->polis_id;
        //     $pengajuan->payment_date = $item->pay_date;
        //     $pengajuan->save();

        //     Kepesertaan::where('no_debit_note',$item->no_debit_note)->update(['status_akseptasi'=>1,'pengajuan_id'=>$pengajuan->id]);
        // }


        // $polis = Polis::find($this->polis_id);
        // $iuran_tabbaru = $polis->iuran_tabbaru;
        // $ujrah = $polis->ujrah_atas_pengelolaan;
        // $key=0;
        // $update  =[];
        // foreach(Kepesertaan::where(['polis_id'=>$this->polis_id,'is_temp'=>1])->get() as $data){
        //     $check =  Kepesertaan::where(['nama'=>$data->nama,'tanggal_lahir'=>$data->tanggal_lahir])->where(function($table){
        //         $table->where('status_polis','Inforce')->orWhere('status_polis','Akseptasi');
        //     })->sum('basic');

        //     $update[$key]['id'] = $data->id;
        //     if($check>0){
        //         $update[$key]['is_double'] = 1;
        //         $update[$key]['akumulasi_ganda'] =$check+$data->basic;
        //     }else $update[$key]['is_double']=0;

        //     $nilai_manfaat_asuransi = $data->basic;

        //     $update[$key]['usia'] =  $data->tanggal_lahir ? hitung_umur($data->tanggal_lahir,$this->perhitungan_usia,$data->tanggal_mulai) : '0';
        //     $update[$key]['masa'] = hitung_masa($data->tanggal_mulai,$data->tanggal_akhir);
        //     $update[$key]['masa_bulan'] =  hitung_masa_bulan($data->tanggal_mulai,$data->tanggal_akhir,$this->masa_asuransi);

        //     // find rate
        //     $rate = Rate::where(['tahun'=>$data->usia,'bulan'=>$data->masa_bulan,'polis_id'=>$this->polis_id])->first();
        //     if(!$rate || $rate->rate ==0 || $rate->rate ==""){
        //         $data->rate = 0;
        //         $data->kontribusi = 0;
        //     }else{
        //         $update[$key]['rate'] = $rate ? $rate->rate : 0;
        //         $update[$key]['kontribusi'] = $nilai_manfaat_asuransi * $data->rate/1000;
        //     }
            
        //     $update[$key]['dana_tabarru'] = ($data->kontribusi*$iuran_tabbaru)/100; // persen ngambil dari daftarin polis
        //     $update[$key]['dana_ujrah'] = ($data->kontribusi*$ujrah)/100; 
        //     $update[$key]['extra_mortalita'] = $data->rate_em*$nilai_manfaat_asuransi/1000;
            
        //     if($data->akumulasi_ganda)
        //         $uw = UnderwritingLimit::whereRaw("{$data->akumulasi_ganda} BETWEEN min_amount and max_amount")->where(['usia'=>$data->usia,'polis_id'=>$this->polis_id])->first();
        //     else
        //         $uw = UnderwritingLimit::whereRaw("{$nilai_manfaat_asuransi} BETWEEN min_amount and max_amount")->where(['usia'=>$data->usia,'polis_id'=>$this->polis_id])->first();
            
        //     if(!$uw) $uw = UnderwritingLimit::where(['usia'=>$data->usia,'polis_id'=>$this->polis_id])->orderBy('max_amount','ASC')->first();
        //     if($uw){
        //         $update[$key]['uw'] = $uw->keterangan;
        //         $update[$key]['ul'] = $uw->keterangan;
        //     }
            
        //     $update[$key]['is_hitung'] = 1;
        //     $key++;
        //     echo "{$key}. Calculate Nama : ".$data->nama ."\n";
        // }

        // \Batch::update(new Kepesertaan,$update,'id');

        // event(new RequestPengajuan('Data berhasil dikalkukasi'));
    }
}
