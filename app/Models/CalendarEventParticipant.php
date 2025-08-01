<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class CalendarEventParticipant extends Model
{
    use HasFactory;

    protected $fillable = [
        'calendar_event_id',
        'user_id',
        'name',
        'email',
        'phone',
        'status',
    ];

    public function calendarEvent(): BelongsTo
    {
        return $this->belongsTo(CalendarEvent::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopeExternal($query)
    {
        return $query->whereNull('user_id');
    }

    public function scopeInternal($query)
    {
        return $query->whereNotNull('user_id');
    }

    public function isExternal(): bool
    {
        return is_null($this->user_id);
    }

    public function isInternal(): bool
    {
        return !is_null($this->user_id);
    }
}
