<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Cac extends Model
{
    use HasFactory;

    protected $table = 'cac';

    protected $fillable = [
        'period',
        'general',
        'materials',
        'labour',
    ];

    protected $casts = [
        'general' => 'float',
        'materials' => 'float',
        'labour' => 'float',
    ];
}
