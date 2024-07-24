<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrentAccountMovementType extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'type',
        'active',
    ];

}
