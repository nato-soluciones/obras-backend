<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ContractorIndustry extends Model
{
    use HasFactory;

    protected $primaryKey = 'code';
    protected $keyType = 'string';
}
