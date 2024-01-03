<?php

namespace App\Http\Livewire\Polis;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Provinsi;
use App\Models\Polis;
use DateTime;

class Upload extends Component
{
    use WithFileUploads;
    public $data,$file;
    public function render()
    {
        return view('livewire.polis.upload');
    }

    public function save()
    {
        \LogActivity::add('[web] Upload Polis');
        $this->validate([
            'file'=>'required|mimes:xlsx|max:51200' // 50MB maksimal
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

                $no_polis = $item[1];
                $no_polis_sistem = $item[2];
                $nama = $item[3];
                $alamat = $item[4];
                $provinsi = $item[5];
                // find provinsi
                $provinsi = Provinsi::where('nama','LIKE',"%{$provinsi}%")->first();
                if($provinsi) $provinsi = $provinsi->id;
                $terbit_polis = $item[6];
                $tahun_terbit_polis = $item[7];
                $singkatan_nama_produk = $item[8];
                $nama_produk = $item[9];
                $klasifikasi = $item[10];
                $awal = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item[11])->format('Y-m-d');
                $akhir = \PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item[12])->format('Y-m-d');
                $keterangan = $item[13];
                $status = $item[14];
                $rate = $item[15];
                $kode_rate = $item[16];
                $masa_leluasa = $item[17];
                $kelengkapan_berkas = $item[18];
                $kadaluarsa_klaim = $item[19];
                $pemulihan_kepesertaan = $item[20];
                $penyelesaian_perselisihan = $item[21];
                $iuran_tabbaru = $item[22];
                $ujrah_atas_pengelolaan = $item[23];
                $nisbah_hasil_investasi_peserta = $item[24];
                $nisbah_hasil_investasi_pengelolaan = $item[25];
                $surplus_uw_tabbaru = $item[26];
                $surplus_uw_peserta = $item[27];
                $surplus_uw_pengelola = $item[28];
                $usia_minimal = $item[29];
                $usia_maksimal = $item[30];
                $reasuradur = $item[31];
                $tipe = $item[32];
                $model = $item[33];
                $rate_persen = $item[34];
                $ri_com = $item[35];
                $ketentuan_uw_reas = $item[36];
                $stnc = $item[37];
                $kadaluarsa_reas = $item[38];
                $jatuh_tempo_pembayaran_kontribusi_reas = $item[39];
                $no_perjanjian_reas = $item[40];
                $perkalian_biaya_penutupan = $item[41];
                $potong_langsung = $item[42];
                $fee_base_brokerage = $item[43];
                $maintenance = $item[44];
                $admin_agency = $item[45];
                $agen_penutup = $item[46];
                $operasional_agency = $item[47];
                $ujroh_handling_fee_broker = $item[48];
                $referal_fee = $item[49];
                $pph = $item[50];
                $ppn = $item[51];
                $tujuan_pembayaran_nota_penutupan = $item[52];
                $no_rekening = $item[53];
                $bank = $item[54];
                $tujuan_pembayaran_update = $item[55];
                $pks = $item[56];
                $produksi_kontribusi = $item[57];
                $surat_permohonan_tarif_kontribusi = $item[58];
                $fitur_produk = $item[59];
                $tabel_rate_premi = $item[60];
                $spajks = $item[61];
                $spajks_sementara = $item[62];
                $copy_ktp = $item[63];
                $copy_npwp = $item[64];
                $npwp = $item[65];
                $copy_siup = $item[66];
                $nota_penutupan = $item[67];
                $tujuan_pembayaran_nama_penerima_refund = $item[68];
                $bank_refund = $item[69];
                $no_rekening_refund = $item[70];
                $tujuan_pengiriman_surat = $item[71];
                $mcu_dicover_ajri = $item[72];
                $kabupaten = $item[73];
                $kode_kabupaten = $item[74];
                $cabang_pemasaran = $item[75];
                $ket_diskon = $item[76];
                $sektor_keuangan = $item[77];
                $sektor_ekonomi = $item[78];
                $mitra_pengimbang = $item[79];
                $asuransi_mikro = $item[80];
                $pic_marketing = $item[81];
                $dc_aaji = $item[82];
                $dc_ojk = $item[83];
                $office = $item[84];
                $channel = $item[85];
                $segment = $item[86];

                $line_of_business = $item[87];
                $source_of_business = $item[89];
                $no_nota_penutupan = $item[90];
                $no_perjanjian_kerjasama = $item[91];
                $peninjauan_ulang = $item[92];
                $pembayaran_klaim = $item[93];
                $retroaktif = $item[94];
                $waiting_period = $item[95];
                $rate_single_usia = $item[96];
                
                $total_bp = $item[96];
                $no_sb = $item[97];
                $uw_limit = $item[98];
                $margin_rate = $item[99];
                $ri_comm = $item[100];
                $share_reinsurance = $item[101];
                $lost_ratio = $item[102];
                $profit_margin = $item[103];
                $contingency_margin = $item[104];
                $gae = $item[105];
                $business_source = $item[106];
                $refund = $item[107];
                $refund_to_pengalihan = $item[108];
                $dana_tabbaru_reas = $item[109];
                $dana_ujroh_reas = $item[110];
                $stop_loss = $item[111];
                $kerjasama_pemasaran = $item[112];
                $cut_loss = $item[113];
                $refund_cut_loss = $item[114];

                // find polis
                $find_polis = Polis::where('no_polis',$no_polis)->first();
                if($find_polis){
                    $find_polis->line_of_business = $line_of_business;
                    $find_polis->source_of_business = $source_of_business;
                    $find_polis->no_nota_penutupan = $no_nota_penutupan;
                    $find_polis->no_perjanjian_kerjasama = $no_perjanjian_kerjasama;
                    $find_polis->peninjauan_ulang = $peninjauan_ulang;
                    $find_polis->pembayaran_klaim = $pembayaran_klaim;
                    $find_polis->retroaktif = $retroaktif;
                    $find_polis->waiting_period = $waiting_period;
                    $find_polis->save();
                }

                /*
                $arr[$key]['no_polis'] = $no_polis;
                $arr[$key]['nama'] = $nama;
                $arr[$key]['alamat'] = $alamat;
                $arr[$key]['awal'] = $awal;
                $arr[$key]['akhir'] = $akhir;
                $arr[$key]['rate'] = $rate;
                $arr[$key]['keterangan'] = $keterangan;
                $arr[$key]['status'] = $status;
                $arr[$key]['masa_leluasa'] = $masa_leluasa;
                $arr[$key]['kelengkapan_berkas'] = $kelengkapan_berkas;
                $arr[$key]['kadaluara_klaim'] = $kadaluarsa_klaim;
                $arr[$key]['pemulihan_kepesertaan'] = $pemulihan_kepesertaan;
                $arr[$key]['penyelesaian_perselisihan'] = $penyelesaian_perselisihan;
                $arr[$key]['iuran_tabbaru'] = $iuran_tabbaru;
                $arr[$key]['ujrah_atas_pengelolaan'] = $ujrah_atas_pengelolaan;
                $arr[$key]['nisbah_hasil_investasi_peserta'] = $nisbah_hasil_investasi_peserta;
                $arr[$key]['nisbah_hasil_investasi_pengelolaan'] = $nisbah_hasil_investasi_pengelolaan;
                $arr[$key]['surplus_uw_tabbaru'] = $surplus_uw_tabbaru;
                $arr[$key]['surplus_uw_peserta'] = $surplus_uw_peserta;
                $arr[$key]['surplus_uw_pengelola'] = $surplus_uw_pengelola;
                $arr[$key]['usia_minimal'] = $usia_minimal;
                $arr[$key]['usia_maksimal'] = $usia_maksimal;
                $arr[$key]['tipe'] = $tipe;
                $arr[$key]['model'] = $model;
                $arr[$key]['rate_persen'] = $rate_persen;
                $arr[$key]['ri_com'] = $ri_com;
                $arr[$key]['ketentuan_uw_reas'] = $ketentuan_uw_reas;
                $arr[$key]['stnc'] = $stnc;
                $arr[$key]['kadaluarsa_reas'] = $kadaluarsa_reas;
                $arr[$key]['jatuh_tempo_pembayaran_kontribusi_reas'] = $jatuh_tempo_pembayaran_kontribusi_reas;
                $arr[$key]['no_perjanjian_reas'] = $no_perjanjian_reas;
                $arr[$key]['perkalian_biaya_penutupan'] = $perkalian_biaya_penutupan;
                $arr[$key]['potong_langsung'] = $potong_langsung;
                $arr[$key]['fee_base_brokerage'] = $fee_base_brokerage;
                $arr[$key]['maintenance'] = $maintenance;
                $arr[$key]['admin_agency'] = $admin_agency;
                $arr[$key]['agen_penutup'] = $agen_penutup;
                $arr[$key]['operasional_agency'] = $operasional_agency;
                $arr[$key]['ujroh_handling_fee_broker'] = $ujroh_handling_fee_broker;
                $arr[$key]['referal_fee'] = $referal_fee;
                $arr[$key]['pph'] = $pph;
                $arr[$key]['ppn'] = $ppn;
                $arr[$key]['tujuan_pembayaran_nota_penutupan'] = $tujuan_pembayaran_nota_penutupan;
                $arr[$key]['no_rekening'] = $no_rekening;
                $arr[$key]['bank'] = $bank;
                $arr[$key]['tujuan_pembayaran_update'] = $tujuan_pembayaran_update;
                $arr[$key]['pks'] = $pks;
                $arr[$key]['produksi_kontribusi'] = $produksi_kontribusi;
                $arr[$key]['surat_permohonan_tarif_kontribusi'] = $surat_permohonan_tarif_kontribusi;
                $arr[$key]['fitur_produk'] = $fitur_produk;
                $arr[$key]['tabel_rate_premi'] = $tabel_rate_premi;
                $arr[$key]['spajks'] = $spajks;
                $arr[$key]['spajks_sementara'] = $spajks_sementara;
                $arr[$key]['copy_ktp'] = $copy_ktp;
                $arr[$key]['copy_npwp'] = $copy_npwp;
                $arr[$key]['npwp'] = $npwp;
                $arr[$key]['copy_siup'] = $copy_siup;
                $arr[$key]['nota_penutupan'] = $nota_penutupan;
                $arr[$key]['tujuan_pembayaran_nama_penerima_refund'] = $tujuan_pembayaran_nama_penerima_refund;
                $arr[$key]['bank_refund'] = $bank_refund;
                $arr[$key]['no_rekening_refund'] = $no_rekening_refund;
                $arr[$key]['tujuan_pengiriman_surat'] = $tujuan_pengiriman_surat;
                $arr[$key]['mcu_dicover_ajri'] = $mcu_dicover_ajri;
                $arr[$key]['kode_kabupaten'] = $kode_kabupaten;
                $arr[$key]['cabang_pemasaran'] = $cabang_pemasaran;
                $arr[$key]['ket_diskon'] = $ket_diskon;
                $arr[$key]['sektor_keuangan'] = $sektor_keuangan;
                $arr[$key]['sektor_ekonomi'] = $sektor_ekonomi;
                $arr[$key]['mitra_pengimbang'] = $mitra_pengimbang;
                $arr[$key]['asuransi_mikro'] = $asuransi_mikro;
                $arr[$key]['pic_marketing'] = $pic_marketing;
                $arr[$key]['dc_aaji'] = $dc_aaji;
                $arr[$key]['dc_ojk'] = $dc_ojk;
                $arr[$key]['office'] = $office;
                $arr[$key]['channel'] = $channel;
                $arr[$key]['segment'] = $segment;
                $arr[$key]['line_of_business'] = $line_of_business;
                $arr[$key]['source_of_business'] = $source_of_business;
                $arr[$key]['no_nota_penutupan'] = $no_nota_penutupan;
                $arr[$key]['no_perjanjian_kerjasama'] = $no_perjanjian_kerjasama;
                $arr[$key]['peninjauan_ulang'] = $peninjauan_ulang;
                $arr[$key]['pembayaran_klaim'] = $pembayaran_klaim;
                $arr[$key]['retroaktif'] = $retroaktif;
                $arr[$key]['waiting_period'] = $waiting_period;
                $arr[$key]['rate_single_usia'] = $rate_single_usia;
                $arr[$key]['total_bp'] = $total_bp;
                $arr[$key]['no_sb'] = $no_sb;
                $arr[$key]['uw_limit'] = $uw_limit;
                $arr[$key]['margin_rate'] = $margin_rate;
                $arr[$key]['ri_comm'] = $ri_comm;
                $arr[$key]['share_reinsurance'] = $share_reinsurance;
                $arr[$key]['lost_ratio'] = $lost_ratio;
                $arr[$key]['profit_margin'] = $profit_margin;
                $arr[$key]['contingency_margin'] = $contingency_margin;
                $arr[$key]['gae'] = $gae;
                $arr[$key]['business_source'] = $business_source;
                $arr[$key]['refund'] = $refund;
                $arr[$key]['refund_to_pengalihan'] = $refund_to_pengalihan;
                $arr[$key]['dana_tabbaru_reas'] = $dana_tabbaru_reas;
                $arr[$key]['dana_ujroh_reas'] = $dana_ujroh_reas;
                $arr[$key]['stop_loss'] = $stop_loss;
                $arr[$key]['kerjasama_pemasaran'] = $kerjasama_pemasaran;
                $arr[$key]['cut_loss'] = $cut_loss;
                $arr[$key]['refund_cut_loss'] = $refund_cut_loss;
                $arr[$key]['created_at'] = date('Y-m-d H:i:s');
                
                $arr[$key]['updated_at'] = date('Y-m-d H:i:s');
                */
                $key++;
                
            }

            //Polis::insert($arr);

            $this->emit('modal','hide');
            $this->emit('reload-page');
        }
    }
}
