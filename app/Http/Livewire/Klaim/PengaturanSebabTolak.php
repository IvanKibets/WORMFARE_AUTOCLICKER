<?php

namespace App\Http\Livewire\Klaim;

use Livewire\Component;
use App\Models\KlaimSebabTolak;

class PengaturanSebabTolak extends Component
{
    public $insert=false,$name,$data=[];
    public function render()
    {
        $this->data = KlaimSebabTolak::get();

        return view('livewire.klaim.pengaturan-sebab-tolak');
    }

    public function delete($id)
    {
        KlaimSebabTolak::find($id)->delete(); $this->render();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required'
        ]);

        $data = new KlaimSebabTolak();
        $data->name = $this->name;
        $data->save();

        $this->reset('name');
        $this->insert = false;
        $this->render();
    }
}
