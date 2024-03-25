<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;

class Obra extends Model
{
    use HasFactory;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'budget_id',
        'client_id',
        'image',
        'name',
        'address',
        'phone',
        'start_date',
        'end_date',
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

    public function incomes() {
        return $this->hasMany(Income::class);
    }

    public function outcomes() {
        return $this->hasMany(Outcome::class);
    }

    public function budget() {
        return $this->belongsTo(Budget::class);
    }

    public function documents() {
        return $this->hasMany(Document::class);
    }

    public function additionals() {
        return $this->hasMany(Additional::class);
    }

    public function dailyLogs() {
        return $this->hasMany(ObraDailyLog::class);
    }
}
