<?php

namespace App\Http\Livewire\ApplicationRoomRequest;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ApplicationRoomRequest;

class Data extends Component
{
    use WithPagination;
    public $date, $type_request,$status,$is_manager_approval,$selected,$is_pmg_approval,$note;
    protected $paginationTheme = 'bootstrap';
    
    public function render()
    {  
        $data = ApplicationRoomRequest::where('type_request','Application')
                                ->orderBy('created_at', 'desc')
                                ->where('employee_id',\Auth::user()->id);
                            
        if($this->date) $data = $data->whereDate('created_at',$this->date);
        
        return view('livewire.application-room-request.data')->with(['data'=>$data->paginate(50)]);        
    }

    public function set_id(ApplicationRoomRequest $data)
    {
        $this->selected = $data;
    }

    public function reject_pmg()
    {
        $this->selected->status=3; // reject manager
        $this->selected->note_pmg=$this->note;
        $this->selected->save();

        session()->flash('message-success',"Data processed successfully");

        return redirect()->route('application-room-request.index');
    }

    public function approve_pmg()
    {
        $this->selected->status=2; // approve manager
        $this->selected->note_pmg=$this->note;
        $this->selected->save();
        
        // $notif = check_access_data('application-room-request.notif-manager', '');
        // $message = "*Dear PMG / IT *\n\n";
        // $alert = "Berhasil, Pengajuan Application Access sudah diapprove dan menunggu approval PMG / IT !!!";
        
        // $nameuser = [];
        // $emailuser = [];
        // $phoneuser = [];
        // foreach($notif as $no => $itemuser){
        //     $nameuser[$no] = $itemuser->name;
        //     $emailuser[$no] = $itemuser->email;
        //     $phoneuser[$no] = $itemuser->telepon;
        //     $message .= "Pengajuan Room Access sudah diapprove\n\n";
        //     // send_wa(['phone'=> $phoneuser[$no],'message'=>$message]);    

        //     // \Mail::to($emailuser[$no])->send(new PoTrackingReimbursementUpload($item));
        // }
    
        session()->flash('message-success',"Data processed successfully");

        return redirect()->route('application-room-request.index');
    }


    public function reject_manager()
    {
        $this->selected->status=3; // reject manager
        $this->selected->note=$this->note;
        $this->selected->save();

        session()->flash('message-success',"Data processed successfully");

        return redirect()->route('application-room-request.index');
    }

    public function approve_manager()
    {
        $this->selected->status=1; // approve manager
        $this->selected->note=$this->note;
        $this->selected->save();
        
        $notif = check_access_data('application-room-request.notif-manager', '');
        $message = "*Dear PMG / IT *\n\n";
        $alert = "Berhasil, Pengajuan Application Access sudah diapprove dan menunggu approval PMG / IT !!!";
        
        $nameuser = [];
        $emailuser = [];
        $phoneuser = [];
        foreach($notif as $no => $itemuser){
            $nameuser[$no] = $itemuser->name;
            $emailuser[$no] = $itemuser->email;
            $phoneuser[$no] = $itemuser->telepon;
            $message .= "*Pengajuan Application Access dengan id ".$this->selected_id." menunggu konfirmasi *\n\n";
            // send_wa(['phone'=> $phoneuser[$no],'message'=>$message]);    

            // \Mail::to($emailuser[$no])->send(new PoTrackingReimbursementUpload($item));
        }
    
        session()->flash('message-success',$alert);

        return redirect()->route('application-room-request.index');
    }

    public function mount()
    {
        // $this->is_manager_approval = check_access('application-room-request.manager-approval');
        // $this->is_pmg_approval = check_access('application-room-request.pmg-approval');
    }
}