<?php

namespace App\Http\Livewire\Pengajuan;

use Livewire\Component;
use App\Models\Kepesertaan;
use App\Models\Polis;
use App\Models\Rate;
use Livewire\WithPagination;
use App\Models\UnderwritingLimit;

class InsertRow extends Component
{
    protected $listeners = ['reload-row'=>'$refresh','set_polis_id'=>'set_polis_id','hitung'=>'hitung'];
    public $polis_id,$total_pengajuan,$perhitungan_usia=1,$masa_asuransi=1;
    public $total_double=0;
    public $total_nilai_manfaat=0,$total_dana_tabbaru=0,$total_dana_ujrah=0,$total_kontribusi=0,$total_em=0,$total_ek=0,$total_total_kontribusi=0,$check_all,$check_id=[],$total_selected=0;
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public function render()
    {
        $kepesertaan = [];
        if($this->polis_id){
            $kepesertaan = Kepesertaan::with(['parent'])->where(['polis_id'=>$this->polis_id,'is_temp'=>1,'user_id'=>\Auth::user()->id]);
            $this->total_double = Kepesertaan::where(['polis_id'=>$this->polis_id,'is_temp'=>1,'is_double'=>1,'user_id'=>\Auth::user()->id])->count();

            $total_pengajuan = clone $kepesertaan;
            $this->total_pengajuan = $total_pengajuan->count();
        }

        return view('livewire.pengajuan.insert-row')->with(['kepesertaan'=>$kepesertaan ? $kepesertaan->paginate(1000) : []]);
    }

    public function mount($polis_id)
    {
        $this->polis_id = $polis_id;
    }

    public function delete_selected()
    {
        foreach($this->check_id as $id){
            $find = Kepesertaan::find($id);
            if($find) $find->delete();
        }
        $this->total_selected=0;
        $this->check_id = [];
        $this->emit('reload-row');
    }

    public function updated($propertyName)
    {
        if($propertyName=='check_all' and $this->check_all==1){
            if($this->polis_id){
                $kepesertaan = Kepesertaan::with(['parent'])->where(['polis_id'=>$this->polis_id,'is_temp'=>1,'user_id'=>\Auth::user()->id])->get();
                foreach($kepesertaan as $k => $item){
                    $this->check_id[$k] = $item->id;
                    $this->total_selected++;
                }
            }
        }elseif($propertyName=='check_all' and $this->check_all==0){
            $this->check_id = [];
        }
        $this->total_selected=0;
        foreach($this->check_id as $val) if($val) $this->total_selected++;
    }

    public function delete(Kepesertaan $data)
    {
        $data->delete();
        $this->check_id = [];
        $this->total_selected=0;
        $this->emit('reload-row');
    }

    public function set_polis_id($polis_id)
    {
        $this->polis_id = $polis_id;
    }

    public function hitung()
    {
        $polis = Polis::find($this->polis_id);
        $iuran_tabbaru = $polis->iuran_tabbaru;
        $ujrah = $polis->ujrah_atas_pengelolaan;
        $key=0;
        $update  =[];
        foreach(Kepesertaan::where(['polis_id'=>$this->polis_id,'is_temp'=>1,'user_id'=>\Auth::user()->id])->get() as $data){
            $check =  Kepesertaan::where(['nama'=>$data->nama,'tanggal_lahir'=>$data->tanggal_lahir,'user_id'=>\Auth::user()->id])->where(function($table){
                $table->where('status_polis','Inforce')->orWhere('status_polis','Akseptasi');
            })->sum('basic');

            $update[$key]['id'] = $data->id;
            if($check>0){
                $update[$key]['is_double'] = 1;
                $update[$key]['akumulasi_ganda'] =$check+$data->basic;
            }else $update[$key]['is_double']=0;

            $nilai_manfaat_asuransi = $data->basic;

            $update[$key]['usia'] =  $data->tanggal_lahir ? hitung_umur($data->tanggal_lahir,$this->perhitungan_usia) : '0';
            $update[$key]['masa'] = hitung_masa($data->tanggal_mulai,$data->tanggal_akhir);
            $update[$key]['masa_bulan'] =  hitung_masa_bulan($data->tanggal_mulai,$data->tanggal_akhir,$this->masa_asuransi);

            // find rate
            $rate = Rate::where(['tahun'=>$data->usia,'bulan'=>$data->masa_bulan,'polis_id'=>$this->polis_id])->first();
            if(!$rate || $rate->rate ==0 || $rate->rate ==""){
                $data->rate = 0;
                $data->kontribusi = 0;
            }else{
                // $data->rate = $rate ? $rate->rate : 0;
                // $data->kontribusi = $nilai_manfaat_asuransi * $data->rate/1000;
                $update[$key]['rate'] = $rate ? $rate->rate : 0;
                $update[$key]['kontribusi'] = $nilai_manfaat_asuransi * $data->rate/1000;

            }

            // $data->dana_tabarru = ($data->kontribusi*$iuran_tabbaru)/100; // persen ngambil dari daftarin polis
            // $data->dana_ujrah = ($data->kontribusi*$ujrah)/100;
            // $data->extra_mortalita = $data->rate_em*$nilai_manfaat_asuransi/1000;

            $update[$key]['dana_tabarru'] = ($data->kontribusi*$iuran_tabbaru)/100; // persen ngambil dari daftarin polis
            $update[$key]['dana_ujrah'] = ($data->kontribusi*$ujrah)/100;
            $update[$key]['extra_mortalita'] = $data->rate_em*$nilai_manfaat_asuransi/1000;

            if($data->akumulasi_ganda)
                $uw = UnderwritingLimit::whereRaw("{$data->akumulasi_ganda} BETWEEN min_amount and max_amount")->where(['usia'=>$data->usia,'polis_id'=>$this->polis_id])->first();
            else
                $uw = UnderwritingLimit::whereRaw("{$nilai_manfaat_asuransi} BETWEEN min_amount and max_amount")->where(['usia'=>$data->usia,'polis_id'=>$this->polis_id])->first();

            if(!$uw) $uw = UnderwritingLimit::where(['usia'=>$data->usia,'polis_id'=>$this->polis_id])->orderBy('max_amount','ASC')->first();
            if($uw){
                $update[$key]['uw'] = $uw->keterangan;
                $update[$key]['ul'] = $uw->keterangan;
                // $data->uw = $uw->keterangan;
                // $data->ul = $uw->keterangan;
            }
            // $data->is_hitung = 1;
            // $data->save();

            $update[$key]['is_hitung'] = 1;
            $key++;
        }

        \Batch::update(new Kepesertaan,$update,'id');

        $this->emit('message-success','Data berhasil dikalkukasi');
        $this->emit('reload-row');
    }
}
