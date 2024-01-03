<?php

namespace App\Http\Livewire\Klaim;

use Livewire\Component;
use App\Models\KlaimOrgan;

class PengaturanOrgan extends Component
{
    public $insert=false,$name,$data=[];
    public function render()
    {
        $this->data = KlaimOrgan::get();

        return view('livewire.klaim.pengaturan-organ');
    }

    public function delete($id)
    {
        KlaimOrgan::find($id)->delete(); $this->render();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required'
        ]);

        $data = new KlaimOrgan();
        $data->name = $this->name;
        $data->save();

        $this->reset('name');
        $this->insert = false;
        $this->render();
    }
}
