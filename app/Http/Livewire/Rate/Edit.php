<?php

namespace App\Http\Livewire\Rate;

use Livewire\Component;
use App\Models\Rate;

class Edit extends Component
{
    public $rate,$tahun,$is_edit=false,$bulan;
    protected $listeners = ['set_id'];
    public function render()
    {
        return view('livewire.rate.edit');
    }

    public function set_id($data)
    {
        $this->tahun = $data['tahun'];
        $this->bulan = $data['bulan'];
        $this->rate = $data['rate'];
    }

    public function save()
    {
        $this->validate([
            'rate'=>'required'
        ]);

        $data = Rate::where(['tahun'=>$this->tahun,'bulan'=>$this->bulan])->first();
        $data->rate = $this->rate;
        $data->save();
        
        $this->emit('modal','hide');
        $this->emit('reload-page');
    }
}
