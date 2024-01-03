<?php

namespace App\Http\Livewire\Reas;

use Livewire\Component;
use App\Models\Kepesertaan;
use App\Models\ReasuradurRateRates;
use App\Models\ReasuradurRateUw;

class AddExtraKontribusi extends Component
{
    protected $listeners = ['add-extra-kontribusi'=>'set_id'];
    public $amount,$data;
    public function render()
    {
        return view('livewire.reas.add-extra-kontribusi');
    }

    public function set_id(Kepesertaan $data)
    {
        $this->data = $data;
    }
    
    public function save()
    {
        $this->data->reas_extra_kontribusi = ($this->data->total_kontribusi_reas*$this->amount)/100;
        
        $or = $this->data->reas->or;
        $ajri = $this->data->reas->reas;

        $manfaat_asuransi = $this->data->basic;

        $reas_manfaat_asuransi_ajri = ($manfaat_asuransi*$ajri)/100;
        if($reas_manfaat_asuransi_ajri>=100000000){
            $reas_manfaat_asuransi_ajri = 100000000;
            $this->data->nilai_manfaat_asuransi_reas = $manfaat_asuransi - 100000000;
            $this->data->reas_manfaat_asuransi_ajri = $reas_manfaat_asuransi_ajri;
        }else{
            $this->data->nilai_manfaat_asuransi_reas = ($manfaat_asuransi*$or)/100;
            $this->data->reas_manfaat_asuransi_ajri = ($manfaat_asuransi*$ajri)/100;
        }
        // kontribusi reas
        $rate = ReasuradurRateRates::where(['tahun'=>$this->data->usia,'bulan'=>$this->data->masa_bulan,'reasuradur_rate_id'=>$this->data->reasuradur_rate_id])->first();
        if($rate) $this->data->total_kontribusi_reas = ($rate->rate*$this->data->nilai_manfaat_asuransi_reas)/100;
        
        if($this->data->total_kontribusi_reas<=0) $this->data->status = 2; // tidak direaskan karna distribusinya 0
        // ul
        $uw = ReasuradurRateUw::whereRaw("{$manfaat_asuransi} BETWEEN min_amount and max_amount")->where(['usia'=>$this->data->usia,'reasuradur_rate_id'=>$this->data->reasuradur_rate_id])->first();
        if(!$uw) $uw = ReasuradurRateUw::where(['usia'=>$this->data->usia,'reasuradur_rate_id'=>$this->data->reasuradur_rate_id])->orderBy('max_amount','ASC')->first();
        if($uw) $this->data->ul_reas = $uw->keterangan;

        $this->data->save();

        $this->emit('modal','hide');
        $this->emit('reload-page');
    }
}