<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class StoreMovementMaterial extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'store_movement_id',
        'material_id',
        'quantity'
    ];

    protected $casts = [
        'quantity' => 'float',
    ];

    public function storeMovement(): BelongsTo
    {
        return $this->belongsTo(StoreMovement::class);
    }

    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }
} 