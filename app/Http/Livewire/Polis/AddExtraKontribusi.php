<?php

namespace App\Http\Livewire\Polis;

use Livewire\Component;
use App\Models\Kepesertaan;
use App\Models\Rate;
use App\Models\UnderwritingLimit;

class AddExtraKontribusi extends Component
{
    public $amount,$data,$ek_status;
    protected $listeners = ['set_id'];
    public function render()
    {
        return view('livewire.polis.add-extra-kontribusi');
    }

    public function set_id(Kepesertaan $data)
    {
        $this->data = $data;
    }

    public function save()
    {
        $dana_tabbaru = ($this->data->kontribusi*$this->data->polis->iuran_tabbaru)/100;
        if($this->amount == "")
            $extra_kontribusi = 0;
        else{
            $extra_kontribusi = ($this->data->kontribusi*$this->amount)/100;
            $this->data->dana_tabarru = $dana_tabbaru + $this->data->extra_mortalita + $this->amount;
        }

        $this->data->extra_kontribusi = $extra_kontribusi;
        $this->data->ek_status = $this->ek_status;
        $this->data->nomor_ek = str_pad($this->data->id,6, '0', STR_PAD_LEFT).'/EK-UWS/AJRIUS/'.numberToRomawi(date('m')).'/'.date('Y');
        $this->data->save();
        // mulai hitung ulang
        $data = $this->data;
        
        if($data->is_double){
            $sum =  Kepesertaan::where(['nama'=>$data->nama,'tanggal_lahir'=>$data->tanggal_lahir,'status_polis'=>'Inforce'])->sum('basic');
            $data->akumulasi_ganda = $sum+$data->basic;;
            $data->save();
        }
        
        $nilai_manfaat_asuransi = $data->basic;
        $rate = Rate::where(['tahun'=>$data->usia,'bulan'=>$data->masa_bulan,'polis_id'=>$data->polis_id])->first();
        $data->rate = $rate ? $rate->rate : 0;
        $data->kontribusi = $nilai_manfaat_asuransi * $data->rate/1000;
        
        $data->dana_tabarru = ($data->kontribusi*$data->polis->iuran_tabbaru)/100; // persen ngambil dari daftarin polis
        $data->dana_ujrah = ($data->kontribusi*$data->polis->ujrah_atas_pengelolaan)/100; 
        $data->extra_mortalita = $data->rate_em*$nilai_manfaat_asuransi/1000;
        
        if($data->akumulasi_ganda)
            $uw = UnderwritingLimit::whereRaw("{$data->akumulasi_ganda} BETWEEN min_amount and max_amount")->where(['usia'=>$data->usia,'polis_id'=>$data->polis_id])->first();
        else
            $uw = UnderwritingLimit::whereRaw("{$nilai_manfaat_asuransi} BETWEEN min_amount and max_amount")->where(['usia'=>$data->usia,'polis_id'=>$data->polis_id])->first();
        
        if(!$uw) $uw = UnderwritingLimit::where(['usia'=>$data->usia,'polis_id'=>$data->polis_id])->orderBy('max_amount','ASC')->first();
        if($uw){
            $data->uw = $uw->keterangan;
            $data->ul = $uw->keterangan;
        }
        $data->is_hitung = 1;
        $data->save();

        $this->emit('modal','hide');
        $this->emit('reload-page');
        $this->emit('message-success','Extra Kontribusi berhasil disimpan');
    }
}
