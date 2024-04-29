<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Ipc extends Model
{
    use HasFactory;

    protected $table = 'ipc';

    protected $fillable = [
        'period',
        'value',
    ];

    protected $casts = [
        'value' => 'float',
    ];
}
