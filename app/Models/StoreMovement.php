<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class StoreMovement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'created_by_id',
        'from_store_id',
        'to_store_id',
        'material_id',
        'quantity',
        'store_movement_type_id',
        'store_movement_status_id',
        'store_movement_concept_id',

    ];

    protected $casts = [
        'quantity' => 'float',
    ];

    /**
     * Get the user that created the movement.
     */
    public function createdBy(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    /**
     * Get the store that the movement is from.
     */
    public function fromStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'from_store_id');
    }

    /**
     * Get the store that the movement is to.
     */
    public function toStore(): BelongsTo
    {
        return $this->belongsTo(Store::class, 'to_store_id');
    }

    /**
     * Get the material associated with the movement.
     */
    public function material(): BelongsTo
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * Get the status associated with the movement.
     */
    public function status(): BelongsTo
    {
        return $this->belongsTo(StoreMovementStatus::class, 'store_movement_status_id');
    }

    /**
     * Get the type associated with the movement.
     */
    public function type(): BelongsTo
    {
        return $this->belongsTo(StoreMovementType::class, 'store_movement_type_id');
    }

    /**
     * Get the type associated with the movement.
     */
    public function concept(): BelongsTo
    {
        return $this->belongsTo(StoreMovementConcept::class, 'store_movement_concept_id');
    }

    public function movementMaterials(): HasMany
    {
        return $this->hasMany(StoreMovementMaterial::class);
    }
}
