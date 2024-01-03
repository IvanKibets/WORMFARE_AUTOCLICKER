<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Polis;
use App\Models\Kepesertaan;
use App\Models\Provinsi;
use App\Models\Kabupaten;
use App\Models\User;
use App\Models\KlaimFile;
class Klaim extends Model
{
    use HasFactory;

    protected $table = 'klaim';

    public function polis()
    {
        return $this->hasOne(Polis::class,'id','polis_id');
    }

    public function kepesertaan()
    {
        return $this->hasOne(Kepesertaan::class,'id','kepesertaan_id');
    }

    public function provinsi()
    {
        return $this->hasOne(Provinsi::class,'id','provinsi_id');
    }

    public function kabupaten()
    {
        return $this->hasOne(Kabupaten::class,'id','kabupaten_id');
    }

    public function user()
    {
        return $this->hasOne(User::class,'id','user_id');
    }

    public function files()
    {
        return $this->hasMany(KlaimFile::class,'klaim_id','id');
    }
}
