<?php

function status_income($status) {
    switch($status){
        case 1:
            return "<label class=\"badge text-warning\">Unpaid</label>";
        break;
        case 2:
            return "<label class=\"badge text-success\">Paid</label>";
        break;
        case 3:
            return "<label class=\"badge text-info\">Outstanding</label>";
        break;
        case 4:
            return "<label class=\"badge text-danger\">Premi tidak tertagih</label>";
        break;
        case 5:
            return "<label class=\"badge text-warning\">Draft</label>";
        break;
    }
}

function generate_no_voucher_income()
{
    $count = \App\Models\Income::whereMonth('created_at',date('m'))->whereYear('created_at',date('Y'))->count()+1;
    
    return date('y').date('m').str_pad($count,5, '0', STR_PAD_LEFT);
}