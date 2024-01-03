<?php

namespace App\Http\Livewire\Peserta;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\Kepesertaan;
use App\Models\Pengajuan;

class Index extends Component
{
    use WithPagination;
    protected $paginationTheme = 'bootstrap';
    public $filter_keyword,$filter_status_polis,$is_rekon=false,$rekon_id=[],$filter_cabang_id,$filter_status_pengajuan;
    public $check_all=0,$start_tanggal_reconciled,$end_tanggal_reconciled;
    protected $listeners = ['reload-page'=>'$refresh'];
    public function render()
    {
        $data = $this->get_data();

        $total = clone $data;

        return view('livewire.peserta.index')->with(['data'=>$data->paginate(100),'total_kontribusi'=>$total]);
    }

    public function mount()
    {
        // Initial value
        foreach($this->get_data()->get() as $k => $item){
            $this->rekon_id[$k] = '';
        }
    }

    public function submit_rekon()
    {
       
        foreach($this->rekon_id as $id){
            if($id =="") continue;

            $find = Kepesertaan::find($id);
            if($find){
                $pengajuan = Pengajuan::find($find->pengajuan_id);
                if($pengajuan){
                    $pengajuan->status = 5;
                    $pengajuan->rekon_date = date('Y-m-d H:i:s');
                    $pengajuan->rekon_user_id = \Auth::user()->id;
                    $pengajuan->save();
                }
            }
        }

        session()->flash('message-success',__('Rekon berhasil disubmit'));

        return redirect()->route('peserta.index');
    }

    public function get_data()
    {
        $data = Kepesertaan::select('kepesertaan.*',\DB::raw("pengajuan.status AS status_pengajuan"),'pengajuan.rekon_date')->orderBy('kepesertaan.id','DESC')->with(['polis','polis.produk','pengajuan'])
                ->join('pengajuan','pengajuan.id','=','kepesertaan.pengajuan_id')
                //->where('status_akseptasi',1)
                ->where(function($table){
                    $table->where('pengajuan.status',3)->orWhere('pengajuan.status',5);
                    });

        if(\Auth::user()->user_access_id==2) $data->where('pengajuan.account_manager_id',\Auth::user()->id);

        if($this->filter_keyword) $data->where(function($table){
        foreach(\Illuminate\Support\Facades\Schema::getColumnListing('kepesertaan') as $column){
            $table->orWhere('kepesertaan.'.$column,'LIKE',"%{$this->filter_keyword}%");
        }
        });
        if($this->filter_cabang_id) $data->where('pengajuan.account_manager_id',$this->filter_cabang_id);
        if($this->filter_status_polis) $data->where('kepesertaan.status_polis',$this->filter_status_polis);
        if($this->is_rekon) $data->where('pengajuan.status',3);
        if($this->filter_status_pengajuan) $data->where('pengajuan.status',$this->filter_status_pengajuan);
        if($this->start_tanggal_reconciled)
        if($this->start_tanggal_reconciled and $this->end_tanggal_reconciled){
            if($this->start_tanggal_reconciled == $this->end_tanggal_reconciled)
                $data->whereDate('pengajuan.rekon_date',$this->start_tanggal_reconciled);
            else
                $data->whereBetween('pengajuan.rekon_date',[$this->start_tanggal_reconciled,$this->end_tanggal_reconciled]);
        }
        return $data;
    }

    public function updated($propertyName)
    {
        $this->emit('init-data');
        $this->resetPage();

        if($propertyName=='check_all' and $this->check_all==1){
            foreach($this->get_data()->get() as $k => $item){
                $this->rekon_id[$k] = $item->id;
            }
        }

        if($propertyName=='check_all' and ($this->check_all==0 || $this->check_all=="")){
            $this->rekon_id = [];
        }
    }

    public function downloadExcel()
    {
        $objPHPExcel = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        // Set document properties
        $objPHPExcel->getProperties()->setCreator("Entigi System")
                                    ->setLastModifiedBy("Entigi System")
                                    ->setTitle("Office 2007 XLSX Product Database")
                                    ->setSubject("Peserta")
                                    ->setKeywords("office 2007 openxml php");

        $title = 'DAFTAR KEPESERTAAN';
        
        $activeSheet = $objPHPExcel->setActiveSheetIndex(0);
        $activeSheet->setCellValue('A1', $title);
        $activeSheet->mergeCells("A1:O1");
        $activeSheet->getRowDimension('1')->setRowHeight(34);
        $activeSheet->getStyle('A1')->applyFromArray([
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
            ],
            'font' => [
                'size' => 16,
                'bold' => true,
            ]
        ]);;

        $activeSheet
                ->setCellValue('A2', 'NO')
                ->setCellValue('B2', 'STATUS')
                ->setCellValue('C2', 'NO POLIS')
                ->setCellValue('D2', 'PEMEGANG POLIS')
                ->setCellValue('E2', 'PRODUK')
                ->setCellValue('F2', 'NO PENGAJUAN')
                ->setCellValue('G2', 'NAMA PESERTA')
                ->setCellValue('H2', 'KET')
                ->setCellValue('I2', 'BPR/BANK/CAB')
                ->setCellValue('J2', 'NO KTP')
                ->setCellValue('K2', 'ALAMAT')
                ->setCellValue('L2', 'NO HANDPHONE')
                ->setCellValue('M2', 'DATE OF BIRTH')
                ->setCellValue('N2', 'USIA MASUK')
                ->setCellValue('O2', 'JENIS KELAMIN')
                ->setCellValue('P2', 'MULAI ASURANSI')
                ->setCellValue('Q2', 'AKHIR ASURANSI')
                ->setCellValue('R2', 'MASA ASURANSI (BULAN)')
                ->setCellValue('S2', 'TOTAL MANFAAT ASURANSI')
                ->setCellValue('T2', 'PREMI')
                ->setCellValue('U2', 'UW LIMIT')
                ->setCellValue('V2', 'RATE')
                ;

        $activeSheet->getStyle("A2:V2")->applyFromArray([
                    'font' => [
                        'bold' => true,
                    ],
                    'borders' => [
                        'top' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                        'bottom' => [
                            'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                            'color' => ['argb' => '000000'],
                        ],
                    ],
                    'alignment' => [
                        'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
                        'vertical' => \PhpOffice\PhpSpreadsheet\Style\Alignment::VERTICAL_CENTER
                    ],
                ]);       

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
        $activeSheet->getColumnDimension('P')->setAutoSize(true);
        $activeSheet->getColumnDimension('Q')->setAutoSize(true);
        $activeSheet->getColumnDimension('R')->setAutoSize(true);
        $activeSheet->getColumnDimension('S')->setAutoSize(true);
        $activeSheet->getColumnDimension('T')->setAutoSize(true);
        $activeSheet->getColumnDimension('U')->setAutoSize(true);
        $activeSheet->getColumnDimension('V')->setAutoSize(true);
        $num=3;

        $k=0;
        $data = $this->get_data()->get();

        foreach($data as $k => $item){
            $k++;
            $status = '';
            if($item->status_pengajuan==3) $status = 'Accepted';
            if($item->status_pengajuan==5) $status = 'Reconciled';
            $activeSheet
                ->setCellValue('A'.$num,$k)
                ->setCellValue('B'.$num,$status)
                ->setCellValue('C'.$num,isset($item->polis->no_polis) ? $item->polis->no_polis : '')
                ->setCellValue('D'.$num,isset($item->polis->nama) ? $item->polis->nama : '')
                ->setCellValue('E'.$num,isset($item->polis->produk->nama)?$item->polis->produk->nama:'-')
                ->setCellValue('F'.$num,isset($item->pengajuan->no_pengajuan)?$item->pengajuan->no_pengajuan:'-')
                ->setCellValue('G'.$num,$item->nama)
                ->setCellValue('H'.$num,$item->keterangan)
                ->setCellValue('I'.$num,isset($item->pengajuan->account_manager->name)?$item->pengajuan->account_manager->name:'-')
                ->setCellValue('J'.$num,$item->no_ktp)
                ->setCellValue('K'.$num,$item->alamat)
                ->setCellValue('L'.$num,$item->no_telepon)
                ->setCellValue('M'.$num,date('d-M-Y',strtotime($item->tanggal_lahir)))
                ->setCellValue('N'.$num,$item->usia)
                ->setCellValue('O'.$num,$item->jenis_kelamin)
                ->setCellValue('P'.$num,date('d-F-Y',strtotime($item->tanggal_mulai)))
                ->setCellValue('Q'.$num,date('d-F-Y',strtotime($item->tanggal_akhir)))
                ->setCellValue('R'.$num,$item->masa_bulan)
                ->setCellValue('S'.$num,$item->basic)
                ->setCellValue('T'.$num,$item->kontribusi)
                ->setCellValue('U'.$num,$item->uw)
                ->setCellValue('V'.$num,$item->rate)
                ;

                // $activeSheet->getStyle("H{$num}:K{$num}")->getNumberFormat()->setFormatCode('#,##0.00');

                // if($i->extra_mortalita) $activeSheet->getStyle("L{$num}")->getNumberFormat()->setFormatCode('#,##0.00');
                // if($i->extra_kontribusi) $activeSheet->getStyle("M{$num}")->getNumberFormat()->setFormatCode('#,##0.00');

                $activeSheet->getStyle("S{$num}")->getNumberFormat()->setFormatCode('#,##0.00');
                $activeSheet->getStyle("T{$num}")->getNumberFormat()->setFormatCode('#,##0.00');

            // $activeSheet->getStyle("A{$num}:Q{$num}")->applyFromArray([
            //     'borders' => [
            //         'top' => [
            //             'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
            //             'color' => ['argb' => '000000'],
            //         ]
            //     ],
            // ]);

            $num++;
        }
            
        // Rename worksheet
        $activeSheet->setTitle('Pengajuan');
        // Set active sheet index to the first sheet, so Excel opens this as the first sheet
        $objPHPExcel->setActiveSheetIndex(0);
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($objPHPExcel, "Xlsx");

        // Redirect output to a client’s web browser (Excel5)
        header('Content-Type: application/vnd.ms-excel');
        header('Content-Disposition: attachment;filename="database-peserta'.date('d-m-Y').'.xlsx"');
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
        },'database-peserta-'.date('d-m-Y').'.xlsx');
    }
}