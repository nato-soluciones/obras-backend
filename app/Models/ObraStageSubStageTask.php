<?php

namespace App\Models;

use App\Models\QualityControl\QualityControl;
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
        'has_quality_control',
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

    public function taskEvents()
    {
        return $this->hasMany(ObraStageSubStageTaskEvent::class);
    }

    public function qualityControls()
    {
        return $this->morphOne(QualityControl::class, 'entity');
    }
}
