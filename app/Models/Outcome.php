<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Outcome extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'type',
        'provider_cuit',
        'provider_name',
        'provider_iva',
        'provider_condition',
        'date',
        'due_date',
        'document_type',
        'order',
        'gross_total',
        'net_total',
        'total',
        'payment_date',
        'payment_method',
        'currency',
        'exchange_rate',
        'bank',
        'bank_name',
        'bank_cbu',
        'bank_alias',
        'bank_branch',
        'bank_account',
        'check_number',
        'comments',
        'contractor_id',
        'obra_id'
    ];

    protected $casts = [
        'image_paths' => 'array',
    ];

    public function contractor()
    {
        return $this->belongsTo(Contractor::class);
    }

    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }
}
