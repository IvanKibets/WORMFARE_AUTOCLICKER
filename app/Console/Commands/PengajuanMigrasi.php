<?php

namespace App\Console\Commands;

use App\Models\Kepesertaan;
use App\Models\Polis;
use App\Models\User;
use App\Models\Pengajuan;
use App\Models\Reasuradur;
use App\Models\Dn;
use Illuminate\Console\Command;

class PengajuanMigrasi extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'pengajuan:migrasi';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        ini_set('memory_limit', '-1');
        $inputFileName = './public/migrasi/migrasi.xlsx';

        /** Load $inputFileName to a Spreadsheet Object  **/
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $arr = [];
        $key=0;
        $num=0;
        $double=0;
        foreach($sheetData as $k => $item){
            $num++;
            if($num<1) continue;

            $no_polis = $item['A'];
            $no_peserta = $item['B'];
            $nama = $item['C'];
            $tanggal_lahir = date('Y-m-d',strtotime($item['D']));
            $mulai_asuransi = date('Y-m-d',strtotime($item['E']));
            $akhir_asuransi = date('Y-m-d',strtotime($item['F']));
            $manfaat_asurani = replace_idr($item['G']);
            $kontribusi = replace_idr($item['H']);
            $extra_kontribusi = replace_idr($item['I']);
            $no_invoice = $item['J'];
            $status = $item['K'];
            $cabang_ = $item['L'];
            
            // find polis 
            $polis = Polis::where('no_polis',$no_polis)->first();
            if(!$polis) {
                $this->error("not found polis : {$no_polis}");
                continue;
            }            

            // find cabang
            $cabang = User::where('name',"LIKE","%{$cabang_}%")->first();
            if(!$cabang) {
                $this->error("not found cabang :{$cabang_}");
                continue;
            }

            continue;

            if(strtolower($status)=="inforce"){
                $status = 3;
            }elseif(strtolower($status)=="claim"){
                $status = 3;
            }elseif(strtolower($status)=="cancel"){
                $status = 4;
            }else{
                $status=3;
            }

            $pengajuan = new Pengajuan();
            $pengajuan->polis_id = $polis->id;
            $pengajuan->masa_asuransi =  1;
            $pengajuan->perhitungan_usia = 1;
            $pengajuan->polis_id = $polis->id;
            $pengajuan->status = $status; // 3 = Accepted, 5 = Recon 
            $pengajuan->total_akseptasi = 1;
            $pengajuan->total_approve = 1;
            $pengajuan->total_reject = 0;
            $pengajuan->no_pengajuan =  date('dmy').str_pad((Pengajuan::count()+1),6, '0', STR_PAD_LEFT);
            $pengajuan->account_manager_id = $cabang->id;
            $pengajuan->is_migrate = 1;
            $pengajuan->dn_number = $no_invoice;
            $pengajuan->save();

            $peserta = new Kepesertaan();
            $peserta->pengajuan_id = $pengajuan->id;
            $peserta->nama = $nama;
            $peserta->no_peserta = $no_peserta;
            $peserta->basic = $manfaat_asurani;
            $peserta->kontribusi = $kontribusi;
            $peserta->extra_kontribusi = $extra_kontribusi;
            $peserta->polis_id = $polis->id;
            $peserta->user_id = $cabang->id;
            $peserta->is_temp = 0;
            $peserta->status_polis = 'Akseptasi';
            $peserta->tanggal_lahir = $tanggal_lahir;
            $peserta->tanggal_mulai = $mulai_asuransi;
            $peserta->tanggal_akhir = $akhir_asuransi;
            $peserta->save();
        }
        echo "\nSELESAI\n";
    }
}
