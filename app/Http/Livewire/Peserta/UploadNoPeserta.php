<?php

namespace App\Http\Livewire\Peserta;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Kepesertaan;
use App\Models\Polis;

class UploadNoPeserta extends Component
{
    use WithFileUploads;
    public $data,$file;
    public function render()
    {
        return view('livewire.peserta.upload-no-peserta');
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
                if($num<=1 || $item[1]=="") continue;
            
                $nama = $item[1];
                $tanggal_lahir = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item[2])->format('Y-m-d');
                $nomor_peserta = $item[3];
                $peserta = Kepesertaan::with('pengajuan')->where(['nama'=>$nama,'tanggal_lahir'=>$tanggal_lahir,''])->first();
                if($peserta){
                    
                    if($peserta->pengajuan->status !=5) continue;

                    $peserta->no_peserta = $nomor_peserta;
                    $peserta->save();
                }

                $key++;
            }


            $this->emit('modal','hide');
            $this->emit('reload-page');
        }
    }

    public function downloadTemplate()
    {
        $objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Entigi System")
                                    ->setLastModifiedBy("Entigi System")
                                    ->setTitle("Office 2007 XLSX Product Database")
                                    ->setSubject("Peserta")
                                    ->setKeywords("office 2007 openxml php");
        
        $activeSheet = $objPHPExcel->setActiveSheetIndex(0);
        $activeSheet
                ->setCellValue('A1', 'NO')
                ->setCellValue('B1', 'NAMA')
                ->setCellValue('C1', 'TANGGAL LAHIR')
                ->setCellValue('D1', 'NOMOR PESERTA');

        $activeSheet->getColumnDimension('A')->setWidth(5);
        $activeSheet->getColumnDimension('B')->setAutoSize(true);
        $activeSheet->getColumnDimension('C')->setAutoSize(true);
        $activeSheet->getColumnDimension('D')->setAutoSize(true);
            
        // Rename worksheet
        // $activeSheet->setTitle('Pengajuan');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, "Xlsx");

        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="template-upload-no-peserta.xlsx"');
        header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        //header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        header ('Pragma: public'); // HTTP/1.0
        return response()->streamDownload(function() use($writer){
            $writer->save('php://output');
        },'template-upload-no-peserta.xlsx');
    }
}
