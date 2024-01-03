<?php

namespace App\Jobs;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldBeUnique;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use App\Models\Kepesertaan;
use App\Models\Reas;
use App\Models\ReasuradurRate;
use App\Models\ReasuradurRateUw;
use App\Models\ReasuradurRateRates;
use App\Events\RequestReas;

class ReasCalculate implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * The object reas
     *
     * @var object
     */
    public $data;


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
    public function __construct($reas_id)
    {
        $this->data = Reas::find($reas_id);
    }

    /**
     * Execute the job.
     *
     * @return void
     */
    public function handle()
    {
        ini_set('memory_limit', '-1');
        $kepesertaan = Kepesertaan::with(['pengajuan','polis'])->where(['reas_id'=>$this->data->id,'status_akseptasi'=>1])->get();
        
        Kepesertaan::where('reas_id',$this->data->id)->update(['akumulasi_ganda_reas'=>0,'reas_manfaat_asuransi_ajri'=>0,'nilai_manfaat_asuransi_reas'=>0]);

        $rate = ReasuradurRate::find($this->data->reasuradur_rate_id);

        if($rate){
            $this->data->reas = $rate->reas;
            $this->data->or = $rate->reas;
            $this->data->ri_com = $rate->ri_com;
            $this->data->save();
        }

        $or = $this->data->reas;
        $ajri = $this->data->or;
        $ri_com = $this->data->ri_com;
        $perhitungan_usia = $this->data->perhitungan_usia;

        echo "\n\nOR : {$or}\n";
        echo "AJRI : {$ajri}\n";
        echo "RI COM :{$ri_com}\n\n";

        foreach($kepesertaan as $k => $item){
            $manfaat_asuransi = $item->basic;

            // echo "{$k}. Nama : {$item->nama}\n";
            $item->usia_reas = $item->tanggal_lahir ? hitung_umur($item->tanggal_lahir,$perhitungan_usia,$item->tanggal_mulai) : '0';
            // check double
            $check_double = Kepesertaan::where(['tanggal_lahir'=>$item->tanggal_lahir,'nama'=>$item->nama,'polis_id'=>$item->polis_id,'status_polis'=>'Inforce'])->whereNotNull('reas_id');
            $count = clone $check_double;

            $is_double = false;
            if($count->get()->count() >= 2){
                $check_double = $check_double->first();
                if(isset($check_double->reas->reasuradur_id) and isset($this->data->reasuradur_id)){
                    if($check_double->reas->reasuradur_id==$this->data->reasuradur_id){
                        $is_double = true;
                        $item->is_double_reas = 1;
                        $item->akumulasi_ganda_reas = Kepesertaan::where(['tanggal_lahir'=>$item->tanggal_lahir,'nama'=>$item->nama,'polis_id'=>$item->polis_id,'status_polis'=>'Inforce'])
                                                                        ->leftJoin('reas','reas.id','=','kepesertaan.reas_id')
                                                                        ->where('reas.reasuradur_id',$this->data->reasuradur_id)
                                                                        ->get()->sum('reas_manfaat_asuransi_ajri');
                    }
                }
            }else{
                $item->is_double_reas = 0;
                $item->akumulasi_ganda_reas = 0;
            }

            $reas_manfaat_asuransi_ajri = ($manfaat_asuransi*$ajri)/100;

            if($item->is_double_reas==1){
                if($item->akumulasi_ganda_reas>=100000000){
                    $item->nilai_manfaat_asuransi_reas = $manfaat_asuransi;
                    $item->reas_manfaat_asuransi_ajri = 0;
                }else{
                    $akumulasi_reas = $item->akumulasi_ganda_reas + $reas_manfaat_asuransi_ajri;

                    if($akumulasi_reas > 100000000){
                        $sisa_akumulasi_ajri = 100000000 - $item->akumulasi_ganda_reas;
                        $sisa_akumulasi_reas = $manfaat_asuransi - $sisa_akumulasi_ajri;
                    }else{
                        $sisa_akumulasi_reas = ($manfaat_asuransi*$or)/100;
                        $sisa_akumulasi_ajri = ($manfaat_asuransi*$ajri)/100;
                    }
                    $item->nilai_manfaat_asuransi_reas = $sisa_akumulasi_reas;
                    $item->reas_manfaat_asuransi_ajri = $sisa_akumulasi_ajri;
                }
            }else{
                if($reas_manfaat_asuransi_ajri>=100000000){
                    $item->nilai_manfaat_asuransi_reas = $manfaat_asuransi - 100000000;
                    $item->reas_manfaat_asuransi_ajri = 100000000;
                }else{
                    $item->nilai_manfaat_asuransi_reas = ($manfaat_asuransi*$or)/100;
                    $item->reas_manfaat_asuransi_ajri = ($manfaat_asuransi*$ajri)/100;
                }
            }

            // kontribusi reas
            $rate = ReasuradurRateRates::where(['tahun'=>$item->usia_reas,'bulan'=>$item->masa_bulan,'reasuradur_rate_id'=>$this->data->reasuradur_rate_id])->first();
            if($rate){
                $item->rate_reas = $rate->rate;
                $item->total_kontribusi_reas = ($rate->rate*$item->nilai_manfaat_asuransi_reas)/1000;
            }else {
                $item->rate_reas = 0;
                $item->total_kontribusi_reas = 0;
            }

            if($ri_com)
                $item->ujroh_reas = ($item->total_kontribusi_reas * $ri_com) / 100;
            else
                $item->ujroh_reas = 0;

            // ul
            $uw = ReasuradurRateUw::whereRaw("{$manfaat_asuransi} BETWEEN min_amount and max_amount")->where(['usia'=>$item->usia_reas,'reasuradur_rate_id'=>$this->data->reasuradur_rate_id])->first();
            if(!$uw) $uw = ReasuradurRateUw::where(['usia'=>$item->usia_reas,'reasuradur_rate_id'=>$this->data->reasuradur_rate_id])->orderBy('max_amount','ASC')->first();
            if($uw) $item->ul_reas = $uw->keterangan;

            $item->net_kontribusi_reas = $item->total_kontribusi_reas + $item->reas_extra_kontribusi - $item->ujroh_reas;

            if($item->total_kontribusi_reas<=0){
                $item->nilai_manfaat_asuransi_reas = 0;
                $item->reas_manfaat_asuransi_ajri = $item->basic;
                $item->status_reas = 2; // tidak direaskan karna distribusinya 0
            }else{
                $item->status_reas = 1;
            }

            $item->kadaluarsa_reas_tanggal =  date('Y-m-d',strtotime($this->data->created_at." +{$item->polis->kadaluarsa_reas} days"));
            $item->kadaluarsa_reas_hari =  $item->polis->kadaluarsa_reas;
            $item->save();
            // echo "Net Kontribusi : {$item->net_kontribusi_reas}\n\n";
        }

        $this->data->jumlah_peserta = Kepesertaan::where(['reas_id'=>$this->data->id,'status_reas'=>1])->count();
        $this->data->extra_kontribusi = Kepesertaan::where(['reas_id'=>$this->data->id,'status_reas'=>1])->sum('reas_extra_kontribusi');
        $this->data->manfaat_asuransi_reas = Kepesertaan::where(['reas_id'=>$this->data->id,'status_reas'=>1])->sum('nilai_manfaat_asuransi_reas');
        $this->data->manfaat_asuransi_ajri = Kepesertaan::where(['reas_id'=>$this->data->id,'status_reas'=>1])->sum('reas_manfaat_asuransi_ajri');
        $this->data->kontribusi = Kepesertaan::where(['reas_id'=>$this->data->id,'status_reas'=>1])->sum('total_kontribusi_reas');
        $this->data->ujroh = Kepesertaan::where(['reas_id'=>$this->data->id,'status_reas'=>1])->sum('ujroh_reas');
        $this->data->kontribusi_netto = Kepesertaan::where(['reas_id'=>$this->data->id,'status_reas'=>1])->sum('net_kontribusi_reas');
        $this->data->save();

        event(new RequestReas('Data berhasil dikalkukasi',$this->data->id));
        echo "Running Event \n\n";
    }
}
