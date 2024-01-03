<?php

namespace App\Http\Livewire\RecoveryClaim;

use Livewire\Component;
use App\Models\RecoveryClaim;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $data = RecoveryClaim::with(['polis','kepesertaan','klaim'])->orderBy('id','DESC');

        return view('livewire.recovery-claim.index')->with(['data'=>$data->paginate(100)]);
    }
}
