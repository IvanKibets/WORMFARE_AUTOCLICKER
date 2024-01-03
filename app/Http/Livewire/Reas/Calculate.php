<?php

namespace App\Http\Livewire\Reas;

use Livewire\Component;
use App\Models\Reas;
use App\Models\Kepesertaan;
use Livewire\WithPagination;

class Calculate extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap',$listeners = ['reassign'=>'set_reassign','filter-ul'=>'filter_ul','filter-peserta'=>'filter_peserta_'];
    public $check_id=[],$assign_id=[],$data,$extra_kontribusi,$reassign=false,$ul,$filter_peserta;
    public function render()
    {
        $kepesertaan = Kepesertaan::with(['pengajuan','polis'])->where('reas_id',$this->data->id)->where('status_reas',1);

        if($this->ul) $kepesertaan->where('ul_reas',$this->ul);
        if($this->filter_peserta) $kepesertaan->where('is_double_reas',$this->filter_peserta);

        return view('livewire.reas.calculate')->with(['kepesertaan'=>$kepesertaan->get()]);
    }

    public function updated()
    {
        $this->emit('data_assign_reas_',$this->assign_id);
    }

    public function filter_peserta_($filter_peserta)
    {
        $this->filter_peserta = $filter_peserta;
    }

    public function filter_ul($ul)
    {
        $this->ul = $ul;
    }

    public function mount(Reas $data)
    {
        $this->data = $data;
    }

    public function set_reassign($bol)
    {
        $this->reassign = $bol;
    }
}
