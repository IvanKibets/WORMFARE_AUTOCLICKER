<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class EmailSpk extends Mailable
{
    use Queueable, SerializesModels;
    public $subject,$data;
    /**
     * Create a new message instance.
     *
     * @return void
     */
    public function __construct($subject,$data)
    {
        $this->subject = $subject;
        $this->data = $data;
    }

    /**
     * Build the message.
     *
     * @return $this
     */
    public function build()
    {
        return $this->subject($this->subject)
                    ->from('no-reply@stalavista.co.id')
                    ->view('email.spk')
                    ->with(['subject'=>$this->subject,'data'=>$this->data])
                    ->attach($this->data->file_spk);
    }
}
