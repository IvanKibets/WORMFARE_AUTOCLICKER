<?php

namespace App\Http\Livewire\UwLimit;

use Livewire\Component;
use App\Models\UnderwritingLimit;
use Livewire\WithFileUploads;
use App\Models\Polis;

class Index extends Component
{
    use WithFileUploads;
    public $data,$file,$nilai_bawah_atas=[],$usia=[],$tahun=[];
    public function render()
    {
        $data = UnderwritingLimit::get();
        $rows = [];

        foreach($data as $k => $item){
            $rows[$item->usia][$item->min_amount][$item->max_amount] = $item->keterangan;
        }
        return view('livewire.uw-limit.index')->with(['uw'=>$data,'rows'=>$rows]);
    }
    public function mount(Polis $data)
    {
        $this->nilai_bawah_atas = UnderwritingLimit::groupBy('min_amount','max_amount')->orderBy('max_amount','ASC')->get();
        $this->usia = UnderwritingLimit::groupBy('usia')->get();
        $this->data = $data;
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
                    $data = new ModelUnderwritingLimit();
                    $data->min_amount = $item[0];
                    $data->max_amount = $item[1];
                    $data->usia = $val_header;
                    $data->keterangan = $item[$k_header+2];
                    $data->save();
                }
            }
        }

        $this->emit('reload-page');
    }
}