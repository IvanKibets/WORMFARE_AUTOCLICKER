<?php

namespace App\Http\Livewire\Reas;

use Livewire\Component;
use App\Models\Kepesertaan;

class ViewDouble extends Component
{
    public $data=[];
    protected $listeners = ['set_id_reas'=>'set_id'];
    public function render()
    {
        return view('livewire.reas.view-double');
    }

    public function set_id(Kepesertaan $data)
    {
        $this->data = Kepesertaan::where(['nama'=>$data->nama,'tanggal_lahir'=>$data->tanggal_lahir,'polis_id'=>$data->polis_id,'status_polis'=>'Inforce'])->orderBy('id','ASC')->get();;
    }
}
