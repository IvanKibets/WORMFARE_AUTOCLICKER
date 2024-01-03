<?php

namespace App\Http\Livewire\Polis;

use Livewire\Component;
use App\Models\Polis;
use App\Models\Provinsi;
use App\Models\Produk;
use App\Models\Reasuradur;

class Edit extends Component
{
    public $no_polis,$nama,$provinsi_id,$alamat,$produks=[],$provinsi=[],$produk_id,$awal,$akhir,$keterangan,$status;
    public $masa_leluasa,$kadaluarsa_klaim,$pemulihan_kepesertaan,$reasuradur_id,$kelengkapan_berkas,$penyelesaian_perselisihan;
    public $iuran_tabbaru,$ujrah_atas_pengelolaan,$nisbah_hasil_investasi_peserta,$nisbah_hasil_investasi_pengelolaan,$surplus_uw_tabbaru;
    public $usia_minimal,$tipe,$model,$rate_persen,$ri_com,$ketentuan_uw_reas,$stnc,$kadaluarsa_reas,$no_perjanjian_reas,$perkalian_biaya_penutupan;
    public $rates=[],$reasuradur=[],$rate,$potong_langsung,$fee_base_brokerage,$maintenance,$admin_agency,$agen_penutup,$operasional_agency;
    public $ujroh_handling_fee_broker,$referal_fee,$pph,$ppn,$tujuan_pembayaran_nota_penutupan,$no_rekening,$bank,$tujuan_pembayaran_update;
    public $pks,$produksi_kontribusi,$surat_permohonan_tarif_kontribusi,$fitur_produk,$tabel_rate_premi,$spajks,$spajks_sementara,$copy_ktp;
    public $copy_npwp,$npwp,$copy_siup,$nota_penutupan,$tujuan_pembayaran_nama_penerima_refund,$bank_refund,$no_rekening_refund,$tujuan_pengiriman_surat;
    public $mcu_dicover_ajri,$kabupaten_id,$kode_kabupaten,$cabang_pemasaran,$ket_diskon,$sektor_keuangan,$sektor_ekonomi,$mitra_pengimbang,$kerjasama_pemasaran,$asuransi_mikro,$pic_marketing;
    public $dc_aaji,$dc_ojk,$office,$channel,$segment,$line_of_business,$source_of_business,$no_nota_penutupan,$no_perjanjian_kerjasama,$peninjauan_ulang,$pembayaran_klaim;
    public $retroaktif,$waiting_period,$rate_single_usia,$total_bp,$no_sb,$uw_limit,$margin_rate,$ri_comm,$share_reinsurance,$lost_ratio,$profit_margin,$contingency_margin,$gae,$business_source;
    public $refund,$refund_to_pengalihan,$dana_tabbaru_reas,$dana_ujroh_reas,$stop_loss,$cut_loss,$refund_cut_loss,$running_number,$running_number_peserta,$running_number_dn;
    public $running_no_surat,$biaya_polis_materai,$biaya_sertifikat;
    public $data;
    protected $listeners = ['set-id'=>'set_id'];
    public function render()
    {
        if(\Auth::user()->user_access_id!=1) redirect('pengajuan');

        return view('livewire.polis.edit');
    }

    public function mount(Polis $id)
    {
        $this->data = $id;
        $this->produks = Produk::get();
        $this->provinsi = Provinsi::orderBy('nama','ASC')->get();
        $this->reasuradur = Reasuradur::get();
        $this->no_polis = $this->data->no_polis;
        $this->nama = $this->data->nama;
        $this->alamat = $this->data->alamat;
        $this->provinsi = Provinsi::orderBy('nama','ASC')->get();
        $this->produks = Produk::get();
        $this->provinsi_id = $this->data->provinsi_id;
        $this->alamat = $this->data->alamat;
        $this->produk_id = $this->data->produk_id;
        $this->awal = $this->data->awal;
        $this->akhir = $this->data->akhir;
        $this->rate = $this->data->rate;
        $this->keterangan = $this->data->keterangan;
        $this->status = $this->data->status;
        $this->masa_leluasa = $this->data->masa_leluasa;
        $this->kelengkapan_berkas = $this->data->kelengkapan_berkas;
        $this->kadaluarsa_klaim = $this->data->kadaluarsa_klaim;
        $this->pemulihan_kepesertaan = $this->data->pemulihan_kepesertaan;
        $this->penyelesaian_perselisihan = $this->data->penyelesaian_perselisihan;
        $this->iuran_tabbaru = $this->data->iuran_tabbaru;
        $this->ujrah_atas_pengelolaan = $this->data->ujrah_atas_pengelolaan;
        $this->nisbah_hasil_investasi_peserta = $this->data->nisbah_hasil_investasi_peserta;
        $this->nisbah_hasil_investasi_pengelolaan = $this->data->nisbah_hasil_investasi_pengelolaan;
        $this->surplus_uw_tabbaru = $this->data->surplus_uw_tabbaru;
        $this->surplus_uw_peserta = $this->data->surplus_uw_peserta;
        $this->surplus_uw_pengelola = $this->data->surplus_uw_pengelola;
        $this->usia_minimal = $this->data->usia_minimal;
        $this->reasuradur_id = $this->data->reasuradur_id;
        $this->tipe = $this->data->tipe;
        $this->model = $this->data->model;
        $this->rate_persen = $this->data->rate_persen;
        $this->ri_com = $this->data->ri_com;
        $this->stnc = $this->data->stnc;
        $this->kadaluarsa_reas  = $this->data->kadaluarsa_reas;
        $this->no_perjanjian_reas  = $this->data->no_perjanjian_reas;
        $this->perkalian_biaya_penutupan  = $this->data->perkalian_biaya_penutupan;
        $this->potong_langsung = $this->data->potong_langsung;
        $this->fee_base_brokerage = $this->data->fee_base_brokerage;
        $this->maintenance = $this->data->maintenance;
        $this->admin_agency = $this->data->admin_agency;
        $this->agen_penutup = $this->data->agen_penutup;
        $this->operasional_agency = $this->data->operasional_agency;
        $this->ujroh_handling_fee_broker = $this->data->ujroh_handling_fee_broker;
        $this->referal_fee = $this->data->referal_fee;
        $this->pph = $this->data->pph;
        $this->ppn = $this->data->ppn;
        $this->tujuan_pembayaran_nota_penutupan = $this->data->tujuan_pembayaran_nota_penutupan; 
        $this->no_rekening = $this->data->no_rekening;
        $this->bank = $this->data->bank;
        $this->tujuan_pembayaran_update = $this->data->tujuan_pembayaran_update;
        $this->pks = $this->data->pks;
        $this->produksi_kontribusi = $this->data->produksi_kontribusi;
        $this->surat_permohonan_tarif_kontribusi = $this->data->surat_permohonan_tarif_kontribusi;
        $this->fitur_produk = $this->data->fitur_produk;
        $this->tabel_rate_premi = $this->data->tabel_rate_premi;
        $this->spajks = $this->data->spajks;
        $this->spajks_sementara = $this->data->spajks_sementara;
        $this->copy_ktp = $this->data->copy_ktp;
        $this->copy_npwp = $this->data->copy_npwp;
        $this->npwp = $this->data->npwp;
        $this->copy_siup = $this->data->copy_siup;
        $this->nota_penutupan = $this->data->nota_penutupan;
        $this->tujuan_pembayaran_nama_penerima_refund = $this->data->tujuan_pembayaran_nama_penerima_refund;
        $this->bank_refund = $this->data->bank_refund;
        $this->no_rekening_refund = $this->data->no_rekening_refund;
        $this->tujuan_pengiriman_surat = $this->data->tujuan_pengiriman_surat;
        $this->mcu_dicover_ajri = $this->data->mcu_dicover_ajri;
        $this->kabupaten_id = $this->data->kabupaten_id;
        $this->kode_kabupaten = $this->data->kode_kabupaten;
        $this->cabang_pemasaran = $this->data->cabang_pemasaran;
        $this->ket_diskon = $this->data->ket_diskon;
        $this->sektor_keuangan = $this->data->sektor_keuangan;
        $this->sektor_ekonomi = $this->data->sektor_ekonomi;
        $this->mitra_pengimbang = $this->data->mitra_pengimbang;
        $this->kerjasama_pemasaran = $this->data->kerjasama_pemasaran;
        $this->asuransi_mikro = $this->data->asuransi_mikro;
        $this->pic_marketing = $this->data->pic_marketing;
        $this->dc_aaji = $this->data->dc_aaji;
        $this->dc_ojk = $this->data->dc_ojk;
        $this->office = $this->data->office;
        $this->channel = $this->data->channel;
        $this->segment = $this->data->segment;
        $this->line_of_business = $this->data->line_of_business;
        $this->source_of_business = $this->data->source_of_business;
        $this->no_nota_penutupan = $this->data->no_nota_penutupan;
        $this->no_perjanjian_kerjasama = $this->data->no_perjanjian_kerjasama;
        $this->peninjauan_ulang = $this->data->peninjauan_ulang;
        $this->pembayaran_klaim = $this->data->pembayaran_klaim;
        $this->retroaktif = $this->data->retroaktif;
        $this->waiting_period = $this->data->waiting_period;
        $this->rate_single_usia = $this->data->rate_single_usia;
        $this->total_bp = $this->data->total_bp;
        $this->no_sb = $this->data->no_sb;
        $this->uw_limit = $this->data->uw_limit;
        $this->margin_rate = $this->data->margin_rate;
        $this->ri_comm = $this->data->ri_comm;
        $this->share_reinsurance = $this->data->share_reinsurance;
        $this->lost_ratio = $this->data->lost_ratio;
        $this->profit_margin = $this->data->profit_margin;
        $this->contingency_margin = $this->data->contingency_margin;
        $this->gae = $this->data->gae;
        $this->business_source = $this->data->business_source;
        $this->refund = $this->data->refund;
        $this->refund_to_pengalihan = $this->data->refund_to_pengalihan;
        $this->dana_tabbaru_reas = $this->data->dana_tabbaru_reas;
        $this->dana_ujroh_reas = $this->data->dana_ujroh_reas;
        $this->stop_loss = $this->data->stop_loss;
        $this->cut_loss = $this->data->cut_loss;
        $this->refund_cut_loss = $this->data->refund_cut_loss;
        $this->running_number_peserta = $this->data->running_number_peserta;
        $this->running_number_dn = $this->data->running_number_dn;
        $this->running_number = $this->data->running_number;
        $this->running_no_surat = $this->data->running_no_surat;
        $this->biaya_polis_materai = $this->data->biaya_polis_materai;
        $this->biaya_sertifikat = $this->data->biaya_sertifikat;
    }
    
    public function updated($propertyName)
    {
        if($propertyName =='iuran_tabbaru' and $this->iuran_tabbaru > 0) $this->ujrah_atas_pengelolaan = 100 - $this->iuran_tabbaru;
        if($propertyName =='akhir'){
            if(date('Y-m-d') > $this->akhir){
                $this->status  = 'Mature';
            }

            if(date('Y-m-d') <= $this->akhir){
                $this->status  = 'Inforce';
            }
        }
    }

    public function submit_issued()
    {
        $this->data->status_approval = 1;
        $this->data->save();

        $this->save();
    }

    public function submit_draft()
    {
        $this->data->status_approval = 0;
        $this->data->save();    
        
        $this->save();
    }

    public function save()
    {
        $this->validate([
            'nama'=>'required',
            'provinsi_id'=>'required',
            'alamat'=>'required',
            'line_of_business' => 'required',
            // 'iuran_tabbaru' => 'required',
            'ket_diskon' => 'required',
            'masa_leluasa' => 'required'
        ]);

        $this->data->nama = $this->nama;
        $this->data->provinsi_id = $this->provinsi_id;
        $this->data->alamat = $this->alamat;
        $this->data->produk_id = $this->produk_id;
        $this->data->awal = $this->awal;
        $this->data->akhir = $this->akhir;
        $this->data->rate = $this->rate;
        $this->data->keterangan = $this->keterangan;
        $this->data->status = $this->status;
        $this->data->masa_leluasa = $this->masa_leluasa;
        $this->data->kelengkapan_berkas = $this->kelengkapan_berkas;
        $this->data->kadaluarsa_klaim = $this->kadaluarsa_klaim;
        $this->data->pemulihan_kepesertaan = $this->pemulihan_kepesertaan;
        $this->data->penyelesaian_perselisihan = $this->penyelesaian_perselisihan;
        $this->data->iuran_tabbaru = $this->iuran_tabbaru;
        $this->data->ujrah_atas_pengelolaan = $this->ujrah_atas_pengelolaan;
        $this->data->nisbah_hasil_investasi_peserta = $this->nisbah_hasil_investasi_peserta;
        $this->data->nisbah_hasil_investasi_pengelolaan = $this->nisbah_hasil_investasi_pengelolaan;
        $this->data->surplus_uw_tabbaru = $this->surplus_uw_tabbaru;
        $this->data->surplus_uw_peserta = $this->surplus_uw_peserta;
        $this->data->surplus_uw_pengelola = $this->surplus_uw_pengelola;
        $this->data->usia_minimal = $this->usia_minimal;
        $this->data->reasuradur_id = $this->reasuradur_id;
        $this->data->tipe = $this->tipe;
        $this->data->model = $this->model;
        $this->data->rate_persen = $this->rate_persen;
        $this->data->ri_com = $this->ri_com;
        $this->data->stnc = $this->stnc;
        $this->data->kadaluarsa_reas  = $this->kadaluarsa_reas;
        $this->data->no_perjanjian_reas  = $this->no_perjanjian_reas;
        $this->data->perkalian_biaya_penutupan  = $this->perkalian_biaya_penutupan;
        $this->data->potong_langsung = $this->potong_langsung;
        $this->data->fee_base_brokerage = $this->fee_base_brokerage;
        $this->data->maintenance = $this->maintenance;
        $this->data->admin_agency = $this->admin_agency;
        $this->data->agen_penutup = $this->agen_penutup;
        $this->data->operasional_agency = $this->operasional_agency;
        $this->data->ujroh_handling_fee_broker = $this->ujroh_handling_fee_broker;
        $this->data->referal_fee = $this->referal_fee;
        $this->data->pph = $this->pph;
        $this->data->ppn = $this->ppn;
        $this->data->tujuan_pembayaran_nota_penutupan = $this->tujuan_pembayaran_nota_penutupan; 
        $this->data->no_rekening = $this->no_rekening;
        $this->data->bank = $this->bank;
        $this->data->tujuan_pembayaran_update = $this->tujuan_pembayaran_update;
        $this->data->pks = $this->pks;
        $this->data->produksi_kontribusi = $this->produksi_kontribusi;
        $this->data->surat_permohonan_tarif_kontribusi = $this->surat_permohonan_tarif_kontribusi;
        $this->data->fitur_produk = $this->fitur_produk;
        $this->data->tabel_rate_premi = $this->tabel_rate_premi;
        $this->data->spajks = $this->spajks;
        $this->data->spajks_sementara = $this->spajks_sementara;
        $this->data->copy_ktp = $this->copy_ktp;
        $this->data->copy_npwp = $this->copy_npwp;
        $this->data->npwp = $this->npwp;
        $this->data->copy_siup = $this->copy_siup;
        $this->data->nota_penutupan = $this->nota_penutupan;
        $this->data->tujuan_pembayaran_nama_penerima_refund = $this->tujuan_pembayaran_nama_penerima_refund;
        $this->data->bank_refund = $this->bank_refund;
        $this->data->no_rekening_refund = $this->no_rekening_refund;
        $this->data->tujuan_pengiriman_surat = $this->tujuan_pengiriman_surat;
        $this->data->mcu_dicover_ajri = $this->mcu_dicover_ajri;
        $this->data->kabupaten_id = $this->kabupaten_id;
        $this->data->kode_kabupaten = $this->kode_kabupaten;
        $this->data->cabang_pemasaran = $this->cabang_pemasaran;
        $this->data->ket_diskon = $this->ket_diskon;
        $this->data->sektor_keuangan = $this->sektor_keuangan;
        $this->data->sektor_ekonomi = $this->sektor_ekonomi;
        $this->data->mitra_pengimbang = $this->mitra_pengimbang;
        $this->data->kerjasama_pemasaran = $this->kerjasama_pemasaran;
        $this->data->asuransi_mikro = $this->asuransi_mikro;
        $this->data->pic_marketing = $this->pic_marketing;
        $this->data->dc_aaji = $this->dc_aaji;
        $this->data->dc_ojk = $this->dc_ojk;
        $this->data->office = $this->office;
        $this->data->channel = $this->channel;
        $this->data->segment = $this->segment;
        $this->data->line_of_business = $this->line_of_business;
        $this->data->source_of_business = $this->source_of_business;
        $this->data->no_nota_penutupan = $this->no_nota_penutupan;
        $this->data->no_perjanjian_kerjasama = $this->no_perjanjian_kerjasama;
        $this->data->peninjauan_ulang = $this->peninjauan_ulang;
        $this->data->pembayaran_klaim = $this->pembayaran_klaim;
        $this->data->retroaktif = $this->retroaktif;
        if($this->waiting_period) $this->data->waiting_period = $this->waiting_period;
        $this->data->rate_single_usia = $this->rate_single_usia;
        $this->data->total_bp = $this->total_bp;
        $this->data->no_sb = $this->no_sb;
        $this->data->uw_limit = $this->uw_limit;
        $this->data->margin_rate = $this->margin_rate;
        $this->data->ri_comm = $this->ri_comm;
        $this->data->share_reinsurance = $this->share_reinsurance;
        $this->data->lost_ratio = $this->lost_ratio;
        $this->data->profit_margin = $this->profit_margin;
        $this->data->contingency_margin = $this->contingency_margin;
        $this->data->gae = $this->gae;
        $this->data->business_source = $this->business_source;
        $this->data->refund = $this->refund;
        $this->data->refund_to_pengalihan = $this->refund_to_pengalihan;
        $this->data->dana_tabbaru_reas = $this->dana_tabbaru_reas;
        $this->data->dana_ujroh_reas = $this->dana_ujroh_reas;
        $this->data->stop_loss = $this->stop_loss;
        $this->data->cut_loss = $this->cut_loss;
        $this->data->refund_cut_loss = $this->refund_cut_loss;
        $this->data->running_number_peserta = $this->running_number_peserta;
        $this->data->running_number_dn = $this->running_number_dn;
        $this->data->running_number = $this->running_number;
        $this->data->running_no_surat = $this->running_no_surat;
        $this->data->biaya_polis_materai = $this->biaya_polis_materai;
        $this->data->biaya_sertifikat = $this->biaya_sertifikat;
        $this->data->save();  

        session()->flash('message-success',__('Polis berhasil disubmit'));

        return redirect()->route('polis.index');
    }
}
