<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\BankBook;

class VoucherController extends Controller
{
    public function data()
    {
        $data = BankBook::select('bank_books.*')
                        ->where('status',0)
                        ->where('type','R');
        if(isset($_GET['term']))$data->where(function($table){
                $table->where('no_voucher','LIKE',"%{$_GET['term']}%")
                        ->orWhere('amount','LIKE',"%{$_GET['term']}%")
                        ->orWhere('note','LIKE',"%{$_GET['term']}%");
            });

        $arr = [];
        foreach($data->offset(0)->limit(10)->get() as $k => $item){
            $arr[$k] = $item;
            $arr[$k]['amount'] = format_idr($item->amount);
            $arr[$k]['from_bank_'] = isset($item->from_bank->no_rekening) ? $item->from_bank->no_rekening .'- '.$item->from_bank->bank.' an '. $item->from_bank->owner : '-';
            $arr[$k]['to_bank_'] = isset($item->to_bank->no_rekening) ? $item->to_bank->no_rekening .'- '.$item->to_bank->bank.' an '. $item->to_bank->owner : '-';
        }
        return response()->json($arr, 200);
    }
}