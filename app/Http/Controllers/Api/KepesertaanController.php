<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Kepesertaan;
use Illuminate\Http\Request;

class KepesertaanController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {   
        $keyword = isset($_GET['search']) ? $_GET['search'] : '';
        $polis_id = $_GET['polis_id'];
        $klaim_id = isset($_GET['klaim_id']) ? $_GET['klaim_id'] : '';

        $data = Kepesertaan::where('polis_id',$polis_id);

        if($keyword) $data->where(function($table) use($keyword){
                            $table->where('no_peserta','LIKE',"%{$keyword}%")->orWhere('nama','LIKE',"%{$keyword}%");
                        });

        if($klaim_id==1) $data->whereNull('klaim_id');
        
        if(\Auth::user()->user_access_id==2) $data->where('user_id',\Auth::user()->id);
        
        $items = [];
        foreach($data->paginate(10) as $k => $item){
            $items[$k]['id'] = $item->id;
            $items[$k]['name'] = $item->nama;
            $items[$k]['text'] = $item->no_peserta .' / '. $item->nama;
        }

        return response()->json(['message'=>'success','items'=>$items,'total_count'=>count($items)], 200);
    }
}
