<?php

namespace App\Http\Livewire\Reasuradur;

use Livewire\Component;
use App\Models\ReasuradurRateRates;
use Livewire\WithFileUploads;

class RateRates extends Component
{
    use WithFileUploads;
    public $file,$reasuradur_rate_id;
    protected $listeners = ['set_id_rate'=>'set_id'];
    public function render()
    {
        return view('livewire.reasuradur.rate-rates');
    }

    public function set_id($id)
    {
        $this->reasuradur_rate_id = $id;
    }

    public function save()
    {
        $this->validate([
            'file'=>'required|mimes:xlsx|max:51200', // 50MB maksimal
        ]);

        $path = $this->file->getRealPath();
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(true);
        $xlsx = $reader->load($path);
        $sheetDataRate = $xlsx->getActiveSheet()->toArray();
        $num=0;
        $insert = [];
        ReasuradurRateRates::where('reasuradur_rate_id',$this->reasuradur_rate_id)->delete();
        foreach($sheetDataRate as $key => $item){
            if($key<=1) continue;
            for($i=1;$i<=300;$i++){
                if(!isset($item[$i])) continue;
                $insert[$num]['tahun'] = $item[0];
                $insert[$num]['rate'] = $item[$i];
                $insert[$num]['bulan'] = $i;
                $insert[$num]['reasuradur_rate_id'] = $this->reasuradur_rate_id;
                $insert[$num]['created_at'] = date('Y-m-d H:i:s');
                $insert[$num]['updated_at'] = date('Y-m-d H:i:s');
                $num++;
                if($num==1000){ // insert maksimal per 1000
                    ReasuradurRateRates::insert($insert);
                    $num=0;
                    $insert = [];
                }
            }
        }

        /**
         * // it's the same instance.
         * https://laracasts.com/discuss/channels/laravel/too-many-placeholders
            DB::connection()->getPdo() === (new App\Models\Call)->getConnection()->getPdo(); // true

            // set TRUE;
            DB::connection()->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, true);

            App\Models\Call::insert($data);

            // set FALSE
            DB::connection()->getPdo()->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
         */
        ReasuradurRateRates::insert($insert);

        $this->emit('modal','hide');
        $this->emit('reload-page');
    }
}
