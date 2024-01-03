<?php

namespace App\Http\Livewire\Klaim;

use Livewire\Component;
use App\Models\Klaim;

class AnalisaKlaim extends Component
{
    public $data,$sumber_informasi,$sebab_meninggal,$riwayat_penyakit,$tempat_meninggal,$verifikasi_via_telpon,$analisa_medis,$kesimpulan;
    public function render()
    {
        return view('livewire.klaim.analisa-klaim');
    }

    public function mount(Klaim $id)
    {
        $this->data = $id;
        $this->sumber_informasi = $id->sumber_informasi;
        $this->sebab_meninggal = $id->sebab_meninggal;
        $this->riwayat_penyakit = $id->riwayat_penyakit;
        $this->tempat_meninggal = $id->tempat_meninggal;
        $this->verifikasi_via_telpon = $id->verifikasi_via_telpon;
        $this->analisa_medis = $id->analisa_medis;
        $this->kesimpulan = $id->kesimpulan;
    }

    public function save()
    {
        $this->validate([
            'sumber_informasi' => 'required',
            'sebab_meninggal' => 'required',
            'riwayat_penyakit' => 'required',
            'tempat_meninggal' => 'required',
            'verifikasi_via_telpon' => 'required',
            'analisa_medis' => 'required',
            'kesimpulan' => 'required'
        ]);

        $this->data->sumber_informasi = $this->sumber_informasi;
        $this->data->sebab_meninggal = $this->sebab_meninggal;
        $this->data->riwayat_penyakit = $this->riwayat_penyakit;
        $this->data->tempat_meninggal = $this->tempat_meninggal;
        $this->data->verifikasi_via_telpon = $this->verifikasi_via_telpon;
        $this->data->analisa_medis = $this->analisa_medis;
        $this->data->kesimpulan = $this->kesimpulan;
        $this->data->save();

        \LogActivity::add("Analisa Klaim {$this->data->id}");

        session()->flash('message-success',__('Analisa klaim berhasil di submit'));

        return redirect()->route('klaim.edit',$this->data->id);
    }
}
