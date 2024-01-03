<?php

namespace App\Http\Livewire\Klaim;

use Livewire\Component;
use App\Models\KlaimKategoriPenyakit;

class PengaturanKategoriPenyakit extends Component
{
    public $insert=false,$name,$data=[];
    public function render()
    {
        $this->data = KlaimKategoriPenyakit::get();

        return view('livewire.klaim.pengaturan-kategori-penyakit');
    }

    public function delete($id)
    {
        KlaimKategoriPenyakit::find($id)->delete(); $this->render();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required'
        ]);

        $data = new KlaimKategoriPenyakit();
        $data->name = $this->name;
        $data->save();

        $this->reset('name');
        $this->insert = false;
        $this->render();
    }
}
