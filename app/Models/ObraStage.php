<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObraStage extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'start_date',
        'end_date',
        'description',
        'progress',
        'obra_id',
        'created_by_id',
    ];

    public function obra()
    {
        return $this->belongsTo(Obra::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function obraStageTask()
    {
        return $this->hasMany(ObraStageTask::class);
    }
}
