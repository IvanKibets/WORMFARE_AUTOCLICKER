<?php

namespace App\Http\Livewire\Pengajuan;

use Livewire\Component;
use App\Models\Kepesertaan;

class ShowDouble extends Component
{
    public $data=[];
    protected $listeners = ['set_id'=>'set_id','modal_show_double'=>'set_id'];
    public function render()
    {
        return view('livewire.pengajuan.show-double');
    }

    public function set_id(Kepesertaan $data)
    {
        $this->data = Kepesertaan::where(['tanggal_lahir'=>$data->tanggal_lahir,'nama'=>$data->nama])->where(function($table){
            $table->where('status_polis','Inforce')->orWhere('status_polis','Akseptasi');
        })->get();
    }

}
