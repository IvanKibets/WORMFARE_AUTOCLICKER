<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SyariahUnderwriting extends Model
{
    use HasFactory;

    protected $table = 'syariah_underwritings';

    protected $connection = 'finance';
}
