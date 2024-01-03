<?php

namespace App\Http\Livewire\Reasuradur;

use Livewire\Component;
use App\Models\Reasuradur;

class Edit extends Component
{
    public $data,$name;
    protected $listeners = ['edit'=>'edit'];
    public function render()
    {
        return view('livewire.reasuradur.edit');
    }

    public function edit(Reasuradur $data)
    {
        $this->data = $data;
        $this->name =  $data->name;
    }

    public function save()
    {
        $this->data->name = $this->name;
        $this->data->save();

        $this->emit('modal','hide');
        $this->emit('reload-page');
    }
}
