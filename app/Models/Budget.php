<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Budget extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'project_name',
        'estimated_time',
        'expiration_date',
        'covered_area',
        'semi_covered_area',
        'status',
        'currency',
        'comments',
        'fields',
        'total',
        'total_cost',
        'user_id',
        'client_id',
        'created_by_id',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fields' => 'object',
        'total' => 'float',
        'total_cost' => 'float',
    ];

    /**
     * The "booting" method of the model.
     *
     * @return void
     */
    // protected static function boot()
    // {
    //     parent::boot();

    //     static::creating(function ($budget) {
    //         do {
    //             $randomNumber = mt_rand(100000000, 999999999);
    //         } while (self::where('code', $randomNumber)->exists());

    //         $budget->code = $randomNumber;
    //     });
    // }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }
    public function categories()
    {
        return $this->hasMany(BudgetCategory::class);
    }
}
