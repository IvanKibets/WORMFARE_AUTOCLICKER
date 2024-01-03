<?php

namespace App\Http\Livewire\Polis;

use Livewire\Component;
use App\Models\Polis;
use App\Models\Kepesertaan;
use App\Models\Rate;
use Livewire\WithFileUploads;

class UploadKepesertaan extends Component
{
    use WithFileUploads;
    public $data,$file;
    public function render()
    {
        return view('livewire.polis.upload-kepesertaan');
    }

    public function mount(Polis $data)
    {
        $this->data = $data;
    }

    public function save()
    {
        \LogActivity::add('[web] Upload Kepesertaan');
        $this->validate([
            'file'=>'required|mimes:xlsx|max:51200' // 50MB maksimal
        ]);
        
        $path = $this->file->getRealPath();
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(true);
        $xlsx = $reader->load($path);
        $sheetData = $xlsx->getActiveSheet()->toArray();
        
        if(count($sheetData) > 0){
            $countLimit = 1;
            $total_double = 0;
            $total_success = 0;
            
            Kepesertaan::where(['polis_id'=>$this->data->id,'is_double'=>1])->delete();

            foreach($sheetData as $key => $item){
                if($key<=1) continue;
                /**
                 * Skip
                 * Nama, Tanggal lahir
                 */
                if($item[1]=="" || $item[10]=="") continue;
                
                $tanggal_lahir = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item[10])->format('Y-m-d');

                $check =  Kepesertaan::where(['polis_id'=>$this->data->id,'nama'=>$item[1],'tanggal_lahir'=>$tanggal_lahir])->first();
                
                $data = new Kepesertaan();
                
                if($check){
                    $data->is_double = 1;
                    $data->parent_id = $check->id;
                    $total_double++;
                }

                $data->polis_id = $this->data->id;
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
                $data->sub_basic1 = $item[15];
                $data->sub_basic2 = $item[16];
                $data->sub_basic3 = $item[17];
                $data->rider1 = $item[18];
                $data->rider2 = $item[19];
                $data->rider3 = $item[20];
                $data->usia = $data->tanggal_lahir ? hitung_umur($data->tanggal_lahir) : '0';
                $data->masa = hitung_masa($data->tanggal_mulai,$data->tanggal_akhir);
                $data->masa_bulan = hitung_masa_bulan($data->tanggal_mulai,$data->tanggal_akhir);
                $data->kontribusi = 0;
                $data->save();
            }
        }

        if($total_double>0){
            $this->emit('reload_data');
            $this->emit('modal','#modal_check_double');
        }else{
            $this->emit('modal','hide');
            $this->emit('reload-page');
        }
    }
}