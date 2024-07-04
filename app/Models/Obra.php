<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Str;

class Obra extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'budget_id',
        'client_id',
        'covered_area',
        'semi_covered_area',
        'currency',
        'total',
        'total_cost',
        'image',
        'name',
        'address',
        'phone',
        'start_date',
        'end_date',
        'initial_cac_index',
        'status',
    ];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($obra) {
            do {
                $randomCode = Str::upper(Str::random(9));
            } while (self::where('code', $randomCode)->exists());

            $obra->code = $randomCode;
        });
    }

    public function client()
    {
        return $this->belongsTo(Client::class);
    }

    public function incomes()
    {
        return $this->hasMany(Income::class);
    }

    public function outcomes()
    {
        return $this->hasMany(Outcome::class);
    }

    public function budget()
    {
        return $this->belongsTo(Budget::class);
    }

    public function documents()
    {
        return $this->hasMany(Document::class);
    }

    public function additionals()
    {
        return $this->hasMany(Additional::class);
    }

    public function dailyLogs()
    {
        return $this->hasMany(ObraDailyLog::class);
    }
}
