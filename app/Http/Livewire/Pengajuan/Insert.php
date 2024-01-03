<?php

namespace App\Http\Livewire\Pengajuan;

use App\Models\Pengajuan;
use Livewire\Component;
use App\Models\Polis;
use App\Models\Kepesertaan;
use Livewire\WithFileUploads;
use App\Jobs\PengajuanCalculate;

class Insert extends Component
{
    use WithFileUploads;
    public $polis=[],$file,$polis_id,$no_pengajuan,$kepesertaan=[],$check_all=0,$check_id=[],$check_arr;
    public $total_pengajuan=0,$perhitungan_usia=1,$masa_asuransi=1,$message_error = '',$is_calculate=false,$transaction_id,$is_draft=false;
    public $find_polis;
    protected $listeners = ['set_calculate'=>'set_calculate','attach-file'=>'attach_file'];
    public function render()
    {
        return view('livewire.pengajuan.insert');
    }

    public function mount()
    {        
        \LogActivity::add("[web][Pengajuan][Insert] Insert Pengajuan");

        $this->transaction_id = date('dmyHis');
        $this->no_pengajuan =  date('dmy').str_pad((Pengajuan::count()+1),6, '0', STR_PAD_LEFT);
        $this->polis = Polis::where('status_approval',1)->get();

        // if(\Auth::user()->user_access_id ==2){
        //     $this->polis_id = \Auth::user()->polis_id;
            
        //     Kepesertaan::where(['polis_id'=>$this->polis_id,'is_temp'=>1,'user_id'=>\Auth::user()->id])->delete();

        //     $this->emit('set_polis_id',$this->polis_id);
        //     $this->find_polis = Polis::find($this->polis_id);
        //     // if($find_polis){
        //     //     if($find_polis->rate_->count()==0 || $find_polis->uw_limit_->count()==0)
        //     //         $this->message_error = 'Rate / UW limit belum tersedia';
        //     //     else
        //     //         $this->message_error = '';
        //     // }
        // }
    }

    public function attach_file()
    {
        $this->file = Kepesertaan::where(['polis_id'=>$this->polis_id,'is_temp'=>1,'user_id'=>\Auth::user()->id])->count();
    }

    public function draft()
    {
        $this->is_draft = true;
        $this->save();
    }

    public function set_calculate($condition=false)
    {
        $this->is_calculate = $condition;
        $this->emit('reload-row');
        $this->total_pengajuan = Kepesertaan::where(['polis_id'=>$this->polis_id,'is_temp'=>1,'user_id'=>\Auth::user()->id])->count();
    }
    public function calculate()
    {
        $this->is_calculate = true;
        PengajuanCalculate::dispatch($this->polis_id,$this->perhitungan_usia,$this->masa_asuransi,$this->transaction_id,'new');
    }

    public function clear_file()
    {
        Kepesertaan::where(['polis_id'=>$this->polis_id,'is_temp'=>1,'user_id'=>\Auth::user()->id])->delete();
        $this->emit('reload-row');
        $this->kepesertaan = [];
        $this->reset('file');
    }

    public function keepAll()
    {
        foreach($this->kepesertaan as $k => $item){
            $item->is_double = 0;
            $item->parent_id = 0;
            $item->save();
        }
        $this->check_id = [];
        $this->emit('reload-page');
        $this->emit('message-success','Data berhasil diproses');
    }

    public function deleteAll()
    {
        foreach($this->kepesertaan as $k => $item){
            Kepesertaan::find($item->id)->delete();
        }
        $this->check_id = [];

        $this->emit('reload-page');
        $this->emit('message-success','Data berhasil diproses');
    }

    public function delete(Kepesertaan $data)
    {
        $data->delete();
        $this->emit('reload-page');
    }

    public function keep(Kepesertaan $data)
    {
        $data->is_double = 0;;
        $data->save();

        $this->emit('reload-page');
    }

    public function updated($propertyName)
    {
        $this->is_calculate = false;

        if($propertyName=='polis_id'){
            $this->emit('set_polis_id',$this->polis_id);
            $this->find_polis = Polis::find($this->polis_id);
            if($this->find_polis){
                if($this->find_polis->rate_->count()==0 || $this->find_polis->uw_limit_->count()==0)
                    $this->message_error = 'Rate / UW limit belum tersedia';
                else
                    $this->message_error = '';
            }
        }

        if($propertyName=='check_all' and $this->check_all==1){
            foreach($this->kepesertaan as $k => $item){
                $this->check_id[$k] = $item->id;
            }
        }elseif($propertyName=='check_all' and $this->check_all==0){
            $this->check_id = [];
        }

        if($propertyName=='polis_id'){
            Kepesertaan::where(['polis_id'=>$this->polis_id,'is_temp'=>1,'user_id'=>\Auth::user()->user_id])->delete();
            $this->emit('set_polis_id',$this->polis_id);
        } 

        if($propertyName=='file') $this->temp_upload();
    }

    public function temp_upload()
    {
        $this->validate([
            'file'=>'required|mimes:xlsx|max:51200', // 50MB maksimal
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
    }

    public function save()
    {
        $this->validate([
            'polis_id'=>'required',
            'masa_asuransi' => 'required'
        ]);

        \LogActivity::add('[web] Upload Kepesertaan');

        $pengajuan = new Pengajuan();
        $pengajuan->masa_asuransi = $this->masa_asuransi;
        $pengajuan->perhitungan_usia = $this->perhitungan_usia;
        $pengajuan->polis_id = $this->polis_id;
        $pengajuan->status = $this->is_draft ? 6 : 0; 
        $pengajuan->total_akseptasi = Kepesertaan::where(['polis_id'=>$this->polis_id,'is_temp'=>1,'user_id'=>\Auth::user()->id])->count();;
        $pengajuan->total_approve = 0;
        $pengajuan->total_reject = 0;
        $pengajuan->no_pengajuan =  date('dmy').str_pad((Pengajuan::count()+1),6, '0', STR_PAD_LEFT);
        $pengajuan->account_manager_id = \Auth::user()->id;
        $pengajuan->save();

        foreach(Kepesertaan::where(['polis_id'=>$this->polis_id,'is_temp'=>1,'user_id'=>\Auth::user()->id])->get() as $item){
            $item->pengajuan_id = $pengajuan->id;
            $item->is_temp = 0;
            $item->status_polis = 'Akseptasi';
            $item->save();
        }

        session()->flash('message-success',__('Pengajuan berhasil diupload, silahkan menunggu persetujuan'));

        return redirect()->route('pengajuan.index');
    }
}
