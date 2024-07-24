<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CurrentAccountMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'current_account_id',
        'movement_type_id',
        'date',
        'description',
        'amount',
        'reference_entity',
        'reference_id',
        'observation',
        'created_by',
    ];

    public function currentAccount()
    {
        return $this->belongsTo(CurrentAccount::class);
    }

    public function movementType()
    {
        return $this->belongsTo(CurrentAccountMovementType::class, 'movement_type_id', 'id');
    }
}
