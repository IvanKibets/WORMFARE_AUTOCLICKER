<?php

namespace App\Http\Livewire\Klaim;

use Livewire\Component;
use Livewire\WithFileUploads;

class DokumenPendukungInsert extends Component
{
    use WithFileUploads;

    public $formulir_pengajuan_klaim,$surat_keterangan_meninggal_kelurahan,$copy_ktp,$copy_ktp_ahli_waris,$resume_medis,$daftar_angsuran;
    public $copy_akad_pembiayaan,$surat_kuasa,$surat_keterangan_ahli_waris,$surat_dari_pemegang_polis,$dokumen_lain;
    public function render()
    {
        return view('livewire.klaim.dokumen-pendukung-insert');
    }
}
