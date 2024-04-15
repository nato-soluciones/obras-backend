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
        'state',
        'value',
        'inter_month_variation',
    ];

    protected $casts = [
        'value' => 'float',
    ];
}
