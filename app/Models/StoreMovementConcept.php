<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StoreMovementConcept extends Model
{
    use HasFactory, SoftDeletes;

    public $timestamps = false;

    protected $fillable = ['name', 'description'];

    protected $hidden = ['deleted_at'];

    public function type()
    {
        return $this->belongsTo(StoreMovementType::class, 'movement_type_id');
    }
}
