<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Polis;
use App\Models\Kepesertaan;
use App\Models\Reas;

class Pengajuan extends Model
{
    use HasFactory;

    protected $guarded = [];  

    protected $table = 'pengajuan';

    public function polis()
    {
        return $this->belongsTo(Polis::class,'polis_id','id');
    }

    public function account_manager()
    {
        return $this->belongsTo(User::class,'account_manager_id','id');
    }

    public function kepesertaan()
    {
        return $this->hasMany(Kepesertaan::class,'pengajuan_id','id');
    }

    public function kepesertaan_first()
    {
        return $this->hasOne(Kepesertaan::class,'pengajuan_id','id');
    }

    public function akseptasi()
    {
        return $this->hasMany(Kepesertaan::class,'pengajuan_id','id')->where('status_akseptasi',0);
    }

    public function diterima()
    {
        return $this->hasMany(Kepesertaan::class,'pengajuan_id','id')->where('status_akseptasi',1);
    }

    public function ditolak()
    {
        return $this->hasMany(Kepesertaan::class,'pengajuan_id','id')->where('status_akseptasi',2);
    }

    public function reas()
    {
        return $this->belongsTo(Reas::class,'reas_id','id');
    }
}
