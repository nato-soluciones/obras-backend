<?php

namespace App\Models\Company;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CompanyCostCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name'
    ];
}
