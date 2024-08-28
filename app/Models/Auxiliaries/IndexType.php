<?php

namespace App\Models\Auxiliaries;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class IndexType extends Model
{
    use HasFactory;

    protected $fillable = [
        'code',
        'name',
    ];
}
