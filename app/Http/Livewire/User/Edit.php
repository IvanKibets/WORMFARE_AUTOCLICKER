<?php

namespace App\Http\Livewire\User;

use Livewire\Component;
use App\Models\UserAccess;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class Edit extends Component
{
    public $data;
    public $name;
    public $email;
    public $password;
    public $telepon;
    public $address;
    public $user_access_id,$polis_id;
    public $message,$is_supervisor;

    protected $rules = [
        'name' => 'required|string',
        'email' => 'required',
        //'password' => 'required|string',
        'telepon' => 'required',
        'user_access_id' => 'required',
    ];

    public function render()
    {
        return view('livewire.user.edit')
                        ->with([
                            'access' => UserAccess::all(),
                            'data' => $this->data
                        ]);
    }

    public function mount($id)
    {
        $this->data = User::find($id);
        $this->name = $this->data->name;
        $this->email = $this->data->email;
        $this->telepon = $this->data->telepon;
        $this->address = $this->data->address;
        $this->user_access_id = $this->data->user_access_id;
        $this->is_supervisor = $this->data->is_supervisor;
        $this->polis_id = $this->data->polis_id;
    }

    public function save(){
        $this->validate();
        
        $this->data->name = $this->name;
        $this->data->email = $this->email;
        if($this->password!="") $this->data->password = Hash::make($this->password);
        $this->data->telepon = $this->telepon;
        $this->data->address = $this->address;
        $this->data->user_access_id = $this->user_access_id;
        $this->data->is_supervisor = $this->is_supervisor;
        $this->data->polis_id = $this->polis_id;
        $this->data->save();

        session()->flash('message-success',__('Data saved successfully'));
        
        return redirect()->to('users');
    }
}
