<?php

namespace App\Http\Livewire\Polis;

use App\Models\ExtraMortalita;
use App\Models\ExtraMortalitaRate;
use App\Models\Kepesertaan;
use App\Models\Rate;
use App\Models\UnderwritingLimit;
use Livewire\Component;

class AddEm extends Component
{
    public $status,$data,$extra_mortalita_id,$rate;
    protected $listeners = ['set_id'];
    public function render()
    {
        return view('livewire.polis.add-em');
    }

    public function mount()
    {
        $this->rate = ExtraMortalita::get();
    }

    public function set_id(Kepesertaan $data)
    {
        $this->data = $data;
        $this->status = $data->status_em;
    }

    public function save()
    {
        // find reate
        $tahun = round($this->data->masa_bulan / 12);

        if($this->data->masa_bulan>12 and $this->data->masa_bulan<24)
            $rate = ExtraMortalitaRate::where(['extra_mortalita_id'=>$this->extra_mortalita_id,'usia'=>$this->data->usia,'tahun'=>2])->first();
        else
            $rate = ExtraMortalitaRate::where(['extra_mortalita_id'=>$this->extra_mortalita_id,'usia'=>$this->data->usia,'tahun'=>$tahun])->first();
        
        if($rate){
            $this->data->rate_em = $rate->rate;
            $this->data->extra_mortalita = $this->data->basic * $rate->rate / 1000;
            $this->data->use_em = 1;
        }
        if($this->extra_mortalita_id=="" and $this->status==""){
            $this->data->rate_em = null;
            $this->data->extra_mortalita = 0;
            $this->data->use_em = 0;
        }
        $this->data->status_em = $this->status;
        $this->data->nomor_em = str_pad($this->data->id,6, '0', STR_PAD_LEFT).'/EM-UWS/AJRIUS/'.numberToRomawi(date('m')).'/'.date('Y');;
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
        
        if($this->extra_mortalita_id=="" and $this->status=="")
            $data->extra_mortalita = 0;
        else
            $data->extra_mortalita = $data->rate_em*$nilai_manfaat_asuransi/1000;
        
        if($data->akumulasi_ganda)
            $uw = UnderwritingLimit::whereRaw("{$data->s} BETWEEN min_amount and max_amount")->where(['usia'=>$data->usia,'polis_id'=>$data->polis_id])->first();
        else
            $uw = UnderwritingLimit::whereRaw("{$nilai_manfaat_asuransi} BETWEEN min_amount and max_amount")->where(['usia'=>$data->usia,'polis_id'=>$data->polis_id])->first();

        if(!$uw) $uw = UnderwritingLimit::where(['usia'=>$data->usia,'polis_id'=>$data->polis_id])->orderBy('max_amount','ASC')->first();
        if($uw) {
            $data->uw = $uw->keterangan;
            $data->ul = $uw->keterangan;
        }
        $data->save();

        $this->emit('modal','hide');
        $this->emit('reload-page');
        $this->emit('message-success','Extra Mortalita berhasil disimpan');
    }
}
