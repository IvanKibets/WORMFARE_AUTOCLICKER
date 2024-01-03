<?php

namespace App\Models\Finance;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Polis extends Model
{
    use HasFactory;

    protected $table = 'policys';

    protected $connection = 'finance';
}
