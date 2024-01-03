<?php

namespace App\Http\Livewire\Klaim;

use Livewire\Component;
use App\Models\KlaimJenis;

class PengaturanJenisKlaim extends Component
{
    public $insert=false,$name,$data=[];
    public function render()
    {
        $this->data = KlaimJenis::get();

        return view('livewire.klaim.pengaturan-jenis-klaim');
    }

    public function delete($id)
    {
        KlaimJenis::find($id)->delete(); $this->render();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required'
        ]);

        $data = new KlaimJenis();
        $data->name = $this->name;
        $data->save();

        $this->reset('name');
        $this->insert = false;
        $this->render();
    }
}
