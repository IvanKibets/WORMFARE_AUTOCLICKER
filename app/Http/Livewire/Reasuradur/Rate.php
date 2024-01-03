<?php

namespace App\Http\Livewire\Reasuradur;

use Livewire\Component;
use App\Models\ReasuradurRate;
use App\Models\ReasuradurRateUw;
use App\Models\ReasuradurRateRates;

class Rate extends Component
{
    protected $listeners = ['reload-rate'=>'$refresh'];
    public function render()
    {
        $data = ReasuradurRate::withCount('rate')
                                ->withCount('uw_limit')
                                ->orderBy('id','DESC');

        return view('livewire.reasuradur.rate')->with(['data'=>$data->paginate(50)]);
    }

    public function delete($id)
    {
        $data = ReasuradurRate::find($id);
        $data->delete();

        ReasuradurRateUw::where('reasuradur_rate_id',$id)->delete();
        ReasuradurRateRates::where('reasuradur_rate_id',$id)->delete();
    }

    public function set_rates($id)
    {
        $this->emit('show_rate_rates');
        $this->emit('set_id_rate',$id);
    }

    public function set_uw_limit($id)
    {
        $this->emit('show_rate_uw');
        $this->emit('set_id_uw',$id);
    }
}
