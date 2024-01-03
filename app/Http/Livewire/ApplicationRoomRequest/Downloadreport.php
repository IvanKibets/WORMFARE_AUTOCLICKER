<?php

namespace App\Http\Livewire\ApplicationRoomRequest;

use Livewire\Component;
use Livewire\WithFileUploads;
use Auth;
use DB;

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

// use Maatwebsite\Excel\Concerns\Exportable;
// use Illuminate\Contracts\View\View;
// use Maatwebsite\Excel\Concerns\FromView;


class Downloadreport extends Component
{

    use WithFileUploads;
    public $month_from;
    public $month_to;
    public $year_from;
    public $year_to;
    // public $year;
    

    
    public function render()
    {

                       
        $year = \App\Models\DutyrosterSitelistDetail::where('id_master_dutyroster', '4')->get();
                                  
        
        return view('livewire.dana-stpl.downloadreport')->with('year', $year);
    }

    public function downloadreport($id)
    {
        $this->selected_id = $id;

        
    }
  
    public function save()
    {
        // dd("download");
        $month_from = $this->month_from;
        $month_to = $this->month_to;
        $year_from = $this->year_from;
        $year_to = $this->year_to;

       

        $objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Stalavista System")
                                    ->setLastModifiedBy("Stalavista System")
                                    ->setTitle("Office 2007 XLSX Product Database")
                                    ->setSubject("Data Member")
                                    ->setDescription("Data Member")
                                    ->setKeywords("office 2007 openxml php")
                                    ->setCategory("Member");

        $objPHPExcel->getActiveSheet()->getStyle('A1:B1')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('c2d7f3');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('A1', 'Account');
        $objPHPExcel->setActiveSheetIndex(0)->setCellValue('B1', '(Multiple Items)');
        // $objPHPExcel->getActiveSheet()->getStyle('A1')->getFont()->setSize(16);
        $objPHPExcel->getActiveSheet()->getStyle('A1')->getAlignment()->setWrapText(false);
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A3', 'Sum of Withdrawal')
                    ->setCellValue('B3', 'Column Labels');
        $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A4', 'Row Labels')
                    ->setCellValue('B4', 'CMI')
                    ->setCellValue('C4', 'H3I')
                    ->setCellValue('D4', 'ISAT')
                    ->setCellValue('E4', 'STPL')
                    ->setCellValue('F4', 'XL')
                    ->setCellValue('G4', 'Grand Total');
                    
        $objPHPExcel->getActiveSheet()->getStyle('A3:G3')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('c2d7f3');
        $objPHPExcel->getActiveSheet()->getStyle('A3:G3')->getFont()->setBold( true );
        $objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getFill()->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)->getStartColor()->setRGB('c2d7f3');
        $objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getFont()->setBold( true );
        $objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setVertical(\PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER);
        $objPHPExcel->getActiveSheet()->getStyle('A4:G4')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
        // $objPHPExcel->getActiveSheet()->getRowDimension('3')->setRowHeight(34);
        $objPHPExcel->getActiveSheet()->getColumnDimension('A')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('B')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('C')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('D')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('E')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('F')->setAutoSize(true);
        $objPHPExcel->getActiveSheet()->getColumnDimension('G')->setAutoSize(true);
        
        //$objPHPExcel->getActiveSheet()->freezePane('A4');
        $objPHPExcel->getActiveSheet()->setAutoFilter('B3:B3');
        $objPHPExcel->getActiveSheet()->setAutoFilter('A4:A4');
        $num=5;

        $yeartotal = \App\Models\DanaStpl::select(DB::Raw('year(dana_stpl_master.created_at) as year'), DB::Raw('sum(cmi) as t_cmi'), DB::Raw('sum(h3i) as t_h3i'), DB::Raw('sum(isat) as t_isat'), DB::Raw('sum(stp) as t_stp'), DB::Raw('sum(xl) as t_xl'), DB::Raw('sum(danastpl) as grandtotal'))
                                                ->where(DB::Raw('year(created_at)'), '>=', $year_from)
                                                ->where(DB::Raw('year(created_at)'), '<=', $year_to)
                                                ->where('status','3')
                                                ->groupBy(DB::Raw('year(created_at)'))
                                                ->orderBy(DB::Raw('year(created_at)'), 'asc')
                                                ->get();

        // dd($yeartotal);
        foreach($yeartotal as $k => $item){
            $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$num,$item->year)
                    ->setCellValue('B'.$num,$item->t_cmi)
                    ->setCellValue('C'.$num,$item->t_h3i)
                    ->setCellValue('D'.$num,$item->t_isat)
                    ->setCellValue('E'.$num, $item->t_stp)
                    ->setCellValue('F'.$num,$item->t_xl)
                    ->setCellValue('G'.$num, $item->grandtotal);
                $objPHPExcel->getActiveSheet()->getStyle('A'.$num)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                $objPHPExcel->getActiveSheet()->getStyle('A'.$num.':G'.$num)->getFont()->setBold( true );
                // $objPHPExcel->getActiveSheet()->getStyle('B'.$num)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                // $objPHPExcel->getActiveSheet()->getStyle('C'.$num)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                // $objPHPExcel->getActiveSheet()->getStyle('D'.$num)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                // $objPHPExcel->getActiveSheet()->getStyle('E'.$num)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                // $objPHPExcel->getActiveSheet()->getStyle('F'.$num)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                // $objPHPExcel->getActiveSheet()->getStyle('G'.$num)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT);
                
                $datayear = \App\Models\DanaStpl::select(DB::Raw('year(dana_stpl_master.created_at) as year'),DB::Raw('month(dana_stpl_master.created_at) as month'), DB::Raw('sum(cmi) as t_cmi'), DB::Raw('sum(h3i) as t_h3i'), DB::Raw('sum(isat) as t_isat'), DB::Raw('sum(stp) as t_stp'), DB::Raw('sum(xl) as t_xl'), DB::Raw('sum(danastpl) as grandtotal'))
                                        ->where(DB::Raw('year(created_at)'), $item->year)
                                        ->groupBy(DB::Raw('month(created_at)'))
                                        // ->groupBy(DB::Raw('year(created_at)'))
                                        ->orderBy(DB::Raw('month(created_at)'), 'asc')
                                        // ->orderBy(DB::Raw('year(created_at)'), 'asc')
                                        ->get();
                
                // dd(count($datayear));
                
                $numdetail = $num+1;
                foreach($datayear as $l => $itemdetail){
                    $arrmonth = array('Januari', 'Februari', 'Maret', 'April', 'Mei', 'Juni', 'Juli', 'Agustus', 'September', 'Oktober', 'November', 'Desember');
                    $objPHPExcel->setActiveSheetIndex(0)
                    ->setCellValue('A'.$numdetail,'    '.$arrmonth[$itemdetail->month - 1])
                    ->setCellValue('B'.$numdetail,$itemdetail->t_cmi)
                    ->setCellValue('C'.$numdetail,$itemdetail->t_h3i)
                    ->setCellValue('D'.$numdetail,$itemdetail->t_isat)
                    ->setCellValue('E'.$numdetail,$itemdetail->t_stp)
                    ->setCellValue('F'.$numdetail,$itemdetail->t_xl)
                    ->setCellValue('G'.$numdetail, $itemdetail->grandtotal);

                    // $objPHPExcel->getActiveSheet()->getStyle('A'.$numdetail)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);
                    $objPHPExcel->getActiveSheet()->getStyle('A'.$numdetail.':G'.$numdetail)->getFont()->setBold( true );
                    $datamonth = \App\Models\DanaStpl::select('region.region', DB::Raw('year(dana_stpl_master.created_at) as year'), DB::Raw('month(dana_stpl_master.created_at) as month'), DB::Raw('sum(cmi) as t_cmi'), DB::Raw('sum(h3i) as t_h3i'), DB::Raw('sum(isat) as t_isat'), DB::Raw('sum(stp) as t_stp'), DB::Raw('sum(xl) as t_xl'), DB::Raw('sum(danastpl) as grandtotal'))
                                                        ->join(env("DB_DATABASE_EPL_PMT").'.region', 'region.id', 'dana_stpl_master.region_id')
                                                        ->where(DB::Raw('month(dana_stpl_master.created_at)'), $itemdetail->month)
                                                        ->where(DB::Raw('year(dana_stpl_master.created_at)'), $itemdetail->year)
                                                        ->groupBy('dana_stpl_master.region_id')
                                                        ->orderBy(DB::Raw('dana_stpl_master.region_id'), 'asc')
                                                        ->get();
                    $nummonthdetail = $numdetail+1;
                    foreach($datamonth as $l => $itemmonth){
                    // dd($datamonth);
                        $objPHPExcel->setActiveSheetIndex(0)
                        ->setCellValue('A'.$nummonthdetail,'      '.$itemmonth->region)
                        ->setCellValue('B'.$nummonthdetail,$itemmonth->t_cmi)
                        ->setCellValue('C'.$nummonthdetail,$itemmonth->t_h3i)
                        ->setCellValue('D'.$nummonthdetail,$itemmonth->t_isat)
                        ->setCellValue('E'.$nummonthdetail,$itemmonth->t_stp)
                        ->setCellValue('F'.$nummonthdetail,$itemmonth->t_xl)
                        ->setCellValue('G'.$nummonthdetail, $itemmonth->grandtotal);

                        // $objPHPExcel->getActiveSheet()->getStyle('A'.$nummonthdetail)->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

                        $nummonthdetail++;
                    }

                    $numdetail = $numdetail+count($datamonth);
                    $numdetail++;
                }

                $num = $num+$numdetail+count($datayear);
                $num++;
                
        }
        

        

        // foreach($datayear as $item){
        //     dd($item->month);
        //     $monthtotal = [];
        //     $monthtotal = \App\Models\DanaStpl::select(DB::Raw('year(dana_stpl_master.created_at) as year'), DB::Raw('month(dana_stpl_master.created_at) as month'), DB::Raw('sum(cmi) as cmi'), DB::Raw('sum(h3i) as h3i'), DB::Raw('sum(isat) as isat'), DB::Raw('sum(stp) as stp'), DB::Raw('sum(xl) as xl'))
        //                                         ->where(DB::Raw('month(created_at)'), $item->month)
        //                                         ->where(DB::Raw('year(created_at)'), $item->year)
        //                                         ->where('status','3')
        //                                         ->groupBy(DB::Raw('year(created_at)'))
        //                                         ->get();
        //     foreach($monthtotal as $itemmonth){
        //         dd($monthtotal);
        //     }
            
        // }

        // $data = \App\Models\UserMember::orderBy('id','DESC');
        // if($this->keyword) {
        //     $data = $data->where('name','LIKE', '%'.$this->keyword.'%')
        //                                 ->orWhere('name_kta','LIKE', '%'.$this->keyword.'%')
        //                                 ->orWhere('email','LIKE', '%'.$this->keyword.'%');
        // }
        // if($this->koordinator_id) {
        //     $dataMember = UserMember::where('user_id',$this->koordinator_id)->first();
        //     $data = $data->where('koordinator_id',$dataMember->id);
        // }
        // if($this->status) {
        //     $data = $data->where('status',$this->status);
        // }

        // foreach($data->get() as $k => $i){
        //     if($i->koordinator_id==1)
        //         $koordinator_name = "Kantor";
        //     else
        //         $koordinator_name = isset($i->koordinatorUser->name)?$i->koordinatorUser->name:'';

        //     $objPHPExcel->setActiveSheetIndex(0)
        //         ->setCellValue('A'.$num,($k+1))
        //         ->setCellValue('B'.$num,$i->no_anggota_platinum)
        //         ->setCellValue('C'.$num,$i->no_anggota_gold)
        //         ->setCellValue('D'.$num,$i->no_form)
        //         ->setCellValue('E'.$num, $koordinator_name)
        //         ->setCellValue('F'.$num,date('d-m-Y',strtotime($i->tanggal_diterima)))
        //         ->setCellValue('G'.$num, $i->name);
        //     $objPHPExcel->getActiveSheet()->getStyle('AG'.$num)->getNumberFormat()->setFormatCode('#,##0');
        //     $objPHPExcel->getActiveSheet()->getStyle('AH'.$num)->getNumberFormat()->setFormatCode('#,##0');
        //     $objPHPExcel->getActiveSheet()->getStyle('AI'.$num)->getNumberFormat()->setFormatCode('#,##0');
        //     $objPHPExcel->getActiveSheet()->getStyle('AK'.$num)->getNumberFormat()->setFormatCode('#,##0');
        //     $num++;
        // }
        // Rename worksheet
        //$objPHPExcel->getActiveSheet()->setTitle('Iuran-'. date('d-M-Y'));
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);

        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, "Xlsx");

        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="Report-DanaSTPL-' .$year_from.'sd'.$year_to .'.xlsx"');
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
        },'Report-DanaSTPL-' .$year_from.'sd'.$year_to .'.xlsx');



        
        // session()->flash('message-success',"Insiden Report Berhasil diupload");
        // require 'vendor/autoload.php';


        // $spreadsheet = new Spreadsheet();
        // $sheet = $spreadsheet->getActiveSheet();
        // $sheet->setCellValue('A1', 'Hello World !');

        // $writer = new Xlsx($spreadsheet);
        // $writer->save('hello world.xlsx');


        // $filename = 'export.xlsx';
        // // here is generated a "normal" file download response of Laravel
        // return Excel::download(new CustomExcelExport(), $filename);
        // // return redirect()->route('dana-stpl.index');
        // // dd('download');

        // return Storage::disk('exports')->download('export.csv');

        // Excel::create('Filename', function($excel) {

        // })->download('xls');

        // return response()->download('export.csv');

        // return (new \App\Models\DanaStplReport($download))->download('EPL.DanaStplReport.xlsx');
    }

}
