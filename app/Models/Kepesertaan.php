<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Polis;
use App\Models\Rate;
use App\Models\Pengajuan;

class Kepesertaan extends Model
{
    use HasFactory;

    protected $table = 'kepesertaan';
    protected $guarded = [];  

    public function parent()
    {
        return $this->belongsTo(Kepesertaan::class,'parent_id','id');
    }

    public function polis()
    {
        return $this->hasOne(Polis::class,'id','polis_id');
    }

    public function rate_()
    {
        return $this->hasMany(Rate::class,'polis_id','polis_id');
    }

    public function pengajuan()
    {
        return $this->hasOne(Pengajuan::class,'id','pengajuan_id');
    }

    public function double_peserta()
    {
        return $this->hasMany(Kepesertaan::class,'nama','nama')->where('status_polis','Inforce')->orWhere('status_polis','Akseptasi');
    }

    public function reas()
    {
        return $this->hasOne(Reas::class,'id','reas_id');
    }
}
