<?php

namespace App\Http\Livewire\Klaim;

use Livewire\Component;
use App\Models\Klaim;
use Livewire\WithFileUploads;

class PersetujuanKlaim extends Component
{
    use WithFileUploads;
    public $data,$head_klaim_status,$head_klaim_note,$head_klaim_date,$head_teknik_status,$head_teknik_note,$head_teknik_date,$head_devisi_status,$head_devisi_note,$head_devisi_date;
    public $keputusa_arr = [''=>'-',1=>'Terima',2=>'Tolak',3=>'Tunda',4=>'Investigasi',5=>'Liable',6=>'STNC'];
    public $direksi_1_file,$direksi_1_status,$direksi_1_note,$direksi_1_date,$is_edit_head_klaim=false,$is_edit_head_teknik=false,$is_edit_head_devisi=false;
    public $is_edit_direksi_1=false,$is_edit_direksi_2=false;
    public $direksi_2_file,$direksi_2_status,$direksi_2_note,$direksi_2_date,$detail_penolakan,$edit_detail_penolakan=false;
    public function render()
    {
        return view('livewire.klaim.persetujuan-klaim');
    }

    public function mount(Klaim $id)
    {
        $this->data = $id;
        $this->head_klaim_status = $id->head_klaim_status;
        $this->head_klaim_note = $id->head_klaim_note;
        $this->head_klaim_date = $id->head_klaim_date;
        $this->head_teknik_status = $id->head_teknik_status;
        $this->head_teknik_note = $id->head_teknik_note;
        $this->head_teknik_date = $id->head_teknik_date;
        $this->head_devisi_status = $id->head_devisi_status;
        $this->head_devisi_note = $id->head_devisi_note;
        $this->head_devisi_date = $id->head_devisi_date;
        $this->direksi_1_file = $id->direksi_1_file;
        $this->direksi_1_status = $id->direksi_1_status;
        $this->direksi_1_note = $id->direksi_1_note;
        $this->direksi_1_date = $id->direksi_1_date;
        $this->direksi_2_file = $id->direksi_2_file;
        $this->direksi_2_status = $id->direksi_2_status;
        $this->direksi_2_note = $id->direksi_2_note;
        $this->direksi_2_date = $id->direksi_2_date;
        $this->detail_penolakan = $id->detail_penolakan;
    }

    public function saveDetailPenolakan()
    {
        $this->data->detail_penolakan = $this->detail_penolakan;
        $this->data->save();
        $this->edit_detail_penolakan = false;
    }

    public function save_head_klaim()
    {
        $this->validate([
            'head_klaim_status'=>'required',
            'head_klaim_note'=>'required'
        ]);
        
        $this->data->head_klaim_status = $this->head_klaim_status;
        $this->data->head_klaim_note = $this->head_klaim_note;

        /**
         * Jika nilai klaim dibawah 50 jt maka langsung approve oleh Head Departemen Claim Unit Syariah
         *
        if($this->data->nilai_klaim_disetujui<=50000000){
            $this->data->status = 3;
            $this->data->status_pengajuan = $this->head_klaim_status;
        }
        */

        if($this->is_edit_head_klaim==false){
            $this->data->status = 1;
            $this->data->head_klaim_date = date('Y-m-d');
            $this->data->head_klaim_id = \Auth::user()->id;
        }
        $this->data->save();

        session()->flash('message-success',__('Data berhasil di submit'));

        event(new \App\Events\GeneralNotification(\Auth::user()->name."<br />Klaim {$this->data->no_pengajuan} submitted"));

        \LogActivity::add("Klaim Submit Head Klaim {$this->data->id}");
    }

    public function save_head_teknik()
    {
        $this->validate([
            'head_teknik_status'=>'required',
            'head_teknik_note'=>'required'
        ]);

        if($this->is_edit_head_teknik==false){
            $this->data->status = 2;    
            $this->data->head_teknik_date = date('Y-m-d');
            $this->data->head_teknik_id = \Auth::user()->id;
        }
        
        $this->data->head_teknik_status = $this->head_teknik_status;
        $this->data->head_teknik_note = $this->head_teknik_note;
        $this->data->save();

        session()->flash('message-success',__('Data berhasil di submit'));

        event(new \App\Events\GeneralNotification(\Auth::user()->name."<br />Klaim {$this->data->no_pengajuan} submitted"));

        \LogActivity::add("Klaim Submit Head Teknik {$this->data->id}");
    }

    public function save_devisi_syariah()
    {
        $this->validate([
            'head_devisi_status'=>'required',
            'head_devisi_note'=>'required'
        ]);

        if($this->data->nilai_klaim_disetujui<=150000000){
            $this->data->status_pengajuan = $this->head_devisi_status;
            $this->data->status = 3;
            $this->data->jatuh_tempo = date('Y-m-d',strtotime("+{$this->data->kepesertaan->polis->pembayaran_klaim} days"));
        }else{
            $this->data->status = 5;
        }

        $this->data->head_devisi_status = $this->head_devisi_status;
        $this->data->head_devisi_note = $this->head_devisi_note;
        $this->data->head_devisi_date = date('Y-m-d');
        $this->data->head_devisi_id = \Auth::user()->id;
        $this->data->detail_penolakan = $this->detail_penolakan;
        $this->data->save();
        $this->is_edit_head_devisi = false;

        event(new \App\Events\GeneralNotification(\Auth::user()->name."<br />Klaim {$this->data->no_pengajuan} submitted"));

        session()->flash('message-success',__('Data berhasil di submit'));

        \LogActivity::add("Klaim Submit Devisi Syariah {$this->data->id}");

        // return redirect()->route('klaim.edit',$this->data->id);
    }

    public function save_direksi1()
    {
        $this->validate([
            'direksi_1_file' => 'required|mimes:xls,xlsx,pdf,doc,docx,jpeg,png,jpg,gif,svg|max:2048',
            'direksi_1_status'=>'required'
        ]);

        if($this->data->nilai_klaim_disetujui>200000000)
            $this->data->status = 5;
        else{
            $this->data->status_pengajuan = $this->direksi_1_status;
            $this->data->status = 3;
        }
        
        $name = "direksi_1.".$this->direksi_1_file->extension();
        $this->direksi_1_file->storeAs("public/klaim/{$this->data->id}", $name);
        $this->data->direksi_1_file = "storage/klaim/{$this->data->id}/{$name}";
        $this->data->direksi_1_status = $this->direksi_1_status;
        $this->data->direksi_1_note = $this->direksi_1_note;
        $this->data->direksi_1_date = date('Y-m-d');
        $this->data->save();

        \LogActivity::add("Klaim Submit Direksi 1 {$this->data->id}");

        session()->flash('message-success',__('Data berhasil di submit'));

        event(new \App\Events\GeneralNotification(\Auth::user()->name."<br />Klaim {$this->data->no_pengajuan} submitted"));

        // return redirect()->route('klaim.edit',$this->data->id);
    }

    public function save_direksi2()
    {
        $this->validate([
            'direksi_2_file' => 'required|mimes:xls,xlsx,pdf,doc,docx,jpeg,png,jpg,gif,svg|max:2048',
            'direksi_2_status'=>'required'
        ]);

        $this->data->status = 3;
        $this->data->status_pengajuan = $this->direksi_2_status;

        $name = "direksi_2.".$this->direksi_2_file->extension();
        $this->direksi_2_file->storeAs("public/klaim/{$this->data->id}", $name);
        $this->data->direksi_2_file = "storage/klaim/{$this->data->id}/{$name}";
        $this->data->direksi_2_status = $this->direksi_2_status;
        $this->data->direksi_2_note = $this->direksi_2_note;
        $this->data->direksi_2_date = date('Y-m-d');
        $this->data->save();

        \LogActivity::add("Klaim Submit Direksi 2 {$this->data->id}");

        session()->flash('message-success',__('Data berhasil di submit'));

        event(new \App\Events\GeneralNotification(\Auth::user()->name."<br />Klaim {$this->data->no_pengajuan} submitted"));

        // return redirect()->route('klaim.edit',$this->data->id);
    }
}
