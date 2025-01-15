<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Reminder extends Model
{
    protected $fillable = [
        'text',
        'datetime',
        'user_id',
        'created_by',
    ];
}
