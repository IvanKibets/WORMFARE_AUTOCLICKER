<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Klaim;

class KlaimController extends Controller
{

    public function printPersetujuan(Klaim $id)
    {
        \LogActivity::add("Print Persetujuan {$id->id}");
        
        $keputusa_arr = [''=>'-',1=>'Terima',2=>'Tolak',3=>'Tunda',4=>'Investigasi',5=>'Liable',6=>'STNC'];

        if($id->no_apv=="" || $id->no_mak==""){
            $id->no_apv = $id->id. '/KLM-APV/AJRIUS/'.numberToRomawi(date('m')).'/'. date('Y');
            $id->no_mak = $id->id. '/KLM-MAK/AJRIUS/'.numberToRomawi(date('m')).'/'. date('Y');
            $id->save();
        }

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadView('livewire.klaim.print-persetujuan',['data'=>$id,'keputusa_arr'=>$keputusa_arr])->setPaper([0, 0, 210, 297], 'landscape');;

        return $pdf->stream();
    }

    public function printTolak(Klaim $id)
    {
        \LogActivity::add("Print Persetujuan {$id->id}");

        if($id->no_surat_tolak==""){
            $id->no_surat_tolak = $id->id. '/KLM-APV/AJRIUS/'.numberToRomawi(date('m')).'/'. date('Y');
            $id->save();
        }

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadView('livewire.klaim.print-tolak',['data'=>$id])->setPaper([0, 0, 210, 297], 'landscape');;

        return $pdf->stream();
    }

    public function printDiterima(Klaim $id)
    {
        \LogActivity::add("Print Diterima {$id->id}");

        if($id->no_surat_diterima==""){
            $id->no_surat_diterima = $id->id. '/KEP-KLM/AJRIUS/'.numberToRomawi(date('m')).'/'. date('Y');
            $id->save();
        }

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadView('livewire.klaim.print-diterima',['data'=>$id])->setPaper([0, 0, 210, 297], 'landscape');;

        return $pdf->stream();
    }

    public function printMemo(Klaim $id)
    {
        \LogActivity::add("Print Memo Pembayaran {$id->id}");

        if($id->no_memo==""){
            $id->no_memo = $id->id. '/KEP-KLM-DN/AJRIUS/'.numberToRomawi(date('m')).'/'. date('Y');
            $id->save();
        }

        if($id->jatuh_tempo){
            $id->jatuh_tempo = date('Y-m-d',strtotime(" +{$id->polis->pembayaran_klaim} days", strtotime($id->created_at)));
            $id->save();
        }

        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadView('livewire.klaim.print-memo',['data'=>$id])->setPaper([0, 0, 210, 297], 'landscape');;

        return $pdf->stream();
    }
}
