<?php

namespace App\Http\Livewire\Pengajuan;

use App\Mail\EmailSpk;
use Livewire\Component;
use App\Models\Kepesertaan;
class SendEmail extends Component
{
    protected $listeners = ['set_id'];
    public $data,$email;
    public function render()
    {
        return view('livewire.pengajuan.send-email');
    }

    public function set_id(Kepesertaan $data)
    {
        $this->data = $data;
        $this->email = $data->email;
    }
    public function send()
    {
        $this->validate([
            'email' => 'required:email'
        ]);

        // Save data email
        $this->data->email = $this->email;
        $this->data->save();
        
        \Mail::to($this->email)->send(new EmailSpk('Asuransi Jiwa Reliance - Surat Pernyataan Kesehatan (SPK)',$this->data));

        $this->reset('email');
        $this->emit('modal','hide');
        $this->emit('reload-page');
        $this->emit('message-success','Email berhasil dikirim');
    }
}
