<?php

namespace App\Http\Livewire\Polis;

use Livewire\Component;
use App\Models\Polis;
use App\Models\Kepesertaan as ModelKepesertaan;
use App\Models\UnderwritingLimit;
use App\Models\Rate;

class Kepesertaan extends Component
{
    public $data,$keyword;
    protected $listeners = ['reload-page'=>'$refresh'];
    public function render()
    {
        $data = ModelKepesertaan::where('polis_id',$this->data->id)->orderBy('id','ASC');

        if($this->keyword) $data = $data->where(function($table){
            foreach(\Illuminate\Support\Facades\Schema::getColumnListing('kepesertaan') as $column){
                $table->orWhere('kepesertaan.'.$column,'LIKE',"%{$this->keyword}%");
            }
        });

        return view('livewire.polis.kepesertaan')->with(['kepesertaan'=>$data->paginate(100)]);
    }

    public function mount(Polis $data)
    {
        $this->data = $data;
    }

    public function calculate()
    {
        $datas = ModelKepesertaan::where('polis_id',$this->data->id)->orderBy('id','ASC')->get();
        foreach($datas as $data){
            // generate no peserta
            $no_peserta = $data->polis->produk->id ."-". date('ym').str_pad($data->id,4, '0', STR_PAD_LEFT).'-'.str_pad($data->polis_id,6, '0', STR_PAD_LEFT);
            $data->no_peserta = $no_peserta;
            $nilai_manfaat_asuransi = $data->basic;

            if($data->masa_bulan /12 >15)
                $data->kontribusi_keterangan = 'max. 15 th';
            else{
                // find rate
                $rate = Rate::where(['tahun'=>$data->usia,'bulan'=>$data->masa_bulan])->first();
                $data->rate = $rate ? $rate->rate : 0;
                $data->kontribusi = $nilai_manfaat_asuransi * $data->rate/1000;
            }
            
            $data->dana_tabarru = ($data->kontribusi*$data->polis->iuran_tabbaru)/100; // persen ngambil dari daftarin polis
            $data->dana_ujrah = ($data->kontribusi*$data->polis->ujrah_atas_pengelolaan)/100; 
            $data->extra_mortalita = $data->rate_em*$nilai_manfaat_asuransi/1000;
            
            /**
             * 
             * @var : kontribusi
             * @param : = IF((AF9/12)>15;"max. 15 th";ROUNDDOWN(R9*AJ9/1000;0))
             * 
             * @var : Masa Bulan
             * @param : AF9 = IF(L9="";"";AO9*12+(AP9+IF(AQ9>0;1;0)))
             *  
             * @param : R9 = Nilai manfaat asuransi
             * @param : N9 = Usia
             * @param : AJ9 = VLOOKUP(N9;rate;AF9+1;0)
             */

            /**  \
             * @param : UW
             * @param : =IF(N9+(AF9/12)>75;"X+N=75";VLOOKUP(R9;uw_limit;VLOOKUP(N9;lookup;4;TRUE);TRUE)) 
             * 
             */
            if($data->usia + ($data->masa_bulan/12) > 75){
                $data->ul = "X+N=75";
                $data->uw = "X+N=75";
            }else{
                $uw = UnderwritingLimit::where('max_amount','<=',$nilai_manfaat_asuransi)->where('min_amount','>=',$nilai_manfaat_asuransi)
                                        ->where('usia',$data->usia)->first();

                if(!$uw) $uw = UnderwritingLimit::where('max_amount','<=',$nilai_manfaat_asuransi) ->where('usia',$data->usia)->first();
                if($uw) {
                    $data->uw = $uw->keterangan;
                    $data->ul = $uw->keterangan;
                }
            }

            if(isset($data->polis->waiting_period) and $data->polis->waiting_period !="")
                $data->tanggal_stnc = date('Y-m-d',strtotime(" +{$data->polis->waiting_period} month", strtotime($data->polis->tanggal_akseptasi)));
            else{
                if(countDay($data->polis->tanggal_akseptasi,$data->tanggal_mulai) > $data->polis->retroaktif){
                    $data->tanggal_stnc = date('Y-m-d');
                }elseif(countDay($data->polis->tanggal_akseptasi,$data->tanggal_mulai) < $data->polis->retroaktif){
                    $data->tanggal_stnc = null;
                }
            }

            $data->is_hitung = 1;
            $data->save();
        }

        $this->emit('message-success','Data berhasil dikalkukasi');
        $this->emit('reload-page');
    }
}