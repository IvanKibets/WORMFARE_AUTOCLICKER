<?php

namespace App\Http\Livewire;

use Livewire\Component;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class Login extends Component
{
    public $email;
    public $password,$token;
    public $message;

    protected $rules = [
        'email' => 'required',
        'password' => 'required',
    ];

    public function render()
    {
        return view('livewire.login')
                ->layout('layouts.auth');
    }

    public function login()
    {
        $this->validate();

        if(env('USE_CAPTCHA')==1){
            $response = Http::post('https://www.google.com/recaptcha/api/siteverify?secret='.env('CAPTCHA_SITE_SECRET').'&response='. $this->token);
            $response = $response->json();
            
            if (!$response['success']) {
                $this->message = 'Google thinks you are a bot, please refresh and try again';
            }else{
                $credentials = ['email'=>$this->email,'password'=>$this->password];
    
                if (Auth::attempt($credentials)) {
                    \LogActivity::add('Login');
                    return redirect('/booking-room');
                }
                else $this->message = __('Email / Password incorrect please try again');
            }
        }else{
            $credentials = ['email'=>$this->email,'password'=>$this->password];
    
            if (Auth::attempt($credentials)) {
                \LogActivity::add('Login');
                return redirect('/booking-room');
            }
            else $this->message = __('Email / Password incorrect please try again');
        }
       
    }
}
