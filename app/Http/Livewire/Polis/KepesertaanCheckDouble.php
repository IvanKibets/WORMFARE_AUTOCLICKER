<?php

namespace App\Http\Livewire\Polis;

use Livewire\Component;
use App\Models\Kepesertaan;

class KepesertaanCheckDouble extends Component
{
    public $data,$keyword,$check_all=0,$check_id=[],$check_arr;
    protected $listeners = ['reload-page'=>'$refresh'];
    public function render()
    {
        $kepesertaan = Kepesertaan::where(['is_double'=>1,'polis_id'=>$this->data->id]);

        if($this->keyword) $kepesertaan->where(function($table){
            foreach(\Illuminate\Support\Facades\Schema::getColumnListing('kepesertaan') as $column){
                $table->orWhere('kepesertaan.'.$column,'LIKE',"%{$this->keyword}%");
            }
        });

        $this->check_arr = clone $kepesertaan->get();

        return view('livewire.polis.kepesertaan-check-double')->with(['kepesertaan'=>$kepesertaan->get()]);
    }

    public function mount($data)
    {
        $this->data = $data;
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

    public function keepAll()
    {
        foreach($this->check_arr as $k => $item){
            $data = Kepesertaan::find($item->id);
            $data->is_double = 0;
            $data->save();
        }    
        
        $this->emit('reload-page');
        $this->emit('message-success','Data berhasil diproses');
    }

    public function deleteAll()
    {
        foreach($this->check_arr as $k => $item){
            Kepesertaan::find($item->id)->delete();
        }
        $this->emit('reload-page');        
        $this->emit('message-success','Data berhasil diproses');
    }

    public function delete(Kepesertaan $data)
    {
        $data->delete();
    }

    public function keep(Kepesertaan $data)
    {
        $data->is_double = 0;;
        $data->save();
    }
}
