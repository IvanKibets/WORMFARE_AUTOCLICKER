<?php

namespace App\Http\Livewire\Pengajuan;

use Livewire\Component;

class ConfirmDelete extends Component
{
    public $confirm_delete=false,$selected_id;
    public function render()
    {
        return view('livewire.pengajuan.confirm-delete');
    }

    public function mount($id)
    {
        $this->selected_id = $id;
    }

    public function delete()
    {
        \App\Models\Kepesertaan::find($this->selected_id)->delete();
        $this->emit('reload-page');
        $this->confirm_delete = false;
    }
}
