<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AdditionalCategory extends Model
{
    use HasFactory;

    protected $table = 'additionals_categories';

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'total',
        'additional_id',
    ];

     /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'total' => 'float',
    ];

    public function additional(){
        return $this->belongsTo(Additional::class);
    }

    public function activities(){
        return $this->hasMany(AdditionalCategoryActivity::class);
    }
}
