<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class CalendarEventCategory extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'color',
        'is_system',
        'user_id',
    ];

    protected $casts = [
        'is_system' => 'boolean',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function calendarEvents(): HasMany
    {
        return $this->hasMany(CalendarEvent::class);
    }

    public function scopeSystem($query)
    {
        return $query->where('is_system', true);
    }

    public function scopePersonal($query, $userId)
    {
        return $query->where('is_system', false)->where('user_id', $userId);
    }
}
