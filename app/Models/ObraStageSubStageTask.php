<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ObraStageSubStageTask extends Model
{
    use HasFactory;

    protected $fillable = [
        'title',
        'start_date',
        'end_date',
        'description',
        'progress_type',
        'progress',
        'max_quantity',
        'current_quantity',
        'is_completed',
        'responsible_id',
        'obra_stage_id',
        'obra_stage_sub_stage_id',
        'created_by_id',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
        'created_by_id',
    ];

    public function obraStage()
    {
        return $this->belongsTo(ObraStage::class);
    }

    public function obraStageSubStage()
    {
        return $this->belongsTo(ObraStageSubStage::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by_id');
    }

    public function responsible()
    {
        return $this->belongsTo(User::class, 'responsible_id');
    }
}
