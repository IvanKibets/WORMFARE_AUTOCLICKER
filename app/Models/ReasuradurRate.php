<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Reasuradur;
use App\Models\ReasuradurRateRates;
use App\Models\ReasuradurRateUw;
use Illuminate\Database\Eloquent\SoftDeletes;

class ReasuradurRate extends Model
{
    use HasFactory;

    protected $table = 'reasuradur_rate';
    use SoftDeletes;
    public function reasuradur()
    {
        return $this->hasOne(Reasuradur::class,'id','reasuradur_id');
    }

    public function rate()
    {
        return $this->hasMany(ReasuradurRateRates::class,'reasuradur_rate_id','id');
    }

    public function uw_limit()
    {
        return $this->hasMany(ReasuradurRateUw::class,'reasuradur_rate_id','id');
    }
}
