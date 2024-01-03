<?php

namespace App\Http\Livewire\Room;

use Livewire\Component;
use App\Models\Room;
class Index extends Component
{
    public $data=[],$is_insert=false,$name,$is_delete,$selected_id;
    protected $listeners = ['set_id','reload'=>'$refresh'];
    public function render()
    {
        return view('livewire.room.index');
    }

    public function mount()
    {
        $this->data = Room::orderBy('name','ASC')->get();
    }

    public function set_delete($id){
        $this->selected_id=$id;$this->is_delete=true;
    }

    public function delete()
    {
        Room::find($this->selected_id)->delete();
        $this->selected_id='';$this->is_delete=false;$this->emit('reload');
    }

    public function save()
    {
        $this->validate([
            'name'=>'required'
        ]);

        Room::create([
            'name'=>$this->name
        ]);
        $this->name = '';$this->is_insert=false;$this->emit('reload');
    }
}
