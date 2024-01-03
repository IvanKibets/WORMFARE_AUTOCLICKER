<?php

namespace App\Http\Livewire\Produk;

use Livewire\Component;
use App\Models\Produk;

class Edit extends Component
{
    protected $listeners = ['set-id'=>'set_id'];
    public $data,$singkatan,$nama,$klasifikasi,$kode,$running_number;

    public function render()
    {
        return view('livewire.produk.edit');
    }

    public function set_id(Produk $id)
    {
        $this->data = $id;
        $this->singkatan = $id->singkatan;
        $this->nama = $id->nama;
        $this->klasifikasi = $id->klasifikasi;
        $this->kode = $id->kode;
        $this->running_number = $id->running_number;
    }
    public function save()
    {
        $this->validate([
            'singkatan'=>'required',
            'nama'=>'required',
            'kode' => 'required',
            'klasifikasi'=>'required',
            'running_number'=>'required'
        ]);

        $data = $this->data;;
        $data->singkatan = $this->singkatan;
        $data->nama = $this->nama;
        $data->kode = $this->kode;
        $data->klasifikasi = $this->klasifikasi;
        $data->running_number = $this->running_number;
        $data->save();
        
        $this->emit('modal','hide');
        $this->emit('reload-page');
    }
}
