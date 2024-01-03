<?php

namespace App\Http\Livewire\Peserta;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Kepesertaan;
use App\Models\Polis;

class Upload extends Component
{
    use WithFileUploads;
    public $data,$file;
    public function render()
    {
        return view('livewire.peserta.upload');
    }

    public function save()
    {
        ini_set('memory_limit', '-1');
        \LogActivity::add('[web] Upload Polis');

        $this->validate([
            'file'=>'required' 
        ]);

        $path = $this->file->getRealPath();
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(true);
        $xlsx = $reader->load($path);
        $sheetData = $xlsx->getActiveSheet()->toArray();
        
        if(count($sheetData) > 0){
            $arr = [];
            $key=0;
            $num=0;
            foreach($sheetData as $item){
                $num++;
                if($num<5) continue;
                
                $nomor_polis = $item[1];
                $nomor_peserta = $item[6];
                $bank = $item[8];
                $no_ktp = $item[13];
                $alamat = $item[14];
                $no_telepon = $item[15];
                $nama = $item[16];
                $tanggal_lahir = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item[17])->format('Y-m-d');
                $usia_masuk = $item[18];
                $jenis_kelamin = $item[19];
                $tanggal_mulai = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item[20])->format('Y-m-d');
                $tanggal_akhir = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item[21])->format('Y-m-d');
                $masa_bulan = $item[22];
                $basic = $item[23];
                $kontribusi = $item[24];
                $dana_tabbaru = $item[25];
                $dana_ujrah = $item[26];
                $extra_kontribusi = $item[27];
                $tgl_stnc = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item[35])->format('Y-m-d');
                $uw = $item[36];

                $find_polis = Polis::where('nomor_polis',$nomor_polis)->first();
                if($find_polis) $arr[$key]['polis_id'] = $find_polis->id;
                
                $arr[$key]['nomor_peserta'] = $nomor_peserta;
                $arr[$key]['bank'] = $bank;
                $arr[$key]['no_ktp'] = $no_ktp;
                $arr[$key]['alamat'] = $alamat;
                $arr[$key]['no_telepon'] = $no_telepon;
                $arr[$key]['nama'] = $nama;
                $arr[$key]['tanggal_lahir'] = $tanggal_lahir;
                $arr[$key]['usia'] = $usia_masuk;
                $arr[$key]['jenis_kelamin'] = $jenis_kelamin;
                $arr[$key]['tanggal_mulai'] = $tanggal_mulai;
                $arr[$key]['tanggal_akhir'] = $tanggal_akhir;
                $arr[$key]['masa_bulan'] = $masa_bulan;
                $arr[$key]['basic'] = $basic;
                $arr[$key]['kontribusi'] = $kontribusi;
                $arr[$key]['dana_tabarru'] = $dana_tabbaru;
                $arr[$key]['dana_ujrah'] = $dana_ujrah;
                $arr[$key]['extra_kontribusi'] = $extra_kontribusi;
                $arr[$key]['tgl_stnc'] = $tgl_stnc;
                $arr[$key]['uw'] = $uw;
                
                $key++;
            }

            Kepesertaan::insert($arr);

            $this->emit('modal','hide');
            $this->emit('reload-page');
        }
    }
}
