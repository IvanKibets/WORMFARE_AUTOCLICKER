<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Klaim;

class IndexController extends Controller
{
    public function backtoadmin()
    {
        if(\Session::get('is_login_administrator'))
        {
            \Auth::loginUsingId(\Session::get('is_id'));
            
            \LogActivity::add('Back to Admin');

            return redirect('/')->with('message-success', 'Welcome Back Administrator');
        }
    }

    public function uploadKonvenUw(Request $r)
    {
        // $this->validate([
        //     'file'=>'required|mimes:xls,xlsx|max:51200' // 50MB maksimal
        // ]);
        
        $path = $r->file('file')->getRealPath();
       
        $reader = new \PhpOffice\PhpSpreadsheet\Reader\Xlsx();
        $data = $reader->load($path);
        $sheetData = $data->getActiveSheet();
        //$sheetData->setPreCalculateFormulas(false);
        $sheetData = $sheetData->toArray(null, true, true, true);
        dd($sheetData);


    }
}