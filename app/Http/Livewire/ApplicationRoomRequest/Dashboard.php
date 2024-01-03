<?php

namespace App\Http\Livewire\ApplicationRoomRequest;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\ApplicationRoomRequest;
use DB;

class Dashboard extends Component
{
    use WithPagination;
    public $date, $month, $year,$employee_id;
    public $labels;
    public $datasets;
    public $labelsapp;
    public $datasetsapp;
    public $title;
    public $startdate;
    public $enddate,$date_active,$data_room,$years=[];
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['set_selected_date'];
    public function render()
    {
        $this->generate_chart();
        return view('livewire.application-room-request.dashboard');
    }

    public function cancel_room(ApplicationRoomRequest $data)
    {
        \LogActivity::add('Cancel Room');

        $data->status = 3;
        $data->save();
        $this->data_room = ApplicationRoomRequest::where('type_request','room')->whereDate('start_booking',date('Y-m-d'))->get();

        $this->emit('message-success',"Request successfully canceled");
    }

    public function set_selected_date($date)
    {
        $this->date_active = date('d/M/Y',strtotime($date));
        $this->data_room = ApplicationRoomRequest::where('type_request','room')->whereDate('start_booking',date('Y-m-d',strtotime($date)))->get();
    }

    public function mount()
    {
        $this->date_active = date('d/M/Y');
        $this->employee_id = \Auth::user()->id;
        $this->years = ApplicationRoomRequest::selectRaw("YEAR(start_booking) as tahun")->groupBy('tahun')->get();
        $this->data_room = ApplicationRoomRequest::where('type_request','room')->whereDate('start_booking',date('Y-m-d'))->get();
    }

    public function updated()
    {
        $this->generate_chart();
    }

    public function generate_chart()
    {
        $this->labels = [];
        $this->datasets = [];
        $this->labelsapp = [];
        $this->datasetsapp = [];

        if(empty($this->month)) $this->month = date('m');

        if(empty($this->year)) $this->year = date('Y');

        $roomrequest = ApplicationRoomRequest::select('request_room_detail')
                                                            ->whereMonth('created_at', $this->month)
                                                            ->whereYear('created_at', $this->year)
                                                            ->where('type_request', 'room')
                                                            ->where('status', '2')
                                                            ->groupBy('request_room_detail')->get();
        $numbroomrequest = ApplicationRoomRequest::select(DB::Raw('count(request_room_detail) as jumlahrequest'))
                                                            ->whereMonth('created_at', $this->month)
                                                            ->whereYear('created_at', $this->year)
                                                            ->where('type_request', 'room')
                                                            ->where('status', '2')
                                                            ->groupBy('request_room_detail')->get();
        $apprequest = ApplicationRoomRequest::select('request_room_detail')
                                                            ->whereMonth('created_at', $this->month)
                                                            ->whereYear('created_at', $this->year)
                                                            ->where('type_request', 'application')
                                                            ->where('status', '2')
                                                            ->groupBy('request_room_detail')->get();
        $numbapprequest = ApplicationRoomRequest::select(DB::Raw('count(request_room_detail) as jumlahrequest'))
                                                            ->whereMonth('created_at', $this->month)
                                                            ->whereYear('created_at', $this->year)
                                                            ->where('type_request', 'application')
                                                            ->where('status', '2')
                                                            ->groupBy('request_room_detail')->get();

        $get_request_room = ApplicationRoomRequest::select('request_room_detail')->where(['type_request'=>'room','status'=>2])->get();
        $get_request_room_start = ApplicationRoomRequest::select('start_booking')->where(['type_request'=>'room','status'=>2])->get();
        $get_request_room_end = ApplicationRoomRequest::select('end_booking')->where(['type_request'=>'room','status'=>2])->get();

        $this->labels = json_encode($roomrequest);
        $this->datasets = json_encode($numbroomrequest);
        $this->labelsapp = json_encode($apprequest);
        $this->datasetsapp = json_encode($numbapprequest);
        $this->title = json_encode($get_request_room);
        $this->startdate = json_encode($get_request_room_start);
        $this->enddate = json_encode($get_request_room_end);

        $this->emit('init-chart',['labels'=>$this->labels,
                    'datasets'=>$this->datasets,
                    'labelsapp'=>$this->labelsapp,
                    'datasetsapp'=>$this->datasetsapp,
                    'title'=>$this->title,
                    'startdate'=>$this->startdate,
                    'enddate'=>$this->enddate]);
    }
}
