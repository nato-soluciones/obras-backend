<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Reminder extends Model
{
    protected $fillable = [
        'text',
        'datetime',
        'user_id',
        'priority',
        'is_resolved',
        'date_resolved',
        'created_by',
    ];

    protected $casts = [
        'datetime' => 'datetime',
        'is_resolved' => 'boolean',
        'date_resolved' => 'datetime',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function scopeToday($query)
    {
        return $query->whereDate('datetime', today());
    }

    public function scopePending($query)
    {
        return $query->where('is_resolved', false);
    }

    public function scopeResolved($query)
    {
        return $query->where('is_resolved', true);
    }

    public function scopeCreatedByMe($query, $userId)
    {
        return $query->where('created_by', $userId);
    }

    public function scopeAssignedToMe($query, $userId)
    {
        return $query->where('user_id', $userId);
    }

    public function scopeOverdue($query)
    {
        return $query->where('datetime', '<', now())
                    ->where('is_resolved', false);
    }
}
