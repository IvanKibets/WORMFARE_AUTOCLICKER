<?php

namespace App\Http\Livewire\Reasuradur;

use Livewire\Component;
use App\Models\Reasuradur;

class Insert extends Component
{
    public $name;
    public function render()
    {
        return view('livewire.reasuradur.insert');
    }

    public function save()
    {
        $this->validate([
            'name' => 'required'
        ]);

        $data = new Reasuradur();
        $data->name = $this->name;
        $data->save();

        $this->reset(['name']);
        $this->emit('modal','hide');
        $this->emit('reload-page');
    }
}
