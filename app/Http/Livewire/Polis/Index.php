<?php

namespace App\Http\Livewire\Polis;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Polis;

class Index extends Component
{
    protected $listeners = ['reload-page'=>'$refresh'];
    public $filter_keyword;
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public function render()
    {
        $data = Polis::with(['produk','provinsi'])
                        // ->withCount(['rate_','uw_limit_'])
                        ->orderBy('id','desc');
        
        if($this->filter_keyword) $data->where(function($table){
            foreach(\Illuminate\Support\Facades\Schema::getColumnListing('polis') as $column){
                $table->orWhere($column,'LIKE',"%{$this->filter_keyword}%");
            }
        });

        if(\Auth::user()->user_access_id!=1) redirect('pengajuan');

        return view('livewire.polis.index')->with(['data'=>$data->paginate(500)]);
    }

    public function clear_filter()
    {
        $this->reset('filter_keyword');
    }
}