<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserEventClick extends Model
{
    protected $fillable = [
        'event_id',
        'ip_address',
        'user_agent',
    ];
}