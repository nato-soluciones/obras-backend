<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Movement extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'created_by_id',
        'from_store_id',
        'to_store_id',
        'material_id',
        'quantity',
        'status'
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
        return $this->belongsTo(Material::class, 'material_id');
    }
}
