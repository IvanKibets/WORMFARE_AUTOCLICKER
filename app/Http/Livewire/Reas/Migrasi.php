<?php

namespace App\Http\Livewire\Reas;

use Livewire\Component;
use Livewire\WithFileUploads;

class Migrasi extends Component
{
    public $file;
    use WithFileUploads;
    public function render()
    {
        return view('livewire.reas.migrasi');
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
        Kepesertaan::where(['polis_id'=>$this->polis_id,'is_temp'=>1])->delete();
        foreach($sheetData as $key => $item){
            if($key<=5) continue;

            if($item[1]=="" || $item[10]=="") continue;

            dd($item);

            $no_polis = $item[1];
            $tanggal_lahir = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item[10])->format('Y-m-d');

            $total_data++;
        }

        $this->emit('reload-page');
        $this->emit('modal','hide');
    }
}
