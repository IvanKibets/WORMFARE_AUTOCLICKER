<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Expenses;

class ClaimController extends Controller
{
    public function payable()
    {
        $data = Expenses::select('expenses.*')
                            ->where('status',4)
                            ->with(['pesertas'])
                            ->orderBy('expenses.id','desc')->where('expenses.reference_type','Claim')->groupBy('expenses.id')
                            ->leftJoin('expense_pesertas','expense_pesertas.expense_id','=','expenses.id')
                            ->leftJoin('policys','policys.id','=','expenses.policy_id');
        if(isset($_GET['term'])) $data = $data->where(function($table){
                                    $table->where('expenses.description','LIKE', "%{$_GET['term']}%")
                                        ->orWhere('expenses.no_voucher','LIKE',"%{$_GET['term']}%")
                                        ->orWhere('expenses.reference_no','LIKE',"%{$_GET['term']}%")
                                        ->orWhere('expenses.payment_amount','LIKE',"%{$_GET['term']}%")
                                        ->orWhere('expense_pesertas.no_peserta','LIKE',"%{$_GET['term']}%")
                                        ->orWhere('expense_pesertas.nama_peserta','LIKE',"%{$_GET['term']}%")
                                        ->orWhere('policys.no_polis','LIKE',"%{$_GET['term']}%")
                                        ->orWhere('policys.pemegang_polis','LIKE',"%{$_GET['term']}%")
                                        ;
                                    });
        
        $temp = [];
        foreach($data->offset(0)->limit(10)->get() as $k => $item){
            $temp[$k] = $item;
            $temp[$k]['nominal'] = format_idr($item->payment_amount);
        }
        return response()->json($temp, 200);
    }
}