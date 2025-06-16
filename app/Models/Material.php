<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Material extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'code',
        'dimensions',
        'quantity_per_package',
        'color',
        'description',
        'measurement_unit_id',
        'category_id',
    ];

    // protected $hidden = [
    //     'created_at',
    //     'updated_at',
    //     'measurement_unit_id'
    // ];

    public function storeMaterials()
    {
        return $this->hasMany(StoreMaterial::class);
    }

    public function measurementUnit()
    {
        return $this->belongsTo(MeasurementUnit::class);
    }
    
    public function movements()
    {
        return $this->hasMany(StoreMovement::class);
    }

    public function category()
    {
        return $this->belongsTo(MaterialCategory::class);
    }
}
