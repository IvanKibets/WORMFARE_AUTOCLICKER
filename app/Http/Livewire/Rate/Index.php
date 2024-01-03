<?php

namespace App\Http\Livewire\Rate;

use Livewire\Component;
use App\Models\Rate;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['reload-page'=>'$refresh'];
    public function render()
    {
        $data = Rate::groupBy('tahun')->get();
        $get_bulan = Rate::groupBy('bulan')->get();

        $raw_data = [];
        foreach(Rate::get() as $item){
            $raw_data[$item->tahun][$item->bulan] = $item->rate;
        }

        return view('livewire.rate.index')->with(['data'=>$data,'get_bulan'=>$get_bulan,'raw_data'=>$raw_data]);
    }
}
