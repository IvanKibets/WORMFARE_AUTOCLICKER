<?php

namespace App\Http\Livewire\Rate;

use Livewire\Component;
use App\Models\Rate;

class Insert extends Component
{
    public $rate,$tahun,$bulan;
    public function render()
    {
        return view('livewire.rate.insert');
    }

    public function save()
    {
        $this->validate([
            'rate'=>'required',
            'tahun'=>'required',
            'bulan'=>'required',
        ]);

        $data = new Rate();
        $data->rate = $this->rate;
        $data->tahun = $this->tahun;
        $data->bulan = $this->bulan;
        $data->save();
        
        $this->emit('modal','hide');
        $this->emit('reload-page');
    }
}
