<?php

namespace App\Http\Livewire\Produk;

use Livewire\Component;
use App\Models\Produk;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['reload-page'=>'$refresh'];

    public function render()
    {
        $data = Produk::orderBy('id','DESC');

        return view('livewire.produk.index')->with(['data'=>$data->paginate(100)]);
    }

    public function delete(Produk $data)
    {
        $data->delete();
        $this->emit('message-success','Data berhasil dihapus.');
        $this->emit('reload');
    }
}
