<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Employee;

class ApplicationRoomRequest extends Model
{
    use HasFactory;

    protected $table = 'application_room_request';

}
