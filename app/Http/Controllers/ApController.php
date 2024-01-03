<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Models\Expenses;

class ApController extends Controller
{
    public function others()
    {
        $data = Expenses::orderBy('id','desc')->where(['is_others'=>1,'status'=>4]);
        
        if(isset($_GET['term'])) $data = $data->where(function($table){
                                        $table->where('description','LIKE', "%{$_GET['term']}%")
                                        ->orWhere('no_voucher','LIKE',"%{$_GET['term']}%")
                                        ->orWhere('reference_no','LIKE',"%{$_GET['term']}%")
                                        ->orWhere('recipient','LIKE',"%{$_GET['term']}%")
                                        ;
                                    });

        $temp = [];
        foreach($data->offset(0)->limit(10)->get() as $k => $item){
            $temp[$k] = $item;
            $temp[$k]['nominal'] = format_idr($item->payment_amount);
        }
        return response()->json($temp, 200);
    }

    public function commision()
    {
        $data = Expenses::orderBy('id','desc')->where('reference_type','Komisi');
        
        if(isset($_GET['term'])) $data = $data->where(function($table){
                                        $table->where('description','LIKE', "%{$_GET['term']}%")
                                        ->orWhere('no_voucher','LIKE',"%{$_GET['term']}%")
                                        ->orWhere('reference_no','LIKE',"%{$_GET['term']}%")
                                        ->orWhere('recipient','LIKE',"%{$_GET['term']}%")
                                        ;
                                    });

        $temp = [];
        foreach($data->offset(0)->limit(10)->get() as $k => $item){
            $temp[$k] = $item;
            $temp[$k]['nominal'] = format_idr($item->payment_amount);
        }
        return response()->json($temp, 200);
    }

    public function cancelation()
    {
        $data = Expenses::orderBy('id','desc')->where(['reference_type'=>'Cancelation','status'=>1]);
        
        if(isset($_GET['term'])) $data = $data->where(function($table){
                                        $table->where('description','LIKE', "%{$_GET['term']}%")
                                        ->orWhere('no_voucher','LIKE',"%{$_GET['term']}%")
                                        ->orWhere('reference_no','LIKE',"%{$_GET['term']}%")
                                        ->orWhere('recipient','LIKE',"%{$_GET['term']}%")
                                        ;
                                    });

        $temp = [];
        foreach($data->offset(0)->limit(10)->get() as $k => $item){
            $temp[$k] = $item;
            $temp[$k]['nominal'] = format_idr($item->payment_amount);
        }
        return response()->json($temp, 200);
    }

    public function refund()
    {
        $data = Expenses::orderBy('id','desc')->where(['reference_type'=>'Refund','status'=>1]);
        
        if(isset($_GET['term'])) $data = $data->where(function($table){
                                        $table->where('description','LIKE', "%{$_GET['term']}%")
                                        ->orWhere('no_voucher','LIKE',"%{$_GET['term']}%")
                                        ->orWhere('reference_no','LIKE',"%{$_GET['term']}%")
                                        ->orWhere('recipient','LIKE',"%{$_GET['term']}%")
                                        ;
                                    });
        $temp = [];
        foreach($data->offset(0)->limit(10)->get() as $k => $item){
            $temp[$k] = $item;
            $temp[$k]['nominal'] = format_idr($item->payment_amount);
        }
        return response()->json($temp, 200);
    }

    public function handling_fee()
    {
        $data = Expenses::orderBy('id','desc')->where(['reference_type'=>'Handling Fee','status'=>1]);
        
        if(isset($_GET['term'])) $data = $data->where(function($table){
                                        $table->where('description','LIKE', "%{$_GET['term']}%")
                                        ->orWhere('no_voucher','LIKE',"%{$_GET['term']}%")
                                        ->orWhere('reference_no','LIKE',"%{$_GET['term']}%")
                                        ->orWhere('recipient','LIKE',"%{$_GET['term']}%")
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