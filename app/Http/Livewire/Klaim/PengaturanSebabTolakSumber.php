<?php

namespace App\Http\Livewire\Klaim;

use Livewire\Component;
use App\Models\KlaimSebabTolakSumber;

class PengaturanSebabTolakSumber extends Component
{
    public $insert=false,$name,$data=[];
    public function render()
    {
        $this->data = KlaimSebabTolakSumber::get();

        return view('livewire.klaim.pengaturan-sebab-tolak-sumber');
    }

    public function delete($id)
    {
        KlaimSebabTolakSumber::find($id)->delete(); $this->render();
    }

    public function save()
    {
        $this->validate([
            'name' => 'required'
        ]);

        $data = new KlaimSebabTolakSumber();
        $data->name = $this->name;
        $data->save();

        $this->reset('name');
        $this->insert = false;
        $this->render();
    }
}
