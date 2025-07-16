<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Carbon\Carbon;

class CalendarEvent extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'user_id',
        'title',
        'description',
        'start_datetime',
        'end_datetime',
        'duration',
        'location',
        'status',
        'visibility',
        'notes',
        'calendar_event_category_id',
        'source',
        'meeting_link',
    ];

    protected $casts = [
        'start_datetime' => 'datetime',
        'end_datetime' => 'datetime',
    ];

    protected static function boot()
    {
        parent::boot();
        
        static::saving(function ($event) {
            if ($event->start_datetime && $event->end_datetime) {
                $event->duration = $event->start_datetime->diffInMinutes($event->end_datetime);
            }
        });
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(CalendarEventCategory::class, 'calendar_event_category_id');
    }

    public function participants(): HasMany
    {
        return $this->hasMany(CalendarEventParticipant::class);
    }

    public function scopeForUser($query, $userId)
    {
        return $query->where(function ($q) use ($userId) {
            $q->where('user_id', $userId) // Eventos donde es organizador
              ->orWhereHas('participants', function ($participants) use ($userId) {
                  $participants->where('user_id', $userId); // Eventos donde es participante
              })
              ->orWhere('visibility', 'public'); // Eventos públicos
        });
    }

    public function scopeBetweenDates($query, $startDate, $endDate)
    {
        return $query->where(function ($q) use ($startDate, $endDate) {
            $q->whereBetween('start_datetime', [$startDate, $endDate])
              ->orWhereBetween('end_datetime', [$startDate, $endDate])
              ->orWhere(function ($overlap) use ($startDate, $endDate) {
                  $overlap->where('start_datetime', '<=', $startDate)
                          ->where('end_datetime', '>=', $endDate);
              });
        });
    }

    public function scopeStatus($query, $status)
    {
        return $query->where('status', $status);
    }

    public function scopePublic($query)
    {
        return $query->where('visibility', 'public');
    }

    public function scopePrivate($query)
    {
        return $query->where('visibility', 'private');
    }

    public function hasConflictWith($startDateTime, $endDateTime, $excludeEventId = null)
    {
        $query = static::where('user_id', $this->user_id)
            ->where('status', '!=', 'cancelled')
            ->where(function ($q) use ($startDateTime, $endDateTime) {
                $q->whereBetween('start_datetime', [$startDateTime, $endDateTime])
                  ->orWhereBetween('end_datetime', [$startDateTime, $endDateTime])
                  ->orWhere(function ($overlap) use ($startDateTime, $endDateTime) {
                      $overlap->where('start_datetime', '<=', $startDateTime)
                              ->where('end_datetime', '>=', $endDateTime);
                  });
            });

        if ($excludeEventId) {
            $query->where('id', '!=', $excludeEventId);
        }

        return $query->exists();
    }
}
