<?php

namespace App\Http\Livewire\Reasuradur;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ReasuradurRateUw;

class UwLimit extends Component
{
    use WithFileUploads;
    public $file,$reasuradur_rate_id;
    protected $listeners = ['set_id_uw'=>'set_id'];
    public function render()
    {
        return view('livewire.reasuradur.uw-limit');
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
        $sheetDataUwLimit = $xlsx->getActiveSheet()->toArray();
        
        if(count($sheetDataUwLimit) > 0){
            ReasuradurRateUw::where('reasuradur_rate_id',$this->reasuradur_rate_id)->delete();
            $data_header = [];
            foreach($sheetDataUwLimit as $key => $item){
                if($key==1){
                    for($i=2;$i<=400;$i++){
                        if(!isset($item[$i])) continue;
                        $data_header[] = $item[$i];
                    }
                }
            }

            $insert = [];
            $num=0;
            foreach($sheetDataUwLimit as $key => $item){
                if($key<=1) continue;
                foreach($data_header as $k_header => $val_header){
                    $insert[$num]['min_amount'] = $item[0];
                    $insert[$num]['max_amount'] = $item[1];
                    $insert[$num]['usia'] = $val_header;
                    $insert[$num]['keterangan'] =  $item[$k_header+2];
                    $insert[$num]['reasuradur_rate_id'] = $this->reasuradur_rate_id;
                    $insert[$num]['created_at'] = date('Y-m-d H:i:s');
                    $insert[$num]['updated_at'] = date('Y-m-d H:i:s');
                    $num++;
                }
            }
            ReasuradurRateUw::insert($insert);
        }

        $this->emit('modal','hide');
        $this->emit('reload-page');
    }
}
