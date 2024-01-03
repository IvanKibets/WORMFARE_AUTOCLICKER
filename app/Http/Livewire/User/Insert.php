<?php

namespace App\Http\Livewire\User;

use Livewire\Component;
use App\Models\User;
use App\Models\UserAccess;
use Illuminate\Support\Facades\Hash;

class Insert extends Component
{
    public $name;
    public $email;
    public $password;
    public $telepon;
    public $address;
    public $user_access_id,$polis_id;
    public $message,$is_supervisor;

    protected $rules = [
        'name' => 'required|string',
        'email' => 'required|unique:users',
        'password' => 'required|string',
        'telepon' => 'required',
        'user_access_id' => 'required',
    ];

    public function render()
    {
        return view('livewire.user.insert')->with(
            ['access'=>UserAccess::all()]
        );
    }

    public function save(){
        $this->validate();
        
        $data = new User();
        $data->name = $this->name;
        $data->email = $this->email;
        $data->password = Hash::make($this->password);
        $data->telepon = $this->telepon;
        $data->address = $this->address;
        $data->user_access_id = $this->user_access_id;
        $data->is_supervisor = $this->is_supervisor;
        $data->polis_id = $this->polis_id;
        $data->save();

        session()->flash('message-success',__('Data saved successfully'));

        return redirect()->to('users');
    }
}
