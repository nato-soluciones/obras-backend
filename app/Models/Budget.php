<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Budget extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'date',
        'client_id',
        'obra_name',
        'estimated_time',
        'covered_area',
        'semi_covered_area',
        'status',
        'fields',
        'guilds_administrative',
        'guilds',
        'final_budget',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'fields' => 'object',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($budget) {
            do {
                $randomNumber = mt_rand(100000000, 999999999);
            } while (self::where('code', $randomNumber)->exists());

            $budget->code = $randomNumber;
        });
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }
}
