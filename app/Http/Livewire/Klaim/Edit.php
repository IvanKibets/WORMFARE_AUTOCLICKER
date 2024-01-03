<?php

namespace App\Http\Livewire\Klaim;

use Livewire\Component;
use App\Models\Klaim;
use App\Models\Kepesertaan;
use App\Models\Pengajuan;
use App\Models\KlaimFile;
use Livewire\WithFileUploads;

class Edit extends Component
{
    use WithFileUploads;
    public $data,$peserta,$tab_active='tab_data_klaim', $nilai_klaim, $nilai_klaim_disetujui,$nilai_klaim_or,$nilai_klaim_reas;
    public $organ_yang_mencakup,$kategori_penyakit,$bank_cabang,$kadaluarsa_reas_tanggal,$share_or,$share_reas,$note_pusat;
    public $files = [],$file_title=[],$note_ajri,$status_pengajuan;
    public function render()
    {
        return view('livewire.klaim.edit');
    }

    public function mount(Klaim $id)
    {
        $this->data = $id;
        $this->bank_no_rekening = $this->data->bank_no_rekening;
        $this->bank_cabang = $this->data->bank_cabang;
        $this->bank_atas_nama = $this->data->bank_atas_nama;
        $this->bank_mata_uang = $this->data->bank_mata_uang;
        $this->peserta = $this->data->kepesertaan;
        $this->nilai_klaim = $this->data->nilai_klaim;
        $this->nilai_klaim_disetujui = $id->nilai_klaim_disetujui;
        $this->organ_yang_mencakup = $this->data->organ_yang_mencakup;
        $this->kategori_penyakit = $this->data->kategori_penyakit;
        $this->kadaluarsa_reas_tanggal = $this->data->kadaluarsa_reas_tanggal;
        $this->share_or = $this->data->share_or;
        $this->share_reas = $this->data->share_reas;
        $this->nilai_klaim_reas = $this->data->nilai_klaim_reas;
        $this->nilai_klaim_or = $this->data->nilai_klaim_or;
        $this->files[] = "";$this->file_title[] = "";
        if($this->data->jatuh_tempo==""){
            if(isset($this->peserta->polis->pembayaran_klaim)){
                $this->data->jatuh_tempo = date('Y-m-d',strtotime("{$this->data->updated_at} +{$this->data->kepesertaan->polis->pembayaran_klaim} days"));
                $this->data->save();
            }
        }
        if(isset($this->peserta->pengajuan->kontribusi) and $this->peserta->pengajuan->kontribusi<=0){
            $select = Kepesertaan::select(\DB::raw("SUM(basic) as total_nilai_manfaat"),
                    \DB::raw("SUM(dana_tabarru) as total_dana_tabbaru"),
                    \DB::raw("SUM(dana_ujrah) as total_dana_ujrah"),
                    \DB::raw("SUM(kontribusi) as total_kontribusi"),
                    \DB::raw("SUM(extra_kontribusi) as total_extra_kontribusi"),
                    \DB::raw("SUM(extra_mortalita) as total_extra_mortalita")
                    )->where(['pengajuan_id'=>$this->peserta->pengajuan_id,'status_akseptasi'=>1])->first();
                   
            $nilai_manfaat = $select->total_nilai_manfaat;
            $dana_tabbaru = $select->total_dana_tabbaru;
            $dana_ujrah = $select->total_dana_ujrah;
            $kontribusi = $select->total_kontribusi;
            $ektra_kontribusi = $select->total_extract_kontribusi;
            $extra_mortalita = $select->total_extra_mortalita;
        
            if($select){
                Pengajuan::find($this->peserta->pengajuan_id)->update(['kontribusi'=>$kontribusi,
                                'nilai_manfaat'=>$nilai_manfaat,
                                'dana_tabbaru'=>$dana_tabbaru,
                                'dana_ujrah'=>$dana_ujrah,
                                'extra_kontribusi'=>$ektra_kontribusi,
                                'extra_mortalita'=>$extra_mortalita,
                                ]);
            }
        }

        \LogActivity::add("Klaim Edit {$id->id}");
    }

    public function add_files()
    {
        $this->files[] = "";
        $this->file_title[] = ""; 
    }

    public function deleteFile($key)
    {
        unset($this->files[$key],$this->file_title[$key]);
    }

    public function updated($propertyName)
    {
        if(isset($this->peserta->reas->rate_uw->model_reas)){
            $max_or = $this->peserta->reas->rate_uw->max_or ? $this->peserta->reas->rate_uw->max_or : 0; // AS
            $model_reas = $this->peserta->reas->rate_uw->model_reas; // AR
            $basic = $this->peserta->basic; // V
            $or_share = $this->peserta->reas->or; // AT
            $nilai_klaim = $this->nilai_klaim_disetujui; // Z

           if($model_reas=="OR"){
                $this->nilai_klaim_or = $nilai_klaim ? $nilai_klaim : 0;
            }elseif($model_reas=="Surplus" and $basic<=$max_or){
                if($nilai_klaim) {
                    $this->nilai_klaim_or = ($max_or / $basic) * $nilai_klaim;
                }
            }elseif($model_reas=='QS'){
                if($nilai_klaim) {
                    $this->nilai_klaim_or =  ($or_share/100)*$nilai_klaim;
                }
            }elseif($model_reas=='QS_Surplus' and (($or_share/100)*$basic)<=$max_or){
                if($nilai_klaim) {
                    $this->nilai_klaim_or = ($or_share/100) * $nilai_klaim;
                }
            }elseif($model_reas=='QS_Surplus' and (($or_share/100)*$basic)>$max_or){
                if($nilai_klaim) {
                    $this->nilai_klaim_or = ($max_or/$basic)*$nilai_klaim;
                }
            }else{
                $this->nilai_klaim_or = 0;
            }
            if($nilai_klaim) $this->nilai_klaim_reas = $nilai_klaim - $this->nilai_klaim_or;
        }
        if($this->share_or and $this->nilai_klaim){
            $nilai_klaim = $this->nilai_klaim;
            if($this->nilai_klaim_disetujui) $nilai_klaim = $this->nilai_klaim_disetujui;

            $this->nilai_klaim_reas = $nilai_klaim - ($nilai_klaim * ($this->share_or / 100));
            $this->nilai_klaim_or = $nilai_klaim - $this->nilai_klaim_reas;
            $this->share_reas = 100 - $this->share_or;
        }
    }

    public function submit_ajri()
    {
        $this->validate([
            'status_pengajuan'=>'required'
        ]);
        $this->saveFile();
        $this->data->note_ajri = $this->note_ajri;
        $this->data->status = 2;
        $this->data->status_pengajuan = $this->status_pengajuan;
        $this->data->save();

        $this->emit('message-success','Pengajuan berhasil di submit');
    }
    
    public function approve_ajri()
    {
        $this->saveFile();
        $this->data->note_ajri = $this->note_ajri;
        $this->data->status = 2;
        $this->data->save();

        $this->emit('message-success','Pengajuan berhasil di submit');
    }

    public function reject_ajri()
    {
        $this->saveFile();
        $this->data->note_ajri = $this->note_ajri;
        $this->data->status = 4;
        $this->data->save();

        $this->emit('message-success','Pengajuan berhasil di submit');
    }

    public function saveFile()
    {
        $this->validate([
            'files.*' => 'required|mimes:xls,xlsx,pdf,doc,docx,jpeg,png,jpg,gif,svg|max:2048'
        ],[
            'files.*.required'=>'File required'
        ]);

        foreach($this->files as $k => $file){
            $data = new KlaimFile();
            $data->klaim_id = $this->data->id;
            $data->title = $this->file_title[$k];
            $data->save();

            $name = "{$data->id}.".$file->extension();
            $file->storeAs("public/klaim/{$this->data->id}", $name);
            $data->file = "storage/klaim/{$this->data->id}/{$name}";
            $data->save();
        }
    }

    public function approve_pusat()
    {
        $this->data->note_pusat = $this->note_pusat;
        $this->data->status = 1;
        $this->data->save();

        $this->emit('message-success','Pengajuan berhasil di submit');
    }

    public function reject_pusat()
    {
        $this->data->note_pusat = $this->note_pusat;
        $this->data->status = 3;
        $this->data->save();

        $this->emit('message-success','Pengajuan berhasil di submit');
    }

    public function update_data()
    {
        $this->data->kategori_penyakit = $this->kategori_penyakit;
        $this->data->organ_yang_mencakup = $this->organ_yang_mencakup;
        $this->data->bank_no_rekening = $this->bank_no_rekening;
        $this->data->bank_cabang = $this->bank_cabang;
        $this->data->bank_atas_nama = $this->bank_atas_nama;
        $this->data->bank_mata_uang = $this->bank_mata_uang;
        $this->data->save();

        $this->emit('message-success','Data berhasil disimpan');
    }

    public function save()
    {
        \LogActivity::add("Nilai klaim yang disetujui {$this->data->id}");

        $this->validate([
            'kategori_penyakit'=>'required',
            'organ_yang_mencakup'=>'required'
        ]);

        $this->data->nilai_klaim_reas = $this->nilai_klaim_reas;
        $this->data->nilai_klaim_or = $this->nilai_klaim_or;
        $this->data->nilai_klaim_disetujui = $this->nilai_klaim_disetujui;
        $this->data->organ_yang_mencakup = $this->organ_yang_mencakup;
        $this->data->kategori_penyakit = $this->kategori_penyakit;
        $this->data->bank_no_rekening = $this->bank_no_rekening;
        $this->data->bank_cabang = $this->bank_cabang;
        $this->data->bank_atas_nama = $this->bank_atas_nama;
        $this->data->bank_mata_uang = $this->bank_mata_uang;
        $this->data->kadaluarsa_reas_tanggal = $this->kadaluarsa_reas_tanggal;
        $this->data->share_or = $this->share_or;
        $this->data->share_reas = $this->share_reas;
        $this->data->save();

        $this->emit('message-success','Data berhasil disimpan');
    }

    public function resubmit()
    {
        \LogActivity::add("Klaim {$this->data->id} resubmit");

        $this->data->status = 0;
        $this->data->save();

        $this->emit('message-success','Klaim berhasil di submit');
    }
}
