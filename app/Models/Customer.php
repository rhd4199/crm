<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Customer extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'phone',
        'email',
        'source',
        'tag',
        'assigned_marketing_id',
        'assigned_cs_id',
        'current_stage_id',
        'created_by',
        'notes',
        'estimated_value',
        'last_contact_at',
    ];

    protected $casts = [
        'estimated_value' => 'decimal:2',
        'last_contact_at' => 'datetime',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function marketing()
    {
        return $this->belongsTo(User::class, 'assigned_marketing_id');
    }

    public function customerService()
    {
        return $this->belongsTo(User::class, 'assigned_cs_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function currentStage()
    {
        return $this->belongsTo(PipelineStage::class, 'current_stage_id');
    }

    public function stageHistories()
    {
        return $this->hasMany(PipelineStageHistory::class);
    }

    public function interactions()
    {
        return $this->hasMany(Interaction::class);
    }

    public function activityLogs()
    {
        return $this->morphMany(ActivityLog::class, 'loggable');
    }
}
