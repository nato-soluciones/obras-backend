<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Store extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'address',
        'description',
    ];

     public function materialsStore()
     {
         return $this->hasMany(StoreMaterial::class, 'store_id');
     }
 
     public function materials()
     {
         return $this->hasManyThrough(
             Material::class,
             StoreMaterial::class, 
             'store_id', 
             'id', 
             'id', 
             'material_id'
         );
     }
}
