<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\KlaimTolakReasonItem;

class KlaimTolakReason extends Model
{
    protected $table = 'klaim_tolak_reason';

    use HasFactory;

    public function item()
    {
        return $this->hasMany(KlaimTolakReasonItem::class,'klaim_tolak_reason','id');
    }
}
