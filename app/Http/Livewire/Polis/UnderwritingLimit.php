<?php

namespace App\Http\Livewire\Polis;

use Livewire\Component;
use App\Models\Polis;
use App\Models\UnderwritingLimit as ModelUnderwritingLimit;
use App\Models\Rate;
use Livewire\WithFileUploads;

class UnderwritingLimit extends Component
{
    use WithFileUploads;
    public $data,$file,$nilai_bawah_atas,$usia,$tahun=[],$uw=[],$rows=[];
    protected $listeners = ['set_id','reload-page'=>'$refresh'];
    public function render()
    {
        return view('livewire.polis.underwriting-limit');
    }

    public function set_id(Polis $data)
    {
        $this->data = $data;
        
        $data = ModelUnderwritingLimit::where('polis_id',$this->data->id)->get();
        $rows = [];

        foreach($data as $k => $item){
            $rows[$item->usia][$item->min_amount][$item->max_amount] = $item->keterangan;
        }

        $this->rows = $rows;
        $this->uw = $data;

        $this->nilai_bawah_atas = ModelUnderwritingLimit::where('polis_id',$this->data->id)->groupBy('min_amount','max_amount')->orderBy('max_amount','ASC')->get();
        $this->usia = ModelUnderwritingLimit::where('polis_id',$this->data->id)->groupBy('usia')->get();
    }

    public function upload()
    {
        \LogActivity::add('[web] Upload UW Limit');
        $this->validate([
            'file'=>'required|mimes:xlsx|max:51200' // 50MB maksimal
        ]);
        
        $path = $this->file->getRealPath();
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(true);
        $xlsx = $reader->load($path);
        $sheetData = $xlsx->getActiveSheet()->toArray();
        
        if(count($sheetData) > 0){
            ModelUnderwritingLimit::where('polis_id',$this->data->id)->delete();
            
            $countLimit = 1;
            $total_failed = 0;
            $total_success = 0;
            $data_header = [];
            foreach($sheetData as $key => $item){
                if($key==1){
                    for($i=2;$i<=400;$i++){
                        if(!isset($item[$i])) continue;
                        $data_header[] = $item[$i];
                    }
                }
            }

            foreach($sheetData as $key => $item){
                if($key<=1) continue;
                // get header
                foreach($data_header as $k_header => $val_header){
                    
                    if($item[$k_header+2]=="") continue;

                    $data = new ModelUnderwritingLimit();
                    $data->polis_id = $this->data->id;
                    $data->min_amount = $item[0];
                    $data->max_amount = $item[1];
                    $data->usia = $val_header;
                    $data->keterangan = $item[$k_header+2];
                    $data->save();
                }
            }
        }

        $this->data->is_uw = 1;
        $this->data->save();

        $this->emit('reload-page');
    }
}