<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Produk;
use App\Models\Provinsi;
use App\Models\UnderwritingLimit;

class Polis extends Model
{
    use HasFactory;
    protected $guarded = [];
    public $timestamps = true;
    protected $table = 'polis';

    public function produk()
    {
        return $this->belongsTo(Produk::class);
    }

    public function provinsi()
    {
        return $this->belongsTo(Provinsi::class);
    }

    public function pesertas()
    {
        return $this->hasMany(Kepesertaan::class,'polis_id','id');
    }
    
    public function rate_()
    {
        return $this->hasMany(Rate::class,'polis_id','id');
    }

    public function uw_limit_()
    {
        return $this->hasMany(UnderwritingLimit::class,'polis_id','id');
    }
}
