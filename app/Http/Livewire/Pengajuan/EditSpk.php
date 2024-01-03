<?php

namespace App\Http\Livewire\Pengajuan;

use Livewire\Component;
use App\Models\Kepesertaan;
use Livewire\WithFileUploads;

class EditSpk extends Component
{
    use WithFileUploads;
    public $data,$file_spk;
    protected $listeners = ['set_id'];
    public function render()
    {
        return view('livewire.pengajuan.edit-spk');
    }

    public function set_id(Kepesertaan $data)
    {
        $this->data = $data;
    }

    public function save()
    {
        $this->validate([
            'file_spk' => 'mimes:xlsx,pdf,xlx,jpeg,jpg,png|max:51200'
        ]);

        if($this->file_spk){
            $name = $this->data->id .".".$this->file_spk->extension();
            $this->file_spk->storeAs("public/pengajuan/", $name);
            $this->data->file_spk = "storage/pengajuan/{$name}";
            $this->data->save();
        }

        $this->emit('modal','hide');
        $this->emit('reload-page');
        $this->emit('message-success','File SPK  berhasil disimpan');
    }
}
