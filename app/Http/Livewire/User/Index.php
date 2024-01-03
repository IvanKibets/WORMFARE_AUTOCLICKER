<?php

namespace App\Http\Livewire\User;

use Livewire\Component;
use Livewire\WithPagination;
use App\Models\User;

class Index extends Component
{
    public $keyword;
    public $user_access_id;
    public $active_id;

    use WithPagination;

    protected $paginationTheme = 'bootstrap';

    public function render()
    {
        $data = User::orderBy('id','desc');

        if($this->keyword) $data = $data->where('name','LIKE', '%'.$this->keyword.'%')
                                        ->orWhere('email','LIKE', '%'.$this->keyword.'%')
                                        ->orWhere('telepon','LIKE', '%'.$this->keyword.'%');

        if($this->user_access_id) $data = $data->where('user_access_id',$this->user_access_id);

        return view('livewire.user.index')
                ->layout('layouts.app')
                ->with(['data'=>$data->paginate(100)]);
    }

    public function setActiveId($id)
    {
        $this->active_id = $id;
    }

    public function autologin($id)
    {
        $data = User::where('id', $id)->first();
        if($data){
            \LogActivity::add("Autologin {$data->name}");
            \Session::put('is_id', \Auth::user()->id);
            \Auth::loginUsingId($data->id);
            \Session::put('is_login_administrator', true);

            return redirect('/application-room-request')->with('message-success', 'Welcome, Login success.');
        }
    }

    public function delete($id)
    {
        $data = User::find($id);
        \LogActivity::add("Delete {$data->name}");
        User::find($id)->delete();
    }
}
