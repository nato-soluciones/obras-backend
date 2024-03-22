<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Tool extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'brand',
        'value',
        'category_id',
        'purchase_date',
        'status',
        'last_maintenance',
        'description',
        'image',
    ];

    public function category()
    {
        return $this->belongsTo(ToolCategory::class);
    }

    public function locations()
    {
        return $this->hasMany(ToolLocation::class);
    }
}
