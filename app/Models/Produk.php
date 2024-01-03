<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\ExtraMortalita;

class Produk extends Model
{
    use HasFactory;

    protected $table = 'produk';
    
    public function em()
    {
        return $this->belongsTo(ExtraMortalita::class,'extra_mortalita_id','id');
    }
}
