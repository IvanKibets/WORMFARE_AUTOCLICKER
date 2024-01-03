<?php

namespace App\Http\Livewire\Reasuradur;

use Livewire\Component;
use App\Models\ReasuradurRate;

class EditRate extends Component
{
    public $data;
    public $reasuradur_id,$nama,$or,$reas,$ri_com,$model_reas,$max_or;
    protected $listeners = ['edit-rate'=>'edit'];
    public function render()
    {
        return view('livewire.reasuradur.edit-rate');
    }

    public function updated($propertyName)
    {
        if($this->or>0) $this->reas = 100 - $this->or;
        if($this->or==0 || $this->or =="") $this->reas = 100;
    }

    public function edit(ReasuradurRate $data)
    {
        $this->data = $data;
        $this->reasuradur_id =  $data->reasuradur_id;
        $this->nama =  $data->nama;
        $this->or =  $data->or;
        $this->reas =  $data->reas;
        $this->ri_com =  $data->ri_com;
        $this->model_reas = $data->model_reas;
        $this->max_or = $data->max_or;
    }

    public function save()
    {
        $this->data->nama = $this->nama;
        $this->data->reasuradur_id = $this->reasuradur_id;
        $this->data->or = $this->or;
        $this->data->reas = $this->reas;
        $this->data->ri_com = $this->ri_com;
        $this->data->model_reas = $this->model_reas;
        $this->data->max_or = $this->max_or;
        $this->data->save();

        $this->emit('modal','hide');
        $this->emit('reload-rate');
    }
}
