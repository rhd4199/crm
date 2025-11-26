<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PipelineStage extends Model
{
    use HasFactory, SoftDeletes;

    protected $fillable = [
        'company_id',
        'name',
        'type',
        'sort_order',
        'is_default',
    ];

    protected $casts = [
        'is_default' => 'boolean',
    ];

    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    public function customers()
    {
        return $this->hasMany(Customer::class, 'current_stage_id');
    }

    public function fromHistories()
    {
        return $this->hasMany(PipelineStageHistory::class, 'from_stage_id');
    }

    public function toHistories()
    {
        return $this->hasMany(PipelineStageHistory::class, 'to_stage_id');
    }
}
