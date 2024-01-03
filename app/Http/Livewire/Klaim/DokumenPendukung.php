<?php

namespace App\Http\Livewire\Klaim;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Klaim;

class DokumenPendukung extends Component
{
    use WithFileUploads;
    public $data,$formulir_pengajuan_klaim,$surat_keterangan_meninggal_kelurahan,$surat_keterangan_meninggal_rs,$copy_ktp;
    public $copy_ktp_ahli_waris,$resume_medis,$daftar_angsuran,$copy_akad_pembiayaan,$surat_kuasa,$surat_keterangan_ahli_waris,$surat_dari_pemegang_polis;
    public $dokumen_lain,$dokumen_lain_keterangan;
    protected $listeners = ['reload-page'=>'$refresh'];
    public function render()
    {
        return view('livewire.klaim.dokumen-pendukung');
    }

    public function mount(Klaim $id)
    {
        $this->data = $id;
        $this->dokumen_lain_keterangan = $this->data->dokumen_lain_keterangan;
    }

    public function updated($propertyName)
    {
        if($this->formulir_pengajuan_klaim){
            $this->validate([
                'formulir_pengajuan_klaim' => 'required|mimes:xls,xlsx,pdf,doc,docx,jpeg,png,jpg,gif,svg|max:2048'
            ]);
    
            $name = "formulir_pengajuan_klaim.".$this->formulir_pengajuan_klaim->extension();
            $this->formulir_pengajuan_klaim->storeAs("public/klaim/{$this->data->id}", $name);
            $this->data->formulir_pengajuan_klaim = "storage/klaim/{$this->data->id}/{$name}";
            $this->data->save();
            \LogActivity::add("Upload Dokument Klaim {$this->data->id}");
            $this->emit('reload-page');
        }
        if($this->surat_keterangan_meninggal_kelurahan){
            $this->validate([
                'surat_keterangan_meninggal_kelurahan' => 'required|mimes:xls,xlsx,pdf,doc,docx,jpeg,png,jpg,gif,svg|max:2048'
            ]);
    
            $name = "surat_keterangan_meninggal_kelurahan.".$this->surat_keterangan_meninggal_kelurahan->extension();
            $this->surat_keterangan_meninggal_kelurahan->storeAs("public/klaim/{$this->data->id}", $name);
            $this->data->surat_keterangan_meninggal_kelurahan = "storage/klaim/{$this->data->id}/{$name}";
            $this->data->save();
            \LogActivity::add("Upload Dokument Klaim {$this->data->id}");
            $this->emit('reload-page');
        }
        if($this->surat_keterangan_meninggal_rs){
            $this->validate([
                'surat_keterangan_meninggal_rs' => 'required|mimes:xls,xlsx,pdf,doc,docx,jpeg,png,jpg,gif,svg|max:2048'
            ]);
    
            $name = "surat_keterangan_meninggal_rs.".$this->surat_keterangan_meninggal_rs->extension();
            $this->surat_keterangan_meninggal_rs->storeAs("public/klaim/{$this->data->id}", $name);
            $this->data->surat_keterangan_meninggal_rs = "storage/klaim/{$this->data->id}/{$name}";
            $this->data->save();
            \LogActivity::add("Upload Dokument Klaim {$this->data->id}");
            $this->emit('reload-page');
        }
        if($this->copy_ktp){
            $this->validate([
                'copy_ktp' => 'required|mimes:xls,xlsx,pdf,doc,docx,jpeg,png,jpg,gif,svg|max:2048'
            ]);
    
            $name = "copy_ktp.".$this->copy_ktp->extension();
            $this->copy_ktp->storeAs("public/klaim/{$this->data->id}", $name);
            $this->data->copy_ktp = "storage/klaim/{$this->data->id}/{$name}";
            $this->data->save();
            \LogActivity::add("Upload Dokument Klaim {$this->data->id}");
            $this->emit('reload-page');
        }
        if($this->copy_ktp_ahli_waris){
            $this->validate([
                'copy_ktp_ahli_waris' => 'required|mimes:xls,xlsx,pdf,doc,docx,jpeg,png,jpg,gif,svg|max:2048'
            ]);
    
            $name = "copy_ktp_ahli_waris.".$this->copy_ktp_ahli_waris->extension();
            $this->copy_ktp_ahli_waris->storeAs("public/klaim/{$this->data->id}", $name);
            $this->data->copy_ktp_ahli_waris = "storage/klaim/{$this->data->id}/{$name}";
            $this->data->save();
            \LogActivity::add("Upload Dokument Klaim {$this->data->id}");
            $this->emit('reload-page');
        }
        if($this->resume_medis){
            $this->validate([
                'resume_medis' => 'required|mimes:xls,xlsx,pdf,doc,docx,jpeg,png,jpg,gif,svg|max:2048'
            ]);
    
            $name = "resume_medis.".$this->resume_medis->extension();
            $this->resume_medis->storeAs("public/klaim/{$this->data->id}", $name);
            $this->data->resume_medis = "storage/klaim/{$this->data->id}/{$name}";
            $this->data->save();
            \LogActivity::add("Upload Dokument Klaim {$this->data->id}");
            $this->emit('reload-page');
        }
        if($this->daftar_angsuran){
            $this->validate([
                'daftar_angsuran' => 'required|mimes:xls,xlsx,pdf,doc,docx,jpeg,png,jpg,gif,svg|max:2048'
            ]);
    
            $name = "daftar_angsuran.".$this->daftar_angsuran->extension();
            $this->daftar_angsuran->storeAs("public/klaim/{$this->data->id}", $name);
            $this->data->daftar_angsuran = "storage/klaim/{$this->data->id}/{$name}";
            $this->data->save();
            \LogActivity::add("Upload Dokument Klaim {$this->data->id}");
            $this->emit('reload-page');
        }
        if($this->copy_akad_pembiayaan){
            $this->validate([
                'copy_akad_pembiayaan' => 'required|mimes:xls,xlsx,pdf,doc,docx,jpeg,png,jpg,gif,svg|max:2048'
            ]);
    
            $name = "copy_akad_pembiayaan.".$this->copy_akad_pembiayaan->extension();
            $this->copy_akad_pembiayaan->storeAs("public/klaim/{$this->data->id}", $name);
            $this->data->copy_akad_pembiayaan = "storage/klaim/{$this->data->id}/{$name}";
            $this->data->save();
            \LogActivity::add("Upload Dokument Klaim {$this->data->id}");
            $this->emit('reload-page');
        }
        if($this->surat_kuasa){
            $this->validate([
                'surat_kuasa' => 'required|mimes:xls,xlsx,pdf,doc,docx,jpeg,png,jpg,gif,svg|max:2048'
            ]);
    
            $name = "surat_kuasa.".$this->surat_kuasa->extension();
            $this->surat_kuasa->storeAs("public/klaim/{$this->data->id}", $name);
            $this->data->surat_kuasa = "storage/klaim/{$this->data->id}/{$name}";
            $this->data->save();
            \LogActivity::add("Upload Dokument Klaim {$this->data->id}");
            $this->emit('reload-page');
        }
        if($this->surat_keterangan_ahli_waris){
            $this->validate([
                'surat_keterangan_ahli_waris' => 'required|mimes:xls,xlsx,pdf,doc,docx,jpeg,png,jpg,gif,svg|max:2048'
            ]);
    
            $name = "surat_keterangan_ahli_waris.".$this->surat_keterangan_ahli_waris->extension();
            $this->surat_keterangan_ahli_waris->storeAs("public/klaim/{$this->data->id}", $name);
            $this->data->surat_keterangan_ahli_waris = "storage/klaim/{$this->data->id}/{$name}";
            $this->data->save();
            \LogActivity::add("Upload Dokument Klaim {$this->data->id}");
            $this->emit('reload-page');
        }
        if($this->surat_dari_pemegang_polis){
            $this->validate([
                'surat_dari_pemegang_polis' => 'required|mimes:xls,xlsx,pdf,doc,docx,jpeg,png,jpg,gif,svg|max:2048'
            ]);
    
            $name = "surat_dari_pemegang_polis.".$this->surat_dari_pemegang_polis->extension();
            $this->surat_dari_pemegang_polis->storeAs("public/klaim/{$this->data->id}", $name);
            $this->data->surat_dari_pemegang_polis = "storage/klaim/{$this->data->id}/{$name}";
            $this->data->save();
            \LogActivity::add("Upload Dokument Klaim {$this->data->id}");
            $this->emit('reload-page');
        }

        if($this->dokumen_lain){
            $this->validate([
                'dokumen_lain' => 'required|mimes:xls,xlsx,pdf,doc,docx,jpeg,png,jpg,gif,svg|max:2048'
            ]);
    
            $name = "dokumen_lain.".$this->dokumen_lain->extension();
            $this->dokumen_lain->storeAs("public/klaim/{$this->data->id}", $name);
            $this->data->dokumen_lain = "storage/klaim/{$this->data->id}/{$name}";
            $this->data->save();
            \LogActivity::add("Upload Dokument Klaim {$this->data->id}");
            $this->emit('reload-page');
        }
        if($this->dokumen_lain_keterangan){
            $this->data->dokumen_lain_keterangan = $this->dokumen_lain_keterangan;
            $this->data->save();
            \LogActivity::add("Upload Dokument Klaim {$this->data->id}");
        }
    }

    public function dokumen_lengkap()
    {
        $this->data->tanggal_dok_lengkap = date('Y-m-d');
        $this->data->save();

        \LogActivity::add("Dokumen Klaim Lengkap {$this->data->id}");

        session()->flash('message-success',__('Dokumen berhasil di submit'));

        return redirect()->route('klaim.edit',$this->data->id);
    }
}
