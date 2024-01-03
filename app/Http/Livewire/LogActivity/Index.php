<?php

namespace App\Http\Livewire\LogActivity;

use Livewire\Component;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    public $keyword,$user_id,$date;
    protected $paginationTheme = 'bootstrap';
    public function render()
    {
        $data = \App\Models\LogActivity::with('user')->orderBy('id','desc');
        if($this->keyword) $data = $data->where(function($table){
            $table->where('subject', 'LIKE',"%{$this->keyword}%")
                ->orWhere('url', 'LIKE',"%{$this->keyword}%")
                ->orWhere('ip', 'LIKE',"%{$this->keyword}%")
                ->orWhere('agent', 'LIKE',"%{$this->keyword}%");
        });
        if($this->user_id) $data = $data->where('user_id',$this->user_id);
        if($this->date) $data = $data->whereDate('created_at',$this->date);
        
        return view('livewire.log-activity.index')->with(['data'=>$data->paginate(100)]);
    }
}
