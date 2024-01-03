<?php

namespace App\Http\Livewire\Migration;

use App\Models\BankAccount;
use App\Models\Income;
use App\Models\Policy;
use Livewire\Component;
use App\Models\MigrationData;
use Livewire\WithFileUploads;
use Livewire\WithPagination;

class Index extends Component
{
    use WithFileUploads;
    use WithPagination;
    protected $paginationTheme = 'bootstrap';

    public $file,$keyword,$file_syariah;

    public function render()
    {
        $data = MigrationData::orderBy('id');
        if($this->keyword) $data = $data->where(function($table){
            foreach(\Illuminate\Support\Facades\Schema::getColumnListing('migration_data') as $column){
                $table->orWhere($column,'LIKE',"%{$this->keyword}%");
            }
        });
        return view('livewire.migration.index')->with(['data'=>$data->paginate(100)]);
    }

    public function upload_syariah()
    {
        $this->validate([
            'file_syariah'=>'required|mimes:xls,xlsx|max:51200' // 50MB maksimal
        ]);

        $path = $this->file_syariah->getRealPath();
       
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(true);
        $data = $reader->load($path);
        $sheetData = $data->getActiveSheet()->toArray();
        
        if(count($sheetData) > 0){
            $countLimit = 1;
            foreach($sheetData as $key => $i){
                if($key<5) continue; // skip header
                
                foreach($i as $k=>$a){$i[$k] = trim($a);}
                
                if($i[1]=="" || $i[29] == "") continue; // skip

                $no_register = $i[1];
                $nomor_invoice = $i[2];
                $nomor_polis = $i[3];
                $nama_pemegang_polis = $i[4];
                $jenis_produk = $i[5];
                $tanggal_terbit = $i[6] ? @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($i[6]) : '';
                $jatuh_tempo = $i[7] ? @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($i[7]) : '';
                $aging = $i[8];
                $premi_bruto = $i[9];
                $extra_premi = $i[10];
                $manajemen_fee = $i[11];
                $fee_base = $i[13];
                $lain_lain = $i[14];
                $pajak_ppn = $i[15];
                $pajak_pph = $i[16];
                $pajak_lain = $i[17];
                $biaya_administrasi = $i[18];
                $biaya_polis = $i[19]; 
                $biaya_sertifikat = $i[20];
                $biaya_materai = $i[21];
                $premi_netto = $i[22];
                $jumlah_bayar = $i[23];
                $tanggal_pendapatan = $i[24] ? @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($i[24]) : '';
                $no_rekening = $i[25];
                $bank = $i[26];
                $amount = $i[27];
                $pembayaran = $i[28];
                $piutang = $i[29];
                $status = $i[30];
                $jumlah_peserta = $i[31];
                $period = $i[32];

                $migration = MigrationData::where('no_register',$no_register)->first();
                
                if($migration) continue;
                
                $migration = new MigrationData();
                $migration->no_register = $no_register;
                $migration->nomor_invoice = $nomor_invoice;
                $migration->nomor_polis = $nomor_polis;
                $migration->nama_pemegang_polis = $nama_pemegang_polis;
                $migration->jenis_produk = $jenis_produk;
                if($tanggal_terbit) $migration->tanggal_terbit = date('Y-m-d',$tanggal_terbit);
                if($jatuh_tempo) $migration->jatuh_tempo = date('Y-m-d',$jatuh_tempo);
                $migration->aging = $aging;
                $migration->premi_bruto = $premi_bruto;
                $migration->extra_premi = $extra_premi;
                $migration->fee_base = $fee_base;
                $migration->lain_lain = $lain_lain;
                $migration->pajak_ppn = $pajak_ppn;
                $migration->pajak_pph = $pajak_pph;
                $migration->pajak_lain = $pajak_lain;
                $migration->biaya_administrasi = $biaya_administrasi;
                $migration->biaya_polis = $biaya_polis;
                $migration->biaya_sertifikat = $biaya_sertifikat;
                $migration->biaya_materai = $biaya_materai;
                $migration->premi_netto = $premi_netto;
                $migration->jumlah_bayar = $jumlah_bayar;
                if($tanggal_pendapatan) $migration->tanggal_pendapatan = date('Y-m-d',$tanggal_pendapatan);
                $migration->no_rekening = $no_rekening;
                $migration->bank = $bank;
                $migration->amount = $amount;
                $migration->pembayaran = $pembayaran;
                $migration->piutang = $piutang;
                $migration->status = $status;
                $migration->jumlah_peserta = $jumlah_peserta;
                $migration->period = $period;
                $migration->manajemen_fee = $manajemen_fee;
                $migration->save();

                $policy = Policy::where('no_polis',$nomor_polis)->first();
                if(!$policy){
                    $policy = new Policy();
                    $policy->no_polis = $nomor_polis;
                    $policy->pemegang_polis = $nama_pemegang_polis;
                    $policy->save();
                }

                $rekening_bank = BankAccount::where(['no_rekening'=>$no_rekening])->first();
                if(!$rekening_bank){
                    $rekening_bank = new BankAccount();
                    $rekening_bank->no_rekening = $no_rekening;
                    $rekening_bank->owner = $nama_pemegang_polis;
                    $rekening_bank->bank = $bank; 
                    $rekening_bank->is_client = 1; 
                    $rekening_bank->save();
                }

                // insert income premium receivable
                $income = new Income();
                $income->user_id = \Auth::user()->id;
                $income->no_voucher = generate_no_voucher_income();
                $income->reference_no = $migration->nomor_invoice;
                $income->reference_date = $migration->tanggal_terbit;
                $income->nominal = $amount;
                $income->payment_amount = $pembayaran;
                $income->client = $nomor_polis .' / '. $nama_pemegang_polis;
                $income->reference_type = 'Premium Receivable';
                $income->transaction_table = 'Migration';
                $income->transaction_id = $migration->id;
                $income->due_date = $migration->jatuh_tempo;
                $income->policy_id = $policy->id;
                $income->payment_date = $migration->tanggal_pendapatan;
                $income->type = 2; // syariah
                $income->rekening_bank_id = $rekening_bank->id;
                $income->status = 2;
                $income->save();
            }
        }
        
        session()->flash('message-success','Upload success !');   
        
        return redirect()->route('migration.index');
    }

    public function upload()
    {
        $this->validate([
            'file'=>'required|mimes:xls,xlsx|max:51200' // 50MB maksimal
        ]);

        $path = $this->file->getRealPath();
       
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(true);
        $data = $reader->load($path);
        $sheetData = $data->getActiveSheet()->toArray();
        
        if(count($sheetData) > 0){
            $countLimit = 1;
            foreach($sheetData as $key => $i){
                if($key<5) continue; // skip header
                
                foreach($i as $k=>$a){$i[$k] = trim($a);}
                
                if($i[1]=="" || $i[29] == "") continue; // skip

                $no_register = $i[1];
                $nomor_invoice = $i[2];
                $nomor_polis = $i[3];
                $nama_pemegang_polis = $i[4];
                $sub_polis = $i[5];
                $sub_pemegang_polis = $i[6];
                $jenis_produk = $i[7];
                $line_of_business = $i[8];
                $tanggal_terbit = $i[9] ? @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($i[9]) : '';
                $jatuh_tempo = $i[10] ? @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($i[10]) : '';
                $aging = $i[11];
                $klasifikasi_aging = $i[12];
                $premi_bruto = $i[13];
                $extra_premi = $i[14];
                $premi_gross = $i[15];
                $discount = $i[16];
                $komisi = $i[17];
                $fee_base = $i[18];
                $lain_lain = $i[19];
                $pajak_ppn = $i[20];
                $pajak_pph = $i[21];
                $pajak_lain = $i[22];
                $biaya_administrasi = $i[23];
                $biaya_polis = $i[24];
                $biaya_sertifikat = $i[25];
                $biaya_materai = $i[26];
                $premi_netto = $i[27];
                $jumlah_bayar = $i[28];
                $tanggal_pendapatan = $i[29] ? @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($i[29]) : '';
                $no_rekening = $i[30];
                $bank = $i[31];
                $amount = $i[32];
                $pembayaran = $i[33];
                $piutang = $i[34];
                $status = $i[35];
                $pengajuan_komisi = $i[36] ? @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToTimestamp($i[36]) : '';
                $jumlah_peserta = $i[37];
                $no_memo = $i[38];
                $period = $i[39];

                $migration = MigrationData::where('no_register',$no_register)->first();
                
                if($migration) continue;
                
                $migration = new MigrationData();
                $migration->no_register = $no_register;
                $migration->nomor_invoice = $nomor_invoice;
                $migration->nomor_polis = $nomor_polis;
                $migration->nama_pemegang_polis = $nama_pemegang_polis;
                $migration->sub_polis = $sub_polis;
                $migration->sub_pemegang_polis = $sub_pemegang_polis;
                $migration->jenis_produk = $jenis_produk;
                $migration->line_of_business = $line_of_business;
                if($tanggal_terbit) $migration->tanggal_terbit = date('Y-m-d',$tanggal_terbit);
                if($jatuh_tempo) $migration->jatuh_tempo = date('Y-m-d',$jatuh_tempo);
                $migration->aging = $aging;
                $migration->klasifikasi_aging = $klasifikasi_aging;
                $migration->premi_bruto = $premi_bruto;
                $migration->extra_premi = $extra_premi;
                $migration->premi_gross = $premi_gross;
                $migration->discount = $discount;
                $migration->komisi = $komisi;
                $migration->fee_base = $fee_base;
                $migration->lain_lain = $lain_lain;
                $migration->pajak_ppn = $pajak_ppn;
                $migration->pajak_pph = $pajak_pph;
                $migration->pajak_lain = $pajak_lain;
                $migration->biaya_administrasi = $biaya_administrasi;
                $migration->biaya_polis = $biaya_polis;
                $migration->biaya_sertifikat = $biaya_sertifikat;
                $migration->biaya_materai = $biaya_materai;
                $migration->premi_netto = $premi_netto;
                $migration->jumlah_bayar = $jumlah_bayar;
                if($tanggal_pendapatan) $migration->tanggal_pendapatan = date('Y-m-d',$tanggal_pendapatan);
                $migration->no_rekening = $no_rekening;
                $migration->bank = $bank;
                $migration->amount = $amount;
                $migration->pembayaran = $pembayaran;
                $migration->piutang = $piutang;
                $migration->status = $status;
                if($pengajuan_komisi) $migration->pengajuan_komisi =  date('Y-m-d',$pengajuan_komisi);
                $migration->jumlah_peserta = $jumlah_peserta;
                $migration->no_memo = $no_memo;
                $migration->period = $period;
                $migration->save();

                $policy = Policy::where('no_polis',$nomor_polis)->first();
                if(!$policy){
                    $policy = new Policy();
                    $policy->no_polis = $nomor_polis;
                    $policy->pemegang_polis = $nama_pemegang_polis;
                    $policy->save();
                }

                $rekening_bank = BankAccount::where(['no_rekening'=>$no_rekening])->first();
                if(!$rekening_bank){
                    $rekening_bank = new BankAccount();
                    $rekening_bank->no_rekening = $no_rekening;
                    $rekening_bank->owner = $nama_pemegang_polis;
                    $rekening_bank->bank = $bank; 
                    $rekening_bank->is_client = 1; 
                    $rekening_bank->save();
                }

                // insert income premium receivable
                $income = new Income();
                $income->user_id = \Auth::user()->id;
                $income->no_voucher = generate_no_voucher_income();
                $income->reference_no = $migration->nomor_invoice;
                $income->reference_date = $migration->tanggal_terbit;
                $income->nominal = $amount;
                $income->payment_amount = $pembayaran;
                $income->client = $nomor_polis .' / '. $nama_pemegang_polis;
                $income->reference_type = 'Premium Receivable';
                $income->transaction_table = 'Migration';
                $income->transaction_id = $migration->id;
                $income->due_date = $migration->jatuh_tempo;
                $income->policy_id = $policy->id;
                $income->payment_date = $migration->tanggal_pendapatan;
                $income->type = 1;
                $income->rekening_bank_id = $rekening_bank->id;
                $income->status = 2;
                $income->save();
            }
        }
        
        session()->flash('message-success','Upload success !');   
        
        return redirect()->route('migration.index');
    }
}
