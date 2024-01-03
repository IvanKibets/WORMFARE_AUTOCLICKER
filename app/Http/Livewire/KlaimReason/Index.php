<?php

namespace App\Http\Livewire\KlaimReason;

use Livewire\Component;

class Index extends Component
{
    public $insert=false;
    public function render()
    {
        return view('livewire.klaim-reason.index');
    }
}
