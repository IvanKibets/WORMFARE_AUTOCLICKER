<?php

namespace App\Http\Livewire\Peserta;

use Livewire\Component;
use App\Models\Kepesertaan;

class Editable extends Component
{
    protected $listeners = ['set_id'];
    public $field,$value;
    public function render()
    {
        return view('livewire.peserta.editable');
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

        if($field == 'tanggal_mulai'){

            $earlier = new \DateTime($this->data->tanggal_mulai);
            $later = new \DateTime($this->data->tanggal_akhir);

            $total_hari = $later->diff($earlier)->format("%a");

            $this->data->tanggal_mulai = $this->value;
            $this->data->tanggal_akhir = date('Y-m-d',strtotime("{$this->value} + {$total_hari} days"));
        }else
            $this->data->$field = $this->value;
        

        $this->data->save();
        
        $this->emit('message-success',"Data berhasil disimpan");
        $this->emit('modal','hide');
        $this->emit('reload-page');
    }
}
