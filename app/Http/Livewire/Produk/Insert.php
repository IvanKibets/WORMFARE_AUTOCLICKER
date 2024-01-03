<?php

namespace App\Http\Livewire\Produk;

use App\Models\ExtraMortalita;
use Livewire\Component;
use App\Models\Produk;
use App\Models\Rate;

class Insert extends Component
{
    public $singkatan,$nama,$klasifikasi,$kode,$running_number;
    public function render()
    {
        return view('livewire.produk.insert');
    }

    public function mount()
    {
        $this->extra_mortalita = ExtraMortalita::get();
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

        $data = new Produk();
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
