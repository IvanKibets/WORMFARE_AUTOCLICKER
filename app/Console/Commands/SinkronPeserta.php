<?php

namespace App\Console\Commands;

use App\Models\Kepesertaan;
use App\Models\Polis;
use Illuminate\Console\Command;

class SinkronPeserta extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'sinkron';

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

        $inputFileName = './public/migrasi/migrasi-oktober.xls';

        /** Load $inputFileName to a Spreadsheet Object  **/
        $spreadsheet = \PhpOffice\PhpSpreadsheet\IOFactory::load($inputFileName);
        $sheetData = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        $arr = [];
        $key=0;
        $num=0;
        foreach($sheetData as $item){
            $num++;
            if($num<6) continue;

            $nomor_polis = $item['B'];
            $keterangan = $item['H'];
            $nomor_peserta = $item['G'];
            $bank = $item['I'];
            $no_ktp = $item['N'];
            $alamat = $item['O'];
            $no_telepon = $item['P'];
            $nama = $item['Q'];
            $tanggal_lahir = '';
            if($item['R']) $tanggal_lahir = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item['R'])->format('Y-m-d');
            $usia_masuk = $item['S'];
            $jenis_kelamin = $item['T'];
            $tanggal_mulai = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item['U'])->format('Y-m-d');
            $tanggal_akhir = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item['V'])->format('Y-m-d');
            $masa_bulan = $item['W'];
            $basic = $item['X'];
            $kontribusi = $item['Y'];
            $dana_tabbaru = $item['Z'];
            $dana_ujrah = $item['AA'];
            $extra_kontribusi = $item['AB'];
            if (true === strtotime($item['AJ'])) {
                $tgl_stnc = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item['AJ'])->format('Y-m-d');
            }
            $potongan_langsung = $item['AD'];
            $jumlah_potongan_langsung = $item['AE'];
            $pph = $item['AF'];
            $ppn = $item['AG'];
            $kartu_peserta = $item['AI'];
            $total_kontribusi_dibayar = $item['AH'];
            $status_polis = $item['AS'];
            $uw = $item['AK'];
            $rate = $item['AL'];
            $total_dn = $item['AM'];
            $no_reg = $item['AN'];
            $no_debit_note = $item['AO'];
            $tahun_produksi = $item['AP'];
            $produksi_akrual = $item['AQ'];
            $issued_accepted_date = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item['AR'])->format('Y-m-d');
            $status_endorse = $item['AS'];
            $refund = $item['AT'];
            $refund_keterangan = $item['AU'];
            $refund_date_efektif = $item['AX'];
            $no_cn = $item['AY'];
            $pay_date = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item['AZ'])->format('Y-m-d');
            $produksi_cash_basis = $item['BA'];
            $kontribusi_netto_biaya_penutupan = $item['BB'];
            $perkalian_biaya_penutupan = $item['BC'];
            $bp =  $item['BD'];
            $total_biaya_penutupan = $item['BE'];
            $line_of_business = $item['BF'];
            $ket_polis = $item['BG'];
            $channel = $item['BH'];
            $distribution_channel_to_aaji = $item['BI'];
            $tipe_reas = $item['BJ'];
            $model_reas = $item['BK'];
            $reasuradur = $item['BL'];
            $rate_reas = $item['BM'];
            $ri_com = $item['BN'];
            $nilai_manfaat_asuransi_reas = $item['BO'];
            $total_kontribusi_reas = $item['BP'];
            $ujroh_reas = $item['BQ'];
            $net_kontribusi_reas = $item['BR'];
            $prod_reas = $item['BS'];
            $prod_akrual_reas = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item['BT'])->format('Y-m-d');
            $no_cn_reas = $item['BU'];
            $no_kode = $item['BP'];
            $pay_date_reas = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item['BW'])->format('Y-m-d');

            $data = Kepesertaan::where('no_peserta',$nomor_peserta)->first();

            if(!$data) {
                $data = new Kepesertaan;
                $find_polis = Polis::where('no_polis',$nomor_polis)->first();
                if($find_polis) $data->polis_id = $find_polis->id;
            }
            $data->keterangan = $keterangan;
            $data->no_peserta = $nomor_peserta;
            $data->bank = $bank;
            $data->no_ktp = $no_ktp;
            $data->alamat = $alamat;
            $data->no_telepon = $no_telepon;
            $data->nama = $nama;
            if($tanggal_lahir) $data->tanggal_lahir = $tanggal_lahir;
            $data->usia = $usia_masuk;
            $data->jenis_kelamin = $jenis_kelamin;
            if(isset($tanggal_mulai)) $data->tanggal_mulai = $tanggal_mulai;
            if(isset($tanggal_akhir)) $data->tanggal_akhir = $tanggal_akhir;
            $data->masa_bulan = $masa_bulan;
            $data->basic = $basic;
            $data->kontribusi = $kontribusi;
            $data->dana_tabarru = $dana_tabbaru;
            $data->dana_ujrah = $dana_ujrah;
            $data->extra_kontribusi = $extra_kontribusi;
            $data->pph = $pph;
            $data->ppn = $ppn;
            $data->total_kontribusi_dibayar = $total_kontribusi_dibayar;
            $data->kartu_peserta = $kartu_peserta;
            if(isset($tgl_stnc)) $data->tanggal_stnc = $tgl_stnc;
            $data->uw = $uw;
            $data->rate = $rate;
            $data->total_dn = $total_dn;
            $data->no_reg = $no_reg;
            $data->no_debit_note = $no_debit_note;
            $data->tahun_produksi = $tahun_produksi;
            $data->produksi_akrual = $produksi_akrual;
            $data->issued_accepted_date = $issued_accepted_date;
            $data->status_endorse = $status_endorse;
            $data->refund = $refund;
            $data->refund_keterangan = $refund_keterangan;
            $data->refund_date_efektif = $refund_date_efektif;
            $data->no_cn = $no_cn;
            $data->pay_date = $pay_date;
            $data->produksi_cash_basis = $produksi_cash_basis;
            $data->kontribusi_netto_biaya_penutupan = $kontribusi_netto_biaya_penutupan;
            $data->perkalian_biaya_penutupan = $perkalian_biaya_penutupan;
            $data->bp = $bp;
            $data->total_biaya_penutupan = $total_biaya_penutupan;
            $data->line_of_business = $line_of_business;
            $data->ket_polis = $ket_polis;
            $data->channel = $channel;
            $data->distribution_channel_to_aaji = $distribution_channel_to_aaji;
            $data->tipe_reas = $tipe_reas;
            $data->model_reas = $model_reas;
            $data->reasuradur = $reasuradur;
            $data->rate_reas = $rate_reas;
            $data->ri_com = $ri_com;
            $data->nilai_manfaat_asuransi_reas = $nilai_manfaat_asuransi_reas;
            $data->total_kontribusi_reas = $total_kontribusi_reas;
            $data->ujroh_reas = $ujroh_reas;
            $data->net_kontribusi_reas = $net_kontribusi_reas;
            $data->prod_reas = $prod_reas;
            $data->prod_akrual_reas = $prod_akrual_reas;
            $data->no_cn_reas = $no_cn_reas;
            $data->no_kode = $no_kode;
            $data->pay_date_reas = $pay_date_reas;
            $data->potongan_langsung = $potongan_langsung;
            $data->jumlah_potongan_langsung = $jumlah_potongan_langsung;
            $data->status_polis = $status_polis;
            $data->save();
            echo "{$key}. no peserta :{$data->no_peserta}\nNama : {$data->nama}\n\n";
            $key++;
        }

        echo "\nSELESAI\n";
    }
}
