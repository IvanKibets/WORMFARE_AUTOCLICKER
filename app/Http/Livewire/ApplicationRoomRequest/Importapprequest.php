<?php

namespace App\Http\Livewire\ApplicationRoomRequest;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\ApplicationRoomRequest;

class Importapprequest extends Component
{

    use WithFileUploads;
    public $nik,$employee_id, $employee_name, $departement, $lokasi, $type_request, $request_room_detail,$description,$others;
    public $purpose, $participant, $start_date_booking, $start_time_booking, $end_date_booking, $end_time_booking;

    public function render()
    {
        $user = \Auth::user();
        // $this->nik = $user->employee->nik;
        $this->employee_id = $user->id;
        $this->employee_name = $user->name;
        // $this->departement = get_position($user->user_access_id);
        
        return view('livewire.application-room-request.importapprequest');
    }

    public function save()
    {
        $datamaster                             = new ApplicationRoomRequest();
        $datamaster->employee_id                = $this->employee_id;
        $datamaster->employee_name              = $this->employee_name;
        $datamaster->departement                = $this->departement;
        $datamaster->lokasi                     = $this->lokasi;
        $datamaster->type_request               = 'application';
        $datamaster->request_room_detail        = $this->request_room_detail == "Others" ? $this->others : $this->request_room_detail;
        $datamaster->start_booking              = '';
        $datamaster->end_booking                = '';
        $datamaster->purpose                    = $this->description;
        $datamaster->participant                = '';
        $datamaster->status                     = 0;
        $datamaster->note                       = '';
        $datamaster->save();
        
        session()->flash('message-success',"Success, <strong>Request Successfully Added</strong>");
        
        return redirect()->route('application-room-request.index');
    }
    
}
