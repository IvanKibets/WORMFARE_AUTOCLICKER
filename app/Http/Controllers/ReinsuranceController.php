<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Kepesertaan;
use App\Models\Reas;

class ReinsuranceController extends Controller
{
    public function data()
    {
        $data = Income::select('income.*')->where('reference_type','Reinsurance Commision')
                        ->orderBy('income.id','DESC')
                        ;
        if(isset($_GET['term']))$data->where(function($table){
                $table->where('reference_no','LIKE',"%{$_GET['term']}%")
                        ->orWhere('client','LIKE',"%{$_GET['term']}%")
                        ->orWhere('no_polis','LIKE',"%{$_GET['term']}%")
                        ->orWhere('nominal','LIKE',"%{$_GET['term']}%")
                        ;
            });

        $temp = [];
        foreach($data->offset(0)->limit(10)->get() as $k => $item){
            $temp[$k] = $item;
            $temp[$k]['nominal'] = format_idr($item->nominal);
        }
        return response()->json($temp, 200);
    }

    public function premium()
    {
        $data = Expenses::orderBy('id','desc')->where('reference_type','Reinsurance Premium')
                            ->where('status',1);
        if(isset($_GET['term'])) $data = $data->where(function($table){
                                        $table->where('description','LIKE', "%{$_GET['term']}%")
                                        ->orWhere('no_voucher','LIKE',"%{$_GET['term']}%")
                                        ->orWhere('reference_no','LIKE',"%{$_GET['term']}%")
                                        ->orWhere('recipient','LIKE',"%{$_GET['term']}%")
                                        ;
                                    });

        $temp = [];
        foreach($data->offset(0)->limit(10)->get() as $k => $item){
            $temp[$k] = $item;
            $temp[$k]['nominal'] = format_idr($item->nominal);
        }
        return response()->json($temp, 200);
    }

    public function downloadReport(Reas $id)
    {
        $objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("PMT System")
                                    ->setLastModifiedBy("Stalavista System")
                                    ->setTitle("Office 2007 XLSX Product Database")
                                    ->setSubject("Reas")
                                    ->setDescription("Reas")
                                    ->setKeywords("office 2007 openxml php")
                                    ->setCategory("Reas");

        $activeSheet = $objPHPExcel->setActiveSheetIndex(0);
        $activeSheet->getStyle('A1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('689a3b');
        $activeSheet->setCellValue('B1', 'PT. Asuransi Jiwa Reliance Indonesia Unit Syariah');
        $activeSheet->mergeCells("B1:D1");
        $activeSheet->getRowDimension('1')->setRowHeight(34);
        $activeSheet->getStyle('B1')->getFont()->setSize(16);
        $activeSheet->getStyle('B1')->getAlignment()->setWrapText(false);
        $activeSheet->getStyle('A4:X4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('c2d7f3');
        $activeSheet
                    ->setCellValue('A4', 'No')
                    ->setCellValue('B4', 'No Polis')
                    ->setCellValue('C4', 'Nama Pemegang Polis')
                    ->setCellValue('D4', 'No Peserta')
                    ->setCellValue('E4', 'Nama Peserta')
                    ->setCellValue('F4', 'Gender')
                    ->setCellValue('G4', 'Tanggal Lahir')
                    ->setCellValue('H4', 'Usia')
                    ->setCellValue('I4', 'Mulai Asuransi')
                    ->setCellValue('J4', 'Akhir Asuransi')
                    ->setCellValue('K4', 'Jangka Waktu Asuransi')
                    ->setCellValue('L4', 'Manfaat Asuransi')
                    ->setCellValue('M4', 'Manfaat Asuransi Reas')
                    ->setCellValue('N4', 'Manfaat Asuransi Ajri')
                    ->setCellValue('O4', 'Manfaat')
                    ->setCellValue('P4', 'Type Reas')
                    ->setCellValue('Q4', 'Rate')
                    ->setCellValue('R4', 'Kontribusi Reas')
                    ->setCellValue('S4', 'Extra Kontribusi')
                    ->setCellValue('T4', 'Ujroh')
                    ->setCellValue('U4', 'Kontribusi Netto')
                    ->setCellValue('V4', 'Akseptasi')
                    ->setCellValue('W4', 'Kontribusi AJRI')
                    ->setCellValue('X4', 'UW Limit')
                    ;

        $activeSheet->getColumnDimension('A')->setWidth(5);
        $activeSheet->getColumnDimension('B')->setAutoSize(true);
        $activeSheet->getColumnDimension('C')->setAutoSize(true);
        $activeSheet->getColumnDimension('D')->setAutoSize(true);
        $activeSheet->getColumnDimension('E')->setAutoSize(true);
        $activeSheet->getColumnDimension('F')->setAutoSize(true);
        $activeSheet->getColumnDimension('G')->setAutoSize(true);
        $activeSheet->getColumnDimension('H')->setAutoSize(true);
        $activeSheet->getColumnDimension('I')->setAutoSize(true);
        $activeSheet->getColumnDimension('J')->setAutoSize(true);
        $activeSheet->getColumnDimension('K')->setAutoSize(true);
        $activeSheet->getColumnDimension('L')->setAutoSize(true);
        $activeSheet->getColumnDimension('M')->setAutoSize(true);
        $activeSheet->getColumnDimension('N')->setAutoSize(true);
        $activeSheet->getColumnDimension('O')->setAutoSize(true);
        $activeSheet->getColumnDimension('Q')->setAutoSize(true);
        $activeSheet->getColumnDimension('R')->setAutoSize(true);
        $activeSheet->getColumnDimension('S')->setAutoSize(true);
        $activeSheet->getColumnDimension('T')->setAutoSize(true);
        $activeSheet->getColumnDimension('U')->setAutoSize(true);
        $activeSheet->getColumnDimension('V')->setAutoSize(true);
        $activeSheet->getColumnDimension('W')->setAutoSize(true);
        $activeSheet->getColumnDimension('X')->setAutoSize(true);
        $num=5;

        $data = Kepesertaan::with(['pengajuan','polis'])->where(['reas_id'=>$id->id,'status_akseptasi'=>1])->where('status_reas',1)->get();

        foreach($data as $k => $item){
            $activeSheet
                ->setCellValue('A'.$num,($k+1))
                ->setCellValue('B'.$num,isset($item->polis->no_polis) ? $item->polis->no_polis : '-')
                ->setCellValue('C'.$num,isset($item->polis->nama) ? $item->polis->nama : '-')
                ->setCellValue('D'.$num,$item->no_peserta)
                ->setCellValue('E'.$num,$item->nama)
                ->setCellValue('F'.$num,$item->jenis_kelamin)
                ->setCellValue('G'.$num,date('d-m-Y',strtotime($item->tanggal_lahir)))
                ->setCellValue('H'.$num,$item->usia_reas)
                ->setCellValue('I'.$num,date('d-m-Y',strtotime($item->tanggal_mulai)))
                ->setCellValue('J'.$num,date('d-m-Y',strtotime($item->tanggal_akhir)))
                ->setCellValue('K'.$num,$item->masa_bulan)
                ->setCellValue('L'.$num,$item->basic)
                ->setCellValue('M'.$num,$item->nilai_manfaat_asuransi_reas)
                ->setCellValue('N'.$num,$item->reas_manfaat_asuransi_ajri)
                ->setCellValue('O'.$num,$item->reas_manfaat)
                ->setCellValue('P'.$num,$item->reas_type)
                ->setCellValue('Q'.$num,$item->rate_reas)
                ->setCellValue('R'.$num,$item->total_kontribusi_reas)
                ->setCellValue('S'.$num,$item->reas_extra_kontribusi)
                ->setCellValue('T'.$num,$item->ujroh_reas)
                ->setCellValue('U'.$num,$item->net_kontribusi_reas)
                ->setCellValue('V'.$num,$item->ul_reas)
                ->setCellValue('W'.$num,$item->extra_mortalita+$item->kontribusi+$item->extra_kontribusi)
                ->setCellValue('X'.$num,$item->ul);

            $num++;
        }

        // Rename worksheet
        $activeSheet->setTitle('Reas');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, "Xlsx");

        // Redirect output to a client’s web browser (Excel5)
        //header('Content-Type: application/vnd.ms-excel');
        //header('Content-Disposition: attachment;filename="reas_'.$id->no_pengajuan.'.xlsx"');
        //header('Cache-Control: max-age=0');
        // If you're serving to IE 9, then the following may be needed
        //header('Cache-Control: max-age=1');

        // If you're serving to IE over SSL, then the following may be needed
        //header ('Expires: Mon, 26 Jul 1997 05:00:00 GMT'); // Date in the past
        //header ('Last-Modified: '.gmdate('D, d M Y H:i:s').' GMT'); // always modified
        //header ('Cache-Control: cache, must-revalidate'); // HTTP/1.1
        //header ('Pragma: public'); // HTTP/1.0
        //return TRUE;
        return response()->streamDownload(function() use($writer){
            $writer->save('php://output');
        },'reas_'.$id->no_pengajuan.'.xlsx');
    }
}
