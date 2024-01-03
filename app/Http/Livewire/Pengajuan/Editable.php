<?php

namespace App\Http\Livewire\Pengajuan;

use Livewire\Component;
use App\Models\Kepesertaan;

class Editable extends Component
{
    protected $listeners = ['set_id'];
    public $field,$value;
    public function render()
    {
        return view('livewire.pengajuan.editable');
    }

    public function set_id($data)
    {
        if(is_array($data)){
            $field = isset($data['field']) ? $data['field'] : '';
            $this->data = Kepesertaan::find($data['id']);
            if(isset($field)){
                $this->value = $this->data->$field;
                $this->field = $field;
            }
        }
    }

    public function save()
    {
        $field = $this->field;
        $this->data->$field = $this->value;
        $this->data->save();

        $this->emit('reload-page');
        $this->emit('modal','hide');
    }
}
