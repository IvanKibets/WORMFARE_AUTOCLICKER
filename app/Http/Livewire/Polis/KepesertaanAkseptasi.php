<?php

namespace App\Http\Livewire\Polis;

use Livewire\Component;
use App\Models\Polis;
use App\Models\Kepesertaan as ModelKepesertaan;
use App\Models\UnderwritingLimit;
use App\Models\Rate;

class KepesertaanAkseptasi extends Component
{
    public $data,$keyword,$check_all=0,$check_id=[],$check_arr;
    protected $listeners = ['reload-page'=>'$refresh'];
    public function render()
    {
        $data = ModelKepesertaan::where('polis_id',$this->data->id)->orderBy('id','ASC');

        if($this->keyword) $data = $data->where(function($table){
            foreach(\Illuminate\Support\Facades\Schema::getColumnListing('kepesertaan') as $column){
                $table->orWhere('kepesertaan.'.$column,'LIKE',"%{$this->keyword}%");
            }
        });

        $kepesertaan_approve = clone $data;
        $kepesertaan_reject = clone $data;
        $kepesertaan_postpone = clone $data;
        
        $this->check_arr = clone $data->get();

        return view('livewire.polis.kepesertaan-akseptasi')->with(['kepesertaan_approve'=>$kepesertaan_approve->where('status_akseptasi',1)->paginate(100),'kepesertaan_reject'=>$kepesertaan_reject->where('status_akseptasi',2)->paginate(100),'kepesertaan_postpone'=>$kepesertaan_postpone->where('status_akseptasi',0)->paginate(100)]);
    }

    public function updated($propertyName)
    {
        if($propertyName=='check_all' and $this->check_all==1){
            foreach($this->check_arr as $k => $item){
                $this->check_id[$k] = $item->id;
            }
        }elseif($propertyName=='check_all' and $this->check_all==0){
            $this->check_id = [];
        }
    }

    public function mount(Polis $data)
    {
        $this->data = $data;
    }

    public function approve(ModelKepesertaan $data)
    {
        $data->status_akseptasi = 1;
        $data->save();

        $this->emit('reload');
    }

    public function reject(ModelKepesertaan $data)
    {
        $data->status_akseptasi = 2;
        $data->save();

        $this->emit('reload');
    }

    public function reject_selected()
    {
        $this->validate([
            'note' => 'required'
        ]);

        
    }
}
