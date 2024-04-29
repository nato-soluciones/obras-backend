<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObraDailyLog extends Model
{
    use HasFactory;

    protected $fillable = [
        'event_date',
        'file_name',
        'comment',
        'created_by_id',
        'obra_daily_log_tag_id',
        'obra_id',
    ];

    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }

    public function obraDailyLogTag()
    {
        return $this->belongsTo(ObraDailyLogTag::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }
}
