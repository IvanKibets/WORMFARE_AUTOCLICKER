<?php

namespace App\Http\Livewire\Reasuradur;

use App\Models\Reasuradur;
use Livewire\Component;

class Index extends Component
{
    protected $listeners = ['reload-page'=>'$refresh'];
    public $insert = false;
    public function render()
    {
        $data = Reasuradur::get();

        return view('livewire.reasuradur.index')->with(['data'=>$data]);
    }
}