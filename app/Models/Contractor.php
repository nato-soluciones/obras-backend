<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Contractor extends Model
{
    use HasFactory;

    protected $fillable = [
        'type',
        'last_name',
        'first_name',
        'business_name',
        'trade_name',
        'person_type',
        'industry',
        'address',
        'zip',
        'city',
        'web',
        'email',
        'referral',
        'position',
        'phone',
        'cuit',
        'condition',
        'alicuota',
        'bank',
        'bank_name',
        'bank_type',
        'bank_account',
        'bank_cbu',
        'bank_alias',
        'comments',
    ];

    public function industries()
    {
        return $this->belongsTo(ContractorIndustry::class, 'industry', 'code');
    }
}
