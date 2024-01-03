<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Polis;
use App\Models\Kepesertaan;
use App\Models\Klaim;

class RecoveryClaim extends Model
{
    use HasFactory;

    protected $table = 'recovery_claim';

    public function polis()
    {
        return $this->hasOne(Polis::class,'id','polis_id');
    }

    public function kepesertaan()
    {
        return $this->hasOne(Kepesertaan::class,'id','kepesertaan_id');
    }

    public function klaim()
    {
        return $this->hasOne(Klaim::class,'id','klaim_id');
    }

    public function reas()
    {
        return $this->hasOne(Reas::class,'id','reas_id');
    }
}
