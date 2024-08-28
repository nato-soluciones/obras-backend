<?php

namespace App\Models\Obra;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObraPlanChargeDetail extends Model
{
    use HasFactory;

    protected $fillable = [
        'obra_plan_charge_id',
        'type',
        'installment_number',
        'concept',
        'description',
        'due_date',
        'index_type',
        'index_period',
        'installment_amount',
        'adjustment_amount',
        'total_amount',
        'full_payment_date',
        'status'
    ];

    public function planCharge()
    {
        return $this->belongsTo(ObraPlanCharge::class, 'obra_plan_charge_id');
    }  

    public function payments()
    {
        return $this->hasMany(ObraPlanChargeDetailPayment::class);
    }
}
