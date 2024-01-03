<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Kepesertaan;
use App\Models\Polis;
use App\Models\Rate;
use App\Models\UnderwritingLimit;
use App\Events\RequestPengajuan;

class PengajuanCalculate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    public $polis_id,$perhitungan_usia=1,$masa_asuransi=1,$transaction_id,$type_calculate;

     /**
     * The number of seconds the job can run before timing out.
     *
     * @var int
     */
    public $timeout = 0;

    /**
     * Create a new job instance.
     *
     * @return void
     */
    public function __construct($polis_id,$perhitungan_usia,$masa_asuransi,$transaction_id,$type_calculate='new')
    {
        $this->polis_id = $polis_id;
        $this->perhitungan_usia = $perhitungan_usia;
        $this->masa_asuransi = $masa_asuransi;
        $this->transaction_id = $transaction_id;
        $this->type_calculate = $type_calculate;
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('memory_limit', '-1');
        $polis = Polis::find($this->polis_id);
        $iuran_tabbaru = $polis->iuran_tabbaru;
        $ujrah = $polis->ujrah_atas_pengelolaan;
        echo "Polis : {$polis->nama}\n\n";
        $key=0;
        $update  =[];
        $kepesertaan = Kepesertaan::where(function($table){
            if($this->type_calculate=='draft') {
                $table->where('pengajuan_id',$this->transaction_id);
            }else{
                $table->where(['polis_id'=>$this->polis_id,'is_temp'=>1]);
            }
        })->with(['double_peserta','rate_'])->get();

        foreach($kepesertaan as $data){

            echo "{$key}. Calculate Nama : ".$data->nama ."\n";
            // $check =  Kepesertaan::where(['nama'=>$data->nama,'tanggal_lahir'=>$data->tanggal_lahir])->where(function($table){
            //     $table->where('status_polis','Inforce')->orWhere('status_polis','Akseptasi');
            // })->sum('basic');

            // $update[$key]['id'] = $data->id;
            if($data->double_peserta->where('tanggal_lahir',$data->tanggal_lahir)->count() > 1){
                $update[$key]['is_double'] = 1;
                $data->is_double = 1;
                $check =  Kepesertaan::where(['nama'=>$data->nama,'tanggal_lahir'=>$data->tanggal_lahir])->where(function($table){
                    $table->where('status_polis','Inforce')->orWhere('status_polis','Akseptasi');
                })->sum('basic');
                $data->akumulasi_ganda = $check+$data->basic;
                $update[$key]['akumulasi_ganda'] = $check+$data->basic;
            }else{
                $data->is_double = 0;
                $update[$key]['is_double']=0;
            }

            $nilai_manfaat_asuransi = $data->basic;
            if($data->basic=="" || $data->basic==0) continue;

            $update[$key]['usia'] =  $data->tanggal_lahir ? hitung_umur($data->tanggal_lahir,$this->perhitungan_usia,$data->tanggal_mulai) : '0';
            $update[$key]['masa'] = hitung_masa($data->tanggal_mulai,$data->tanggal_akhir);
            $update[$key]['masa_bulan'] =  hitung_masa_bulan($data->tanggal_mulai,$data->tanggal_akhir,$this->masa_asuransi);

            $data->usia =  $data->tanggal_lahir ? hitung_umur($data->tanggal_lahir,$this->perhitungan_usia,$data->tanggal_mulai) : '0';
            $data->masa = hitung_masa($data->tanggal_mulai,$data->tanggal_akhir);
            $data->masa_bulan = hitung_masa_bulan($data->tanggal_mulai,$data->tanggal_akhir,$this->masa_asuransi);
            // find rate
            $rate = Rate::where(['tahun'=>$data->usia,'bulan'=>$data->masa_bulan,'polis_id'=>$this->polis_id])->first();
            
            if(!$rate || $rate->rate ==0 || $rate->rate ==""){
                $data->rate = 0;
                $data->kontribusi = 0;
            }else{
                $update[$key]['rate'] = $rate ? $rate->rate : 0;
                $update[$key]['kontribusi'] = $update[$key]['rate']==0 ? 0 : ($nilai_manfaat_asuransi * $data->rate/1000);
                $data->rate =  $rate ? $rate->rate : 0;
                $data->kontribusi = $nilai_manfaat_asuransi * $data->rate/1000;
            }

            $update[$key]['dana_tabarru'] = ($data->kontribusi*$iuran_tabbaru)/100; // persen ngambil dari daftarin polis
            $update[$key]['dana_ujrah'] = ($data->kontribusi*$ujrah)/100;
            $update[$key]['extra_mortalita'] = $data->rate_em*$nilai_manfaat_asuransi/1000;

            $data->dana_tabarru =  ($data->kontribusi*$iuran_tabbaru)/100;
            $data->dana_ujrah = ($data->kontribusi*$ujrah)/100;
            $data->extra_mortalita = $data->rate_em*$nilai_manfaat_asuransi/1000;

            if($data->akumulasi_ganda)
                $uw = UnderwritingLimit::whereRaw("{$data->akumulasi_ganda} BETWEEN min_amount and max_amount")->where(['usia'=>$data->usia,'polis_id'=>$this->polis_id])->first();
            else
                $uw = UnderwritingLimit::whereRaw("{$nilai_manfaat_asuransi} BETWEEN min_amount and max_amount")->where(['usia'=>$data->usia,'polis_id'=>$this->polis_id])->first();

            if($uw){
                $update[$key]['uw'] = $uw->keterangan;
                $update[$key]['ul'] = $uw->keterangan;
                $data->uw = $uw->keterangan;
                $data->ul = $uw->keterangan;
            }else{
                $data->uw = '>Max UA';
                $data->ul = '>Max UA';
            }

            $update[$key]['is_hitung'] = 1;
            $data->is_hitung = 1;
            $data->save();

            if($data->tinggi_badan and $data->berat_badan and $data->usia){
                $tinggi_badan = $data->tinggi_badan;
                $tinggi_badan  = $tinggi_badan / 100;
                $tinggi_badan  = $tinggi_badan * $tinggi_badan;
                $berat_badan   = $data->berat_badan;
    
                $data->kb = "";
                $bmi_ = $berat_badan / $tinggi_badan;
                $data->bmi = toFixed($bmi_,2);
    
                if(inRange($data->bmi,0,18.49)){
                    $data->kb = 'Underweight';
                }
                if(inRange($data->bmi,18.5, 24.99)){
                    $data->kb = 'ideal';
                }
                if(inRange($data->bmi,25, 29.99)){
                    $data->kb = 'Overweight';
                }
                if(inRange($data->bmi,30, 34.99)){
                    $data->kb = 'Ob. Kelas1';
                }
                if(inRange($data->bmi,35, 39.99)){
                    $data->kb = 'Ob. Kelas2';
                }
                if(inRange($data->bmi,40, 99)){
                    $data->kb = 'Ob. Kelas3';
                }
                $data->save();
            }

            // Kepesertaan::find($data->id)->update($update);
            $key++;
        }
        // \Batch::update(new Kepesertaan,$update,'id');
        event(new RequestPengajuan('Data berhasil dikalkukasi',$polis->id,$this->transaction_id));
    }
}
