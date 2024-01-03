<?php

namespace App\Http\Livewire\Reas;

use Livewire\Component;
use App\Models\Reas;
use App\Models\Kepesertaan;
use App\Models\Pengajuan;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $filter_keyword;
    public function render()
    {
        $data  = Reas::with(['pengajuan','reasuradur','rate_uw'])->withCount('kepesertaan')->orderBy('id','DESC');

        if($this->filter_keyword) $data->where(function($table){
            foreach(\Illuminate\Support\Facades\Schema::getColumnListing('reas') as $column){
                $table->orWhere($column,'LIKE',"%{$this->filter_keyword}%");
            }
        });

        return view('livewire.reas.index')->with(['data'=>$data->paginate(100)]);
    }

    public function delete(Reas $id)
    {
        Kepesertaan::where('reas_id',$id->id)->update(['reas_id'=>null,'status_reas'=>null]);

        Pengajuan::where('reas_id',$id->id)->update(['reas_id'=>null]);

        $id->delete();

        session()->flash('message-success',__('Data berhasil dihapus'));

        return redirect()->route('reas.index');
    }
}
