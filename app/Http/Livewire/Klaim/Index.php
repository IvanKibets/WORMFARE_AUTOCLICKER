<?php

namespace App\Http\Livewire\Klaim;

use Livewire\Component;
use App\Models\Klaim;
use App\Models\Kepesertaan;
use Livewire\WithPagination;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $filter_keyword,$filter_cabang_id;
    
    public function render()
    {
        $data = Klaim::select('klaim.*')->with(['kepesertaan','polis','provinsi','kabupaten'])->orderBy('klaim.id','DESC')
                ->join('kepesertaan','kepesertaan.id','=','klaim.kepesertaan_id','LEFT');
        
        if(\Auth::user()->user_access_id==2) $data->where('klaim.user_id',\Auth::user()->id);
        if($this->filter_keyword) $data->where(function($table){
            foreach(\Illuminate\Support\Facades\Schema::getColumnListing('klaim') as $column){
                $table->orWhere('klaim.'.$column,'LIKE',"%{$this->filter_keyword}%");
            }
            $table->orWhere('kepesertaan.no_peserta','LIKE',"%{$this->filter_keyword}%");
        });
        
        if($this->filter_cabang_id) $data->where('klaim.user_id',$this->filter_cabang_id);

        $total_klaim = clone $data;
        $nilai_klaim = clone $data;
        $nilai_klaim_disetujui = clone $data;

        return view('livewire.klaim.index')->with(['data'=>$data->paginate(100),
                'total_klaim'=> $total_klaim->count(),
                'nilai_klaim'=>$nilai_klaim->sum('nilai_klaim'),
                'nilai_klaim_disetujui'=>$nilai_klaim_disetujui->sum('nilai_klaim_disetujui')]);
    }

    public function mount()
    {
        \LogActivity::add("Klaim");
    }

    public function delete(Klaim $data)
    {
        Kepesertaan::where('klaim_id',$data->id)->update(['klaim_id'=>null]);

        $data->delete();

        session()->flash('message-success',__('Data berhasil di hapus'));

        return redirect()->route('klaim.index');
    }
}
