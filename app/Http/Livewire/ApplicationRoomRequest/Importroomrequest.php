<?php

namespace App\Http\Livewire\ApplicationRoomRequest;

use Livewire\Component;
use Livewire\WithFileUploads;
use Auth;
use DB;
use App\Models\ApplicationRoomRequest;

class Importroomrequest extends Component
{

    use WithFileUploads;
    public $nik,$employee_id, $employee_name, $departement, $lokasi, $type_request, $request_room_detail;
    public $purpose, $participant, $start_date_booking, $start_time_booking, $end_date_booking, $end_time_booking,$message_error;
    protected $listeners = ['set_selected_date','reload'=>'$refresh'];

    public function render()
    {
        $user = \Auth::user();
        $this->employee_id = $user->id;
        $this->employee_name = $user->name;
        // $this->departement = get_position($user->user_access_id);
    
        return view('livewire.application-room-request.importroomrequest');        
    }

    public function mount()
    {
        $this->start_date_booking = date('Y-m-d');
    }

    public function updated($propertyName)
    {
        if($this->start_date_booking and $this->start_time_booking and $this->end_time_booking and $this->request_room_detail){
            $check = ApplicationRoomRequest::where(function($table){
                $table->whereBetween('start_booking',[$this->start_date_booking .' '.$this->start_time_booking.':00',$this->start_date_booking.' '.$this->end_time_booking.':00'])
                        ->orWhereBetween('end_booking',[$this->start_date_booking.' '.$this->start_time_booking.':00',$this->start_date_booking.' '.$this->end_time_booking.':00']);
                })
                ->where('request_room_detail', $this->request_room_detail)
                ->where('status',2)
                ->first();
            
            if($check)
                $this->message_error = 'Request Can Not be Saved, Try Another Room or Time';
            else
                $this->message_error = '';
        }
    }

    public function set_selected_date($date)
    {
        $this->start_date_booking = date('Y-m-d',strtotime($date));
    }

    public function save()
    {
        $check = ApplicationRoomRequest::where(function($table){
                                        $table->whereBetween('start_booking',[$this->start_date_booking .' '.$this->start_time_booking.':00',$this->start_date_booking.' '.$this->end_time_booking.':00'])
                                                ->orWhereBetween('end_booking',[$this->start_date_booking.' '.$this->start_time_booking.':00',$this->start_date_booking.' '.$this->end_time_booking.':00']);
                                        })
                                        ->where('request_room_detail', $this->request_room_detail)
                                        ->where('status',2)
                                        ->first();

        if(!$check){
            $datamaster                             = new ApplicationRoomRequest();
            $datamaster->employee_id                = \Auth::user()->id;
            $datamaster->employee_name              = \Auth::user()->name;
            $datamaster->lokasi                     = $this->lokasi;
            $datamaster->type_request               = 'room';
            $datamaster->request_room_detail        = $this->request_room_detail;
            $datamaster->start_booking              = $this->start_date_booking.' '.$this->start_time_booking;
            $datamaster->end_booking                = $this->start_date_booking.' '.$this->end_time_booking;
            $datamaster->duration                   = $this->duration($datamaster->end_booking, $datamaster->start_booking);
            $datamaster->purpose                    = $this->purpose;
            $datamaster->participant                = $this->participant;
            $datamaster->status                     = '2';
            $datamaster->note                       = '';
            $datamaster->created_at                 = date('Y-m-d H:i:s');
            $datamaster->updated_at                 = date('Y-m-d H:i:s');
            $datamaster->save();
            session()->flash('message-success',"Success, <strong>Request Successfully Added</strong>");
            return redirect()->route('application-room-request.index');
        }else{
            session()->flash('message-error',"Failed, <strong>Request Can Not be Saved, Try Another Room or Time</strong>");
            return redirect()->route('application-room-request.index');
        }
    }


    public function duration($start_time, $end_time){
        
        $diff = abs(strtotime($end_time) - strtotime($start_time));
        $years   = floor($diff / (365*60*60*24)); 
        $months  = floor(($diff - $years * 365*60*60*24) / (30*60*60*24)); 
        $days    = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24)/ (60*60*24));
        $hours   = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24)/ (60*60)); 
        $minuts  = floor(($diff - $years * 365*60*60*24 - $months*30*60*60*24 - $days*60*60*24 - $hours*60*60)/ 60); 
        
        if($hours > 0){
            $waktu = $hours.'.'.$minuts.' hours';
            // $waktu = $hours;
        }else{
            $waktu = $minuts.' minute';
            // $waktu = $minuts;
        }
        return $waktu;
    }
}