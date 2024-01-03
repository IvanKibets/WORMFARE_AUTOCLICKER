<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Income;

class PremiumReceivableController extends Controller
{
    public function data()
    {
        $data = Income::select('income.*')->where('reference_type','Premium Receivable')
                        ->where(function($table){
                            $table->where('status',1)->orWhere('status',3);
                        })->orderBy('income.id','DESC')
                        ->join('policys','policys.id','=','income.policy_id');
        if(isset($_GET['term']))$data->where(function($table){
                $table->where('reference_no','LIKE',"%{$_GET['term']}%")
                        ->orWhere('client','LIKE',"%{$_GET['term']}%")
                        ->orWhere('no_polis','LIKE',"%{$_GET['term']}%")
                        ->orWhere('nominal','LIKE',"%{$_GET['term']}%")
                        ;
            });
        
        $temp = [];
        foreach($data->offset(0)->limit(10)->get() as $k => $item){
            $temp[$k] = $item;
            $temp[$k]['nominal'] = $item->outstanding_balance ? format_idr($item->outstanding_balance) : format_idr($item->nominal);
        }
        return response()->json($temp, 200);
    }
}