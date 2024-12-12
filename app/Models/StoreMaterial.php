<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreMaterial extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'material_id',
        'store_id',
        'quantity',
        'minimum_limit',
        'critical_limit'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'quantity' => 'integer',
        'minimum_limit' => 'integer',
        'critical_limit' => 'integer',
    ];

    /**
     * Get the material associated with the store material.
     */
    public function material()
    {
        return $this->belongsTo(Material::class);
    }

    /**
     * Get the store associated with the store material.
     */
    public function store()
    {
        return $this->belongsTo(Store::class);
    }

    /**
     * Get the movements associated with the store material.
     */
    public function movements()
    {
        return $this->hasMany(Movement::class);
    }
}