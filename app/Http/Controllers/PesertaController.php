<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Kepesertaan;

class PesertaController extends Controller
{
    public function printEm(Kepesertaan $id)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadView('livewire.polis.print-em',['data'=>$id]);
        
        return $pdf->stream();
    }

    public function printEk(Kepesertaan $id)
    {
        $pdf = \App::make('dompdf.wrapper');
        $pdf->loadView('livewire.polis.print-ek',['data'=>$id]);
        
        return $pdf->stream();
    }
}
