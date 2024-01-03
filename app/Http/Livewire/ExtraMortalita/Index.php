<?php

namespace App\Http\Livewire\ExtraMortalita;

use App\Models\ExtraMortalita;
use App\Models\ExtraMortalitaRate;
use Livewire\Component;

class Index extends Component
{
    public $extra_mortalita_id;
    protected $listeners = ['reload-page'=>'$refresh'];
    public function render()
    {
        $data = ExtraMortalita::get();
        
        $raw_data = [];$tahun = [];$usia=  [];
        if($this->extra_mortalita_id){
            foreach(ExtraMortalitaRate::where('extra_mortalita_id',$this->extra_mortalita_id)->get() as $item){
                $raw_data[$item->tahun][$item->usia] = $item->rate;
            }
            $tahun = ExtraMortalitaRate::where('extra_mortalita_id',$this->extra_mortalita_id)->groupBy('tahun')->get();
            $usia = ExtraMortalitaRate::where('extra_mortalita_id',$this->extra_mortalita_id)->groupBy('usia')->get();
        }
        return view('livewire.extra-mortalita.index')->with(['data'=>$data,'raw_data'=>$raw_data,'row_tahun'=>$tahun,'row_usia'=>$usia]);
    }
}