<?php

namespace App\Models\Obra;

use App\Models\Obra;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObraPlanCharge extends Model
{
    use HasFactory;

    protected $fillable = [
        'obra_id',
        'financed_amount',
        'installment_count',
        'installment_frequency',
        'installment_first_due_date',
        'created_by_id'
    ];


    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }


    public function details()
    {
        return $this->hasMany(ObraPlanChargeDetail::class);
    }
}
