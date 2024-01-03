<?php

namespace App\Http\Livewire;

use Livewire\Component;

class Home extends Component
{
    public function render()
    {
        if(\Auth::user()->user_access_id ==1) 
            $this->redirect('log-activity');
        else
            $this->redirect('polis');// redirect to finance
        // if(\Auth::user()->user_access_id ==4) $this->redirect('bank-book');// redirect to treasury
        // if(\Auth::user()->user_access_id ==3) $this->redirect('accounting-journal'); // redirect to accounting
        // if(\Auth::user()->user_access_id ==6) $this->redirect('bank-book/payable'); // redirect payable
        // if(\Auth::user()->user_access_id ==5) $this->redirect('teknis-konven-underwriting'); // redirect payable
        // if(\Auth::user()->user_access_id ==7) $this->redirect('bank-book-teknik'); // redirect receivable
        
        return view('livewire.home');
    }
}
