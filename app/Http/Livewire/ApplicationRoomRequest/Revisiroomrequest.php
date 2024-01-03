<?php

namespace App\Http\Livewire\ApplicationRoomRequest;

use Livewire\Component;
use Livewire\WithFileUploads;
use Auth;
use DB;

class Revisiroomrequest extends Component
{

    protected $listeners = [
        'modalrevisidutyroster'=>'revisidutyroster',
    ];

    use WithFileUploads;
    public $file, $selected_id;

    
    public function render()
    {
        
        // if(!check_access('accident-report.index')){
        //     session()->flash('message-error','Access denied, you have no permission please contact your administrator.');
        //     $this->redirect('/');
        // }
        
        
        return view('livewire.application-room-request.revisiroomrequest');
        
    }

    public function revisidutyroster($id)
    {
        $this->selected_id = $id;
    }

  
    public function save()
    {
        $this->validate([
            'file'=>'required|mimes:xls,xlsx|max:51200' // 50MB maksimal
        ]);
        
        $users = Auth::user();


        $datadelete                 = \App\Models\DutyrosterDophomebaseDetail::where('id_master_dutyroster', $this->selected_id)->delete();

        

        $path = $this->file->getRealPath();
       
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $data = $reader->load($path);
        $sheetData = $data->getActiveSheet()->toArray();


        if(count($sheetData) > 0){
            $countLimit = 1;
            $total_failed = 0;
            $total_success = 0;

            


            foreach($sheetData as $key => $i){
                if($key<2) continue; // skip header
                
                foreach($i as $k=>$a){ $i[$k] = trim($a); }
                    
                    $data                           = new \App\Models\DutyrosterDophomebaseDetail();

                    $data->id_master_dutyroster     = $this->selected_id;
                    $data->nama_dop                 = $i[0];
                    $data->project                  = $i[1];
                    $data->region                   = $i[2];
                    $data->alamat                   = $i[3];
                    $data->long                     = $i[4];
                    $data->lat                      = $i[5];
                    $data->pemilik_dop              = $i[6];
                    $data->telepon_pemilik          = $i[7];
                    $data->opex_region_ga           = $i[8];
                    $data->type_homebase_dop        = $i[9];
                    $data->remarks                  = '';


                    $data->created_at           = date('Y-m-d H:i:s');
                    $data->updated_at           = date('Y-m-d H:i:s');
                    $data->save();
                    
               

                $total_success++;
            }

            $dataupdate                 = \App\Models\DutyrosterDophomebaseMaster::where('id', $this->selected_id)->first();
 
            $dataupdate->status         = '';
            $dataupdate->note           = '';
            $dataupdate->save();

            session()->flash('message-success',"Upload success, Success : <strong>{$total_success}</strong>, Total Failed <strong>{$total_failed}</strong>");
            
            return redirect()->route('duty-roster-dophomebase.index');   
        }
    }
    

    public function yearborn($year){
        if($year > substr(date('Y'), 2, 2)){
            return '19'.$year;
        }else{
            return '20'.$year;
        }

    }
}
