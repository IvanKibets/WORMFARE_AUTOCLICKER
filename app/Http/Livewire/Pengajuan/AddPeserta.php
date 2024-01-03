<?php

namespace App\Http\Livewire\Pengajuan;

use Livewire\Component;
use App\Models\Kepesertaan;
use App\Models\Rate;
use App\Models\UnderwritingLimit;
use Livewire\WithFileUploads;

class AddPeserta extends Component
{
    use WithFileUploads;
    public $type = 1,$nama,$no_ktp,$alamat,$no_telepon,$pekerjaan,$bank,$cab,$no_closing,$no_akad_kredit;
    public $tanggal_lahir,$jenis_kelamin,$tanggal_mulai,$tanggal_akhir,$uang_pertanggungan,$tinggi_badan;
    public $berat_badan,$umur=0,$perhitungan_usia,$bmi,$kriteria_badan,$polis_id,$masa_asuransi,$file;
    public $file_spk,$selected,$premi=0,$rate,$masa_bulan,$uw="",$pemeriksaan_kesehatan,$ep,$ep_persen;
    protected $listeners = ['set_polis_id'=>'set_polis_id','set_id'=>'set_id','set_clear_id'=>'set_clear_id'];
    public function render()
    {
        return view('livewire.pengajuan.add-peserta');
    }

    public function set_clear_id()
    {
        $this->selected = "";
    }
    public function set_id($id)
    {
        $this->selected = Kepesertaan::find($id);
        if($this->selected){
            $this->nama = $this->selected->nama;
            $this->no_ktp = $this->selected->no_ktp;
            $this->alamat = $this->selected->alamat;
            $this->no_telepon = $this->selected->no_telepon;
            $this->pekerjaan = $this->selected->pekerjaan;
            $this->bank = $this->selected->bank;
            $this->no_closing = $this->selected->no_closing;
            $this->no_akad_kredit = $this->selected->no_akad_kedit;
            $this->tanggal_lahir = $this->selected->tanggal_lahir;
            $this->jenis_kelamin = $this->selected->jenis_kelamin;
            $this->tanggal_mulai = $this->selected->tanggal_mulai;
            $this->tanggal_akhir = $this->selected->tanggal_akhir;
            $this->uang_pertanggungan = $this->selected->basic;
            $this->tinggi_badan = $this->selected->tinggi_badan;
            $this->berat_badan = $this->selected->berat_badan;
            $this->umur = $this->selected->usia;
            $this->bmi = $this->selected->bmi;
            $this->kriteria_badan = $this->selected->kb;
            $this->premi = $this->selected->kontribusi;
            $this->ep = $this->selected->extra_kontribusi;
            $this->ep_persen = $this->selected->extra_kontribusi_percen;
        }else
            $this->selected = "";
    }
    public function set_polis_id($polis_id)
    {
        $this->polis_id = $polis_id;
    }
    public function mount($perhitungan_usia,$masa_asuransi,$polis_id)
    {
        $this->perhitungan_usia = $perhitungan_usia;
        $this->polis_id = $polis_id;
        $this->masa_asuransi = $masa_asuransi;
    }
    public function updated($propertyName)
    {
        if($propertyName=='tanggal_lahir' and $this->tanggal_lahir){
            $this->umur = hitung_umur($this->tanggal_lahir,$this->perhitungan_usia);
        }

        if($this->tinggi_badan and $this->berat_badan and $this->umur){
            $tinggi_badan = $this->tinggi_badan;
            $tinggi_badan  = $tinggi_badan / 100;
            $tinggi_badan  = $tinggi_badan * $tinggi_badan;
            $berat_badan   = $this->berat_badan;

            $this->kriteria_badan = "";
            $bmi_ = $berat_badan / $tinggi_badan;
            $this->bmi = toFixed($bmi_,2);
            $this->ep = 0;

            if(inRange($this->bmi,0,18.49)){
                $this->kriteria_badan = 'Underweight';
                $this->ep_persen = 25;
            }
            if(inRange($this->bmi,18.5, 24.99)){
                $this->kriteria_badan = 'ideal';
                $this->ep_persen = 0;
            }
            if(inRange($this->bmi,25, 29.99)){
                $this->kriteria_badan = 'Overweight';
                $this->ep_persen = 25;
            }
            if(inRange($this->bmi,30, 34.99)){
                $this->kriteria_badan = 'Ob. Kelas1';
                $this->ep_persen = 50;
            }
            if(inRange($this->bmi,35, 39.99)){
                $this->kriteria_badan = 'Ob. Kelas2';
                $this->ep_persen = 75;
            }
            if(inRange($this->bmi,40, 99)){
                $this->kriteria_badan = 'Ob. Kelas3';
                $this->ep_persen = 100;
            }
        }

        if($this->umur and $this->tanggal_mulai and $this->tanggal_akhir and $this->uang_pertanggungan){
            $this->masa_bulan =  hitung_masa_bulan($this->tanggal_mulai,$this->tanggal_akhir,$this->masa_asuransi);

            $rate = Rate::where(['tahun'=>$this->umur,'bulan'=>$this->masa_bulan,'polis_id'=>$this->polis_id])->first();

            if(!$rate){
                $this->rate = 0;
                $this->premi = 0;
            }else{
                $this->premi = replace_idr($this->uang_pertanggungan) * $rate->rate/1000;
                $this->rate = $rate->rate;
            }
        }

        if($this->premi>0 and $this->ep_persen>0){
            $this->ep = ($this->premi*$this->ep_persen)/100;
        }

        if($this->uang_pertanggungan and $this->umur){
            $uw = UnderwritingLimit::whereRaw(replace_idr($this->uang_pertanggungan)." BETWEEN min_amount and max_amount")->where(['usia'=>$this->umur,'polis_id'=>$this->polis_id])->first();
            if($uw) $this->uw = $uw->keterangan;
        }
    }

    public function temp_upload()
    {
        $this->validate([
            'file'=>'required|mimes:xlsx|max:10240', // 10MB maksimal
        ]);

        $path = $this->file->getRealPath();
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $reader->setReadDataOnly(true);
        $xlsx = $reader->load($path);
        $sheetData = $xlsx->getActiveSheet()->toArray();
        $total_data = 0;
        $total_double = 0;
        $total_success = 0;
        Kepesertaan::where(['polis_id'=>$this->polis_id, 'user_id'=>\Auth::user()->id, 'is_temp'=>1,'is_double'=>1])->delete();
        $insert = [];
        foreach($sheetData as $key => $item){
            if($key<=1) continue;
            /**
             * Skip
             * Nama, Tanggal lahir
             */
            if($item[1]=="" || $item[10]=="") continue;
            $insert[$total_data]['polis_id'] = $this->polis_id;
            $insert[$total_data]['nama'] = $item[1];
            $insert[$total_data]['no_ktp'] = $item[2];
            $insert[$total_data]['alamat'] = $item[3];
            $insert[$total_data]['no_telepon'] = $item[4];
            $insert[$total_data]['pekerjaan'] = $item[5];
            $insert[$total_data]['bank'] = $item[6];
            $insert[$total_data]['cab'] = $item[7];
            $insert[$total_data]['no_closing'] = $item[8];
            $insert[$total_data]['no_akad_kredit'] = $item[9];
            $insert[$total_data]['tanggal_lahir'] = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item[10])->format('Y-m-d');
            $insert[$total_data]['jenis_kelamin'] = $item[11];
            if($item[12]) $insert[$total_data]['tanggal_mulai'] = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item[12])->format('Y-m-d');
            if($item[13]) $insert[$total_data]['tanggal_akhir'] = @\PhpOffice\PhpSpreadsheet\Shared\Date::excelToDateTimeObject($item[13])->format('Y-m-d');
            $insert[$total_data]['basic'] = $item[14];
            $insert[$total_data]['tinggi_badan'] = $item[15];
            $insert[$total_data]['berat_badan'] = $item[16];
            $insert[$total_data]['kontribusi'] = 0;
            $insert[$total_data]['is_temp'] = 1;
            $insert[$total_data]['is_double'] = 2;
            $insert[$total_data]['user_id'] = \Auth::user()->id;
            $total_data++;
        }

        if(count($insert)>0){
            Kepesertaan::insert($insert);
        }

        $this->emit('reload-row');
        $this->emit('attach-file');
        $this->emit('modal','hide');
    }

    public function save()
    {
        $validate = [
            'nama' => 'required',
            'tanggal_lahir' => 'required',
            'jenis_kelamin' => 'required',
            'tanggal_mulai' => 'required',
            'tanggal_akhir' => 'required',
            'uang_pertanggungan' => 'required',
            'no_ktp' => 'required',
            'tinggi_badan' => 'required',
            'berat_badan' => 'required',
        ];

        /**
         * Validate file spk
         */
        if($this->uw!="" and $this->uw!='GOA')
        // if($this->file_spk || !$this->selected)
        {
            $validate['file_spk'] = 'required|mimes:xlsx,pdf,xlx,jpeg,jpg,png|max:10240';
        }

        if(!$this->selected){
            $validate['pemeriksaan_kesehatan'] = 'required|mimes:xlsx,pdf,xlx,jpeg,jpg,png|max:10240';
        }

        $this->validate($validate);

        $check =  Kepesertaan::where(['polis_id'=>$this->polis_id,'nama'=>$this->nama,'tanggal_lahir'=>$this->tanggal_lahir])->first();
        $data = new Kepesertaan();

        if($this->selected!="") $data = $this->selected;
        
        if($check){
            $data->is_double = 1;
            $data->parent_id = $check->id;
        }

        $data->polis_id = $this->polis_id;
        $data->nama = $this->nama;
        $data->no_ktp = $this->no_ktp;
        $data->alamat = $this->alamat;
        $data->no_telepon = $this->no_telepon;
        $data->pekerjaan = $this->pekerjaan;
        $data->bank = $this->bank;
        $data->tanggal_lahir = $this->tanggal_lahir;
        $data->jenis_kelamin = $this->jenis_kelamin;
        $data->tanggal_mulai = $this->tanggal_mulai;
        $data->tanggal_akhir = $this->tanggal_akhir;
        $data->basic = replace_idr($this->uang_pertanggungan);
        $data->tinggi_badan = $this->tinggi_badan;
        $data->berat_badan = $this->berat_badan;
        $data->is_temp = 1;
        $data->usia = $this->umur;
        $data->bmi = $this->bmi;
        $data->kb = $this->kriteria_badan;
        $data->masa = hitung_masa($this->tanggal_mulai,$this->tanggal_akhir);
        $data->masa_bulan = hitung_masa_bulan($this->tanggal_mulai,$this->tanggal_akhir,$this->masa_asuransi);
        $data->user_id = \Auth::user()->id;
        $data->kontribusi = $this->premi;
        $data->extra_kontribusi = $this->ep;
        $data->extra_kontribusi_percen = $this->ep_persen;
        $data->save();

        if($this->file_spk){
            $name = $data->id .".".$this->file_spk->extension();
            $this->file_spk->storeAs("public/pengajuan/", $name);
            $data->file_spk = "storage/pengajuan/{$name}";
            $data->save();
        }

        if($this->pemeriksaan_kesehatan){
            $name = "{$data->id}_pemeriksaan_kesehatan.".$this->pemeriksaan_kesehatan->extension();
            $this->pemeriksaan_kesehatan->storeAs("public/pengajuan/", $name);
            $data->pemeriksaan_kesehatan = "storage/pengajuan/{$name}";
            $data->save();
        }

        $this->reset('nama','no_ktp','alamat','no_telepon','pekerjaan','bank','cab','no_closing','no_akad_kredit','tanggal_lahir','jenis_kelamin','tanggal_mulai',
                    'tanggal_akhir','uang_pertanggungan','tinggi_badan','berat_badan','umur','bmi','kriteria_badan','selected','ep','ep_persen');
        $this->emit('reload-row');
        $this->emit('reload-page');
        $this->emit('attach-file');
        $this->emit('modal','hide');
    }
}
