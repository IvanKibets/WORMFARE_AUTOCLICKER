<?php

namespace App\Http\Livewire\Reas;

use App\Jobs\ReasCalculate;
use App\Models\Reas;
use Livewire\Component;
use App\Models\Kepesertaan;
use App\Models\ReasuradurRateUw;
use App\Models\ReasuradurRateRates;
use App\Models\ReasuradurRate;
use App\Models\Reasuradur;
use Livewire\WithPagination;

class Edit extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    protected $listeners = ['reload-page'=>'$refresh','set_calculate_reas'=>'set_calculate_reas',
        'data_assign_draft_'=>'data_assign_draft_',
        'data_assign_reas_'=>'data_assign_reas_',
        'data_assign_or_'=>'data_assign_or_'
    ];
    public $data,$no_pengajuan,$tab_active='tab_draft',$check_id=[],$filter_status,$is_calculate=false,$filter_perhitungan_usia;
    public $filter_ul_arr=[],$filter_ul,$filter_peserta,$is_reassign=false;
    public $data_reassign_draft=[],$data_reassign_reas=[],$data_reassign_or=[],$reasuradur=[],$reasuradur_id,$reasuradur_rate_id;
    public $manfaat,$perhitungan_usia,$or,$reas,$ri_com,$type_reas,$kadaluarsa_reas_hari,$is_edit_kadaluarsa=false;
    public function render()
    {
        $kepesertaan = Kepesertaan::with(['pengajuan','polis'])->where(['reas_id'=>$this->data->id,'status_akseptasi'=>1]);

        $draft = clone $kepesertaan;
        $reas = clone $kepesertaan;
        $or = clone $kepesertaan;

        if($this->filter_status) $kepesertaan->where('status_reas',$this->filter_status);

        return view('livewire.reas.edit')->with(['count_draft'=>$draft->where('status_reas',0)->count(),
                                                    'count_reas'=>$reas->where('status_reas',1)->count(),
                                                    'count_or'=>$or->where('status_reas',2)->count()]);
    }

    public function mount($id)
    {
        $this->data = Reas::find($id);
        $this->kadaluarsa_reas_hari = $this->data->kadaluarsa_reas_hari;
        $this->filter_perhitungan_usia = $this->data->perhitungan_usia;
        $this->no_pengajuan = $this->data->no_pengajuan;
        $this->filter_ul_arr = Kepesertaan::where('reas_id',$this->data->id)->groupBy('ul_reas')->get();
        $this->reasuradur = Reasuradur::get();
    }

    public function saveKadaluarsa()
    {
        $this->data->kadaluarsa_reas_hari = $this->kadaluarsa_reas_hari;
        $this->data->save();
        $this->is_edit_kadaluarsa = false;
    }

    public function submit_reassign()
    {
        $this->validate([
            'reasuradur_id' => 'required',
            'reasuradur_rate_id' => 'required',
            'manfaat' => 'required',
            'perhitungan_usia' => 'required'
        ]);

        $data = new Reas();
        $data->no_pengajuan = 'R'.date('dmy').str_pad((Reas::count()+1),6, '0', STR_PAD_LEFT);
        $data->reasuradur_id = $this->reasuradur_id;
        $data->reasuradur_rate_id = $this->reasuradur_rate_id;
        $data->or = $this->or;
        $data->reas = $this->reas;
        $data->ri_com = $this->ri_com;
        $data->manfaat  = $this->manfaat;
        $data->type_reas = $this->type_reas;
        $data->perhitungan_usia = $this->perhitungan_usia;
        $data->save();

        foreach($this->data_reassign_draft as $item){
            $item->reas_id = $data->id;
            $item->save();

            Kepesertaan::where(['pengajuan_id'=>$item->id,'status_akseptasi'=>1])->update(['status_reas'=>0,'reas_id'=>$data->id,'reas_manfaat'=>$this->manfaat,'reas_type'=>$this->type_reas]);
        }
        foreach($this->data_reassign_reas as $item){
            $item->reas_id = $data->id;
            $item->save();

            Kepesertaan::where(['pengajuan_id'=>$item->id,'status_akseptasi'=>1])->update(['status_reas'=>0,'reas_id'=>$data->id,'reas_manfaat'=>$this->manfaat,'reas_type'=>$this->type_reas]);
        }
        foreach($this->data_reassign_or as $item){
            $item->reas_id = $data->id;
            $item->save();

            Kepesertaan::where(['pengajuan_id'=>$item->id,'status_akseptasi'=>1])->update(['status_reas'=>0,'reas_id'=>$data->id,'reas_manfaat'=>$this->manfaat,'reas_type'=>$this->type_reas]);
        }

        session()->flash('message-success',__('Reassign berhasil submit, dengan no pengajuan : <a href="'.route('reas.edit',$data->id).'" target="_blank">'. $data->no_pengajuan.'</a>'));

        return redirect()->route('reas.edit',$this->data->id);
    }

    public function data_assign_draft_($data)
    {
        $this->data_reassign_draft = Kepesertaan::whereIn('id',$data)->get();
    }

    public function data_assign_reas_($data)
    {
        $this->data_reassign_reas = Kepesertaan::whereIn('id',$data)->get();
    }

    public function data_assign_or_($data)
    {
        $this->data_reassign_or = Kepesertaan::whereIn('id',$data)->get();
    }

    public function set_reassign($status=false)
    {
        $this->is_reassign = $status;
        $this->emit('reassign',$status);
    }

    public function updated($propertyName)
    {
        if($propertyName=='filter_perhitungan_usia'){
            $this->data->perhitungan_usia = $this->filter_perhitungan_usia;
            $this->data->save();

            ReasCalculate::dispatch($this->data->id);
        }
        if($propertyName=='filter_ul') $this->emit('filter-ul',$this->filter_ul);
        if($propertyName=='filter_peserta') $this->emit('filter-peserta',$this->filter_peserta);
        if($this->reasuradur_id) $this->rate = ReasuradurRate::where('reasuradur_id',$this->reasuradur_id)->get();
        if($this->reasuradur_rate_id) {
            $find = ReasuradurRate::find($this->reasuradur_rate_id);
            $this->or = $find->or;
            $this->reas = $find->reas;
            $this->ri_com = $find->ri_com;
        }
    }

    public function hitung()
    {
        $this->is_calculate = true;
        ReasCalculate::dispatch($this->data->id);
    }

    public function set_calculate_reas()
    {
        $this->is_calculate = false;
        $this->emit('reload-page');
    }

    public function submit_underwriting()
    {
        $this->data->status = 1;
        $this->data->save();

        session()->flash('message-success',__('Pengajuan berhasil submit'));

        return redirect()->route('reas.edit',$this->data->id);
    }

    public function submit_head_teknik()
    {
        $this->data->status = 2;
        $this->data->save();

        session()->flash('message-success',__('Pengajuan berhasil submit'));

        return redirect()->route('reas.edit',$this->data->id);
    }

    public function submit_head_syariah()
    {
        $this->data->status = 3;
        $this->data->save();

        session()->flash('message-success',__('Pengajuan berhasil submit'));

        return redirect()->route('reas.edit',$this->data->id);
    }
}
