<?php

namespace App\Http\Livewire\Rate;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Rate;
use App\Models\Polis;

class Upload extends Component
{
    use WithFileUploads;
    public $file,$polis_id,$data,$get_bulan,$raw_data;
    protected $listeners = ['set_id'];
    public function render()
    {
        return view('livewire.rate.upload');
    }

    public function set_id($id)
    {
        $this->polis_id = $id;

        $data = Rate::where('polis_id',$this->polis_id)->groupBy('tahun')->get();
        $get_bulan = Rate::where('polis_id',$this->polis_id)->groupBy('bulan')->get();

        $raw_data = [];
        foreach(Rate::where('polis_id',$this->polis_id)->get() as $item){
            $raw_data[$item->tahun][$item->bulan] = $item->rate;
        }
        
        $this->raw_data = $raw_data;
        $this->data = $data;
        $this->get_bulan = $get_bulan;
    }

    public function save()
    {
        \LogActivity::add('[web] Upload Rate');
        $this->validate([
            'file'=>'required|mimes:xlsx|max:51200' // 50MB maksimal
        ]);
        
        $path = $this->file->getRealPath();
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(true);
        $data_ = $reader->load($path);
        $sheetData = $data_->getActiveSheet()->toArray();

        if(count($sheetData) > 0){
            $insert = [];
            $num = 0;

            Rate::where('polis_id',$this->polis_id)->delete();

            foreach($sheetData as $key => $item){
                if($key<=1) continue;
                
                for($i=1;$i<=300;$i++){
                    if(!isset($item[$i])) continue;
                    $insert[$num]['polis_id'] = $this->polis_id;
                    $insert[$num]['tahun'] = $item[0];
                    $insert[$num]['rate'] = $item[$i];
                    $insert[$num]['bulan'] = $i;

                    $num++;
                }
            }

            Rate::insert($insert);
        }

        $polis = Polis::find($this->polis_id);
        if($polis){
            $polis->is_rate = 1;
            $polis->save();
        }

        session()->flash('message-success',__('Data Rate berhasil di upload'));

        return redirect()->route('polis.index');
        
    }
}
