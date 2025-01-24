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

    protected $hidden = ['deleted_at'];

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

     public function users()
     {
        return $this->belongsToMany(User::class, 'user_store');
     }

     public function userStores()
     {
         return $this->hasMany(UserStore::class);
     }

     public function movements()
     {
         return $this->where(function($query) {
             $query->where('from_store_id', $this->id)
                   ->orWhere('to_store_id', $this->id);
         });
     }

     public function lastMovement()
     {
         return $this->hasOne(StoreMovement::class, 'from_store_id')
             ->orWhere('to_store_id', $this->id)
             ->latest();
     }
}
