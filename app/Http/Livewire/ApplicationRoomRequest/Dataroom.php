<?php

namespace App\Http\Livewire\ApplicationRoomRequest;

use Livewire\Component;
use Livewire\WithPagination;
use Auth;
use DB;


class Dataroom extends Component
{
    use WithPagination;
    public $date, $type_request, $status;
    protected $paginationTheme = 'bootstrap';
    
    public function render()
    {

       
        $data = \App\Models\ApplicationRoomRequest::where('type_request','Room')->orderBy('created_at', 'desc');
                                    

        if($this->date) $ata = $data->whereDate('created_at',$this->date);
        // if($this->status == 'all'){
        //     $ata = $data->where(DB::Raw('status != "" '));
        // }else{
        //     $ata = $data->where('status',$this->status);
        // } 
        // if($this->type_request) $ata = $data->where('type_request',$this->type_request);
                        
        
        return view('livewire.application-room-request.dataroom')->with(['data'=>$data->paginate(50)]);

        
    }


}



