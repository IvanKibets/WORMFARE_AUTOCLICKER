<?php

namespace App\Http\Livewire\Pengajuan;

use Livewire\Component;
use App\Models\Reasuradur;
use App\Models\ReasuradurRate;
use App\Models\Reas;
use App\Models\Pengajuan;
use App\Models\Kepesertaan;

class SubmitReas extends Component
{
    public $pengajuan = [];
    public $reasuradur_id,$reasuradur=[],$rate=[],$or,$reas,$ri_com,$reasuradur_rate_id,$manfaat,$type_reas,$perhitungan_usia,$kadaluarsa_reas_hari;
    protected $listeners = ['set_pengajuan'=>'set_pengajuan'];
    public function render()
    {
        return view('livewire.pengajuan.submit-reas');
    }

    public function mount()
    {
        $this->reasuradur = Reasuradur::get();
    }

    public function set_pengajuan($pengajuan)
    {
        $this->pengajuan = Pengajuan::whereIn('id',$pengajuan)->get();;
    }

    public function updated($propertyName)
    {
        if($this->reasuradur_id) $this->rate = ReasuradurRate::where('reasuradur_id',$this->reasuradur_id)->get();
        if($this->reasuradur_rate_id){
            $find = ReasuradurRate::find($this->reasuradur_rate_id);
            $this->or = $find->or;
            $this->reas = $find->reas;
            $this->ri_com = $find->ri_com;
        }
    }

    public function save()
    {
        $this->validate([
            'reasuradur_id' => 'required',
            'reasuradur_rate_id' => 'required',
            'manfaat' => 'required',
            'perhitungan_usia' => 'required',
            // 'kadaluarsa_reas_hari' => 'required'
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
        // $data->kadaluarsa_reas_hari = $this->kadaluarsa_reas_hari;
        $data->save();

        foreach($this->pengajuan as $item){
            $item->reas_id = $data->id;
            $item->save();

            Kepesertaan::where(['pengajuan_id'=>$item->id,'status_akseptasi'=>1])->update(['status_reas'=>0,'reas_id'=>$data->id,'reas_manfaat'=>$this->manfaat,'reas_type'=>$this->type_reas]);
        }

        session()->flash('message-success',__('Pengajuan Reas berhasil disubmit'));

        return redirect()->route('reas.edit',$data->id);
    }
}
