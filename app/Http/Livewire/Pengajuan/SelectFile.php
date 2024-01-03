<?php

namespace App\Http\Livewire\Pengajuan;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Kepesertaan;

class SelectFile extends Component
{
    public $file,$polis_id;
    use WithFileUploads;

    protected $listeners = ['set-polis'=>'set_polis'];

    public function render()
    {
        return view('livewire.pengajuan.select-file');
    }

    public function set_polis($id)
    {
        $this->polis_id = $id;
    }

    public function upload()
    {
        $this->validate([
            'file'=>'required|mimes:xlsx|max:51200', // 50MB maksimal
        ]);
        
        $path = $this->file->getRealPath();
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(true);
        $xlsx = $reader->load($path);
        $sheetData = $xlsx->getActiveSheet()->toArray();
        $total_data = 0;
        $total_double = 0;
        $total_success = 0;
        Kepesertaan::where(['polis_id'=>$this->polis_id,'is_temp'=>1,'user_id'=>\Auth::user()->id])->delete();
        foreach($sheetData as $key => $item){
            if($key<=1) continue;
            /**
             * Skip
             * Nama, Tanggal lahir
             */
            if($item[1]=="" || $item[10]=="") continue;
            
            $tanggal_lahir = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item[10])->format('Y-m-d');
            $check =  Kepesertaan::where(['polis_id'=>$this->polis_id,'nama'=>$item[1],'tanggal_lahir'=>$tanggal_lahir])->first();
            $data = new Kepesertaan();
            
            if($check){
                $data->is_double = 1;
                $data->parent_id = $check->id;
                $total_double++;
            }

            $data->polis_id = $this->polis_id;
            $data->nama = $item[1];
            $data->no_ktp = $item[2];
            $data->alamat = $item[3];
            $data->no_telepon = $item[4];
            $data->pekerjaan = $item[5];
            $data->bank = $item[6];
            $data->cab = $item[7];
            $data->no_closing = $item[8];
            $data->no_akad_kredit = $item[9];
            if($item[10]) $data->tanggal_lahir = $tanggal_lahir;
            $data->jenis_kelamin = $item[11];
            if($item[12]) $data->tanggal_mulai = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item[12])->format('Y-m-d');
            if($item[13]) $data->tanggal_akhir = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item[13])->format('Y-m-d');
            $data->basic = $item[14];
            $data->tinggi_badan = $item[15];
            $data->berat_badan = $item[16];
            $data->kontribusi = 0;
            $data->is_temp = 1;
            $data->usia = $data->tanggal_lahir ? hitung_umur($data->tanggal_lahir,$this->perhitungan_usia) : '0';
            $data->masa = hitung_masa($data->tanggal_mulai,$data->tanggal_akhir);
            $data->masa_bulan = hitung_masa_bulan($data->tanggal_mulai,$data->tanggal_akhir,$this->masa_asuransi);
            $data->save();
            $total_data++;
        }

        $this->emit('reload-page');
        $this->emit('modal','hide');
    }
}
