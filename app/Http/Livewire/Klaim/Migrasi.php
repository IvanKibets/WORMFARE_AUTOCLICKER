<?php

namespace App\Http\Livewire\Klaim;

use App\Models\Kepesertaan;
use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Klaim;
use App\Models\Polis;
use App\Models\Reasuradur;
use App\Models\ReasuradurRate;
use App\Models\Reas;
use App\Models\RecoveryClaim;
use App\Models\Provinsi;
use App\Models\Kabupaten;

class Migrasi extends Component
{
    public $file;
    use WithFileUploads;
    public function render()
    {
        return view('livewire.klaim.migrasi');
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
        $sheetData = $xlsx->getActiveSheet()->toArray(null, true, false, true);
        // $sheetData = $xlsx->getActiveSheet()->toArray();
        $total_data = 0;
        $total_double = 0;
        $total_success = 0;
        $total_failed = 0;
        $data_failed = [];
        foreach($sheetData as $key => $item){
            if($key<=3) continue;
            $item['AT'] = $item['AT'] * 100;
            $item['AU'] = $item['AU'] * 100;

            if($item['B']=="") continue;
            $no_polis = $item['B'];
            $no_peserta = $item['G'];
            $tanggal_meninggal = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item['P'])->format('Y-m-d');
            $usia_masuk_klaim = $item['Q'];
            $nilai_klaim = $item['X'];
            $klaim_terverifikasi = $item['Y'];
            $uw_limit = $item['AA'];
            $kode_produk = $item['AC'];
            $type = $item['AD'];
            $jenis = $item['AE'];
            $tgl_stnc = $item['AF'];
            $tgl_kadaluarsa = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item['AK'])->format('Y-m-d');
            $reasuradur = $item['AM'];
            $ua_reas = $item['AL'];
            $nama_reas = $item['AM'];
            $kadaluarsa_reas = $item['AN'];
            $usia_kadaluarsa = $item['AO'];
            $kadaluarsa_klaim_hari = $item['AI'];
            $tgl_kadaluarsa_reas = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item['AP'])->format('Y-m-d');
            $bisnis_reas = $item['AQ'];
            $model_reas = $item['AR'];
            $max_or = $item['AS'];
            $or_share = $item['AT'];
            $reas_share = $item['AU'];
            $or = $item['AV'];
            $reas = $item['AW'];
            $tgl_pengajuan = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item['AY'])->format('Y-m-d');
            $kelengkapan_dok = $item['AZ'];
            $tgl_terima_dok = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item['BB'])->format('Y-m-d');
            $sumber_informasi = $item['BL'];
            $sebab_meninggal = $item['BM'];
            $riwayat_penyakit = $item['BN'];
            $verifikasi_via_telpon = $item['BO'];
            $analisa_medis = $item['BP'];
            $kesimpulan = $item['BQ'];
            $tgl_surat_investigasi = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item['BR'])->format('Y-m-d');
            $hasil_investigasi = $item['BT'];
            $jatuh_tempo_klaim = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item['BU'])->format('Y-m-d');

            $tgl_rekomendasi_dept_klaim = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item['BW'])->format('Y-m-d');
            $persetujuan_dept_klaim = $item['BX'];

            $tgl_rekomendasi_div_teknik = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item['BZ'])->format('Y-m-d');
            $persetujuan_devisi_teknik = $item['CA'];

            $tgl_rekomendasi_div = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item['CE'])->format('Y-m-d');
            $persetujuan_devisi = $item['CF'];

            $tgl_rekomendasi_direksi1 = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item['CH'])->format('Y-m-d');
            $tgl_rekomendasi_direksi2 = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item['CJ'])->format('Y-m-d');
            $status = $item['CK'];
            $tgl_approval = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item['CL'])->format('Y-m-d');
            $jenis_klaim = $item['CT'];
            $keterangan_klaim = $item['CU'];
            $jenis_penyakit = $item['CV'];
            $tempat_klaim = $item['CW'];
            $provinsi = $item['CZ'];
            $kota_kabupaten = $item['DA'];
            $alamat = $item['DB'];
            $sebab_tolak = $item['DC'];

            $polis = Polis::where('no_polis',$no_polis)->first();
            if(!$no_polis){
                $total_failed++;
                $data_failed[] = $item;
                continue;
            }

            $kepesertaan = Kepesertaan::where(['polis_id'=>$polis->id,'no_peserta'=>$no_peserta])->first();;
            if(!$kepesertaan){
                $total_failed++;
                $data_failed[] = $item;
                continue;
            }

            $data = Klaim::where(['kepesertaan_id'=>$kepesertaan->id,'is_migrate'=>1])->first();
            // if(!$data) {
            //     $data = new Klaim;
            //     $data->no_pengajuan = 'K'.date('dmy').str_pad((Klaim::count()+1),6, '0', STR_PAD_LEFT);
            // }
            // if($jatuh_tempo_klaim) $data->jatuh_tempo = $jatuh_tempo_klaim;
            // $data->save();

            // find reasuradur
            // if($nama_reas){
                // $reasuradur = Reasuradur::where('name',$nama_reas)->first();
                // if(!$reasuradur){
                //     $reasuradur = new Reasuradur();
                //     $reasuradur->name = $nama_reas;
                //     $reasuradur->save();
                // }

                // $reasuradur_rate = ReasuradurRate::where(['nama'=>$model_reas,'reasuradur_id'=>$reasuradur->id])->first();
                // if(!$reasuradur_rate){
                //     $reasuradur_rate = new ReasuradurRate();
                //     $reasuradur_rate->nama = $model_reas;
                //     $reasuradur_rate->reasuradur_id = $reasuradur->id;
                //     $reasuradur_rate->or = $or_share;
                //     $reasuradur_rate->reas = $reas_share;
                //     $reasuradur_rate->ri_com = 0;
                //     $reasuradur_rate->save();
                // }

                // $data_reas = Reas::where(['is_migrate'=>1,'reasuradur_id'=>$reasuradur->id,'reasuradur_rate_id'=>$reasuradur_rate->id])->first();
                // if(!$data_reas){
                //     $data_reas = new Reas();
                //     $data_reas->no_pengajuan = 'R'.date('dmy').str_pad((Reas::count()+1),6, '0', STR_PAD_LEFT);
                //     $data_reas->reasuradur_id = $reasuradur->id;
                //     $data_reas->reasuradur_rate_id = $reasuradur_rate->id;
                //     $data_reas->or = $or_share;
                //     $data_reas->reas = $reas_share;
                //     $data_reas->ri_com = 0;
                //     $data_reas->manfaat  = $bisnis_reas;
                //     $data_reas->is_migrate = 1;
                //     $data_reas->save();
                // }

            //     $recovery = RecoveryClaim::where('klaim_id',$data->id)->first();
            //     if(!$recovery){
            //         $recovery = new RecoveryClaim();
            //         $recovery->klaim_id = $data->id;
            //         $recovery->no_pengajuan = 'RC'.date('dmy').str_pad((RecoveryClaim::count()+1),6, '0', STR_PAD_LEFT);
            //     }

            //     $recovery->or_share = $or_share;
            //     $recovery->reas_share = $reas_share;
            //     $recovery->nilai_klaim = $klaim_terverifikasi;
            //     $recovery->polis_id = $polis->id;
            //     $recovery->kepesertaan_id = $kepesertaan->id;
            //     $recovery->reas_id = $kepesertaan->reas_id;
            //     $recovery->status = 1;
            //     $recovery->save();

            //     $kepesertaan->kadaluarsa_reas_tanggal = $tgl_kadaluarsa_reas;
            //     $kepesertaan->reas_manfaat_asuransi_ajri = $or;
            //     $kepesertaan->nilai_manfaat_asuransi_reas = $reas;
            //     $kepesertaan->reas_id = $kepesertaan->reas_id;
            //     $kepesertaan->recovery_claim_id = $recovery->id;
            // }

            // $data->status = 4; // migrasi
            // $data->head_klaim_status = '';
            // if($tgl_rekomendasi_dept_klaim) $data->head_klaim_date = $tgl_rekomendasi_dept_klaim;
            // $data->head_klaim_note = $persetujuan_dept_klaim;

            // if(strtoupper(substr($persetujuan_dept_klaim,0,6))=='TERIMA') $data->head_klaim_status = 1;
            // if(strtoupper(substr($persetujuan_dept_klaim,0,5))=='TOLAK' || strtoupper(substr($persetujuan_dept_klaim,0,5))=='DECLINE') $data->head_klaim_status = 2;
            // if(strtoupper(substr($persetujuan_dept_klaim,0,5))=='TUNDA') $data->head_klaim_status = 3;
            // if(strtoupper(substr($persetujuan_dept_klaim,0,11))=='INVESTIGASI') $data->head_klaim_status = 4;
            // if(strtoupper(substr($persetujuan_dept_klaim,0,6))=='LIABLE') $data->head_klaim_status = 5;
            // if(strtoupper(substr($persetujuan_dept_klaim,0,4))=='STNC') $data->head_klaim_status = 6;

            // $data->head_teknik_status = '';
            // $data->head_teknik_note = $persetujuan_devisi_teknik;
            // if(strtoupper(substr($persetujuan_devisi_teknik,0,6))=='TERIMA') $data->head_teknik_status = 1;
            // if(strtoupper(substr($persetujuan_devisi_teknik,0,5))=='TOLAK' || strtoupper(substr($persetujuan_dept_klaim,0,5))=='DECLINE') $data->head_teknik_status = 2;
            // if(strtoupper(substr($persetujuan_devisi_teknik,0,5))=='TUNDA') $data->head_teknik_status = 3;
            // if(strtoupper(substr($persetujuan_devisi_teknik,0,11))=='INVESTIGASI') $data->head_teknik_status = 4;
            // if(strtoupper(substr($persetujuan_devisi_teknik,0,6))=='LIABLE') $data->head_teknik_status = 5;
            // if(strtoupper(substr($persetujuan_devisi_teknik,0,4))=='STNC') $data->head_teknik_status = 6;

            // $data->head_teknik_status = '';
            // $data->head_teknik_note = $persetujuan_devisi_teknik;
            // if(strtoupper(substr($persetujuan_devisi_teknik,0,6))=='TERIMA') $data->head_teknik_status = 1;
            // if(strtoupper(substr($persetujuan_devisi_teknik,0,5))=='TOLAK' || strtoupper(substr($persetujuan_dept_klaim,0,5))=='DECLINE') $data->head_teknik_status = 2;
            // if(strtoupper(substr($persetujuan_devisi_teknik,0,5))=='TUNDA') $data->head_teknik_status = 3;
            // if(strtoupper(substr($persetujuan_devisi_teknik,0,11))=='INVESTIGASI') $data->head_teknik_status = 4;
            // if(strtoupper(substr($persetujuan_devisi_teknik,0,6))=='LIABLE') $data->head_teknik_status = 5;
            // if(strtoupper(substr($persetujuan_devisi_teknik,0,4))=='STNC') $data->head_teknik_status = 6;

            // $data->head_devisi_status = '';
            // $data->head_devisi_note = $persetujuan_devisi;
            // if(
            //     strtoupper(substr($persetujuan_devisi,0,6))=='TERIMA' ||
            //     strtoupper(substr($persetujuan_devisi,0,8))=='DITERIMA'
            //     ) $data->head_devisi_status = 1;
            // if(strtoupper(substr($persetujuan_devisi,0,5))=='TOLAK' || strtoupper(substr($persetujuan_devisi,0,5))=='DECLINE') $data->head_devisi_status = 2;
            // if(strtoupper(substr($persetujuan_devisi,0,5))=='TUNDA') $data->head_devisi_status = 3;
            // if(strtoupper(substr($persetujuan_devisi,0,11))=='INVESTIGASI') $data->head_devisi_status = 4;
            // if(strtoupper(substr($persetujuan_devisi,0,6))=='LIABLE') $data->head_devisi_status = 5;
            // if(strtoupper(substr($persetujuan_devisi,0,4))=='STNC') $data->head_devisi_status = 6;

            // if($tgl_rekomendasi_div_teknik) $data->head_teknik_date = $tgl_rekomendasi_div_teknik;
            // if($tgl_rekomendasi_direksi1) $data->direksi_1_date = $tgl_rekomendasi_direksi1;
            // if($tgl_rekomendasi_direksi2) $data->direksi_2_date = $tgl_rekomendasi_direksi2;

            // switch($status){
            //     case 'Analisa':
            //         $data->status_pengajuan = "";
            //         break;
            //     case 'Batal':
            //         $data->status_pengajuan = 7;
            //         break;
            //     case 'Terima':
            //         $data->status_pengajuan = 1;
            //         break;
            //     case 'Tolak':
            //         $data->status_pengajuan = 2;
            //         break;
            //     case 'Tunda':
            //         $data->status_pengajuan = 3;
            //         break;
            // } 

            // $provinsi = Provinsi::where('nama',$provinsi)->first();
            // if($provinsi){
            //     $data->provinsi_id = $provinsi->id;
            //     $kabupaten = Kabupaten::where('provinsi_id',$provinsi->id)->where('name','LIKE',"%{$kota_kabupaten}%")->first();
            //     if($kabupaten){
            //         $data->kabupaten_id = $kabupaten->id;
            //     }
            // }

            // $data->sumber_informasi = $sumber_informasi;
            $data->sebab = $sebab_meninggal;
            // $data->riwayat_penyakit = $riwayat_penyakit;
            // $data->verifikasi_via_telpon = $verifikasi_via_telpon;
            // $data->tempat_meninggal = $tempat_klaim;
            // $data->analisa_medis = $analisa_medis;
            // $data->kesimpulan = $kesimpulan;
            // $data->polis_id = $polis->id;
            // $data->kepesertaan_id = $kepesertaan->id;
            // $data->tanggal_meninggal = $tanggal_meninggal;
            // $data->nilai_klaim = $nilai_klaim;
            // $data->jenis_klaim = $jenis_klaim;
            $data->tempat_dan_sebab = $tempat_klaim;
            // $data->kadaluarsa_klaim_hari = $kadaluarsa_klaim_hari;
            // $data->kadaluarsa_klaim_tanggal = $tgl_kadaluarsa;
            // $data->kadaluarsa_reas_tanggal = $tgl_kadaluarsa_reas;
            // $data->alamat = $alamat; 
            // $data->tanggal_pengajuan = $tgl_pengajuan;
            if($tgl_terima_dok) {
                $data->tanggal_dok_lengkap = $tgl_terima_dok;
                $data->tanggal_proses = $tgl_terima_dok;
            }
            // $data->is_migrate = 1;
            // $data->nilai_klaim_disetujui = $klaim_terverifikasi;
            // $data->model_reas = $model_reas;
            // $data->type_reas = $bisnis_reas;
            // $data->reasuradur_ = $reasuradur;
            // $data->max_or = $max_or;
            // $data->share_or = $or_share;
            // $data->share_reas = $reas_share;
            // $data->nilai_klaim_or = $or;
            // $data->nilai_klaim_reas = $reas;
            // $data->model_reas = $model_reas;
            // $data->kategori_penyakit = $jenis_penyakit;
            $data->save();

            // $kepesertaan->ul_reas = $ua_reas;
            // $kepesertaan->klaim_id = $data->id;
            // $kepesertaan->save();

            $total_data++;
        }

        session()->flash('message-success',__('Migrasi berhasil'));

        return redirect()->route('klaim.index');
    }
}
