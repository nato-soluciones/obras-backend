<?php

namespace App\Models\Obra;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObraPlanChargeDetailPayment extends Model
{
    use HasFactory;

    protected $fillable = [
        'obra_plan_charge_detail_id',
        'date',
        'amount',
        'description'
    ];
}
