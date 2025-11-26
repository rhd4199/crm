<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
// use Illuminate\Database\Eloquent\SoftDeletes; // kalau nanti mau soft delete user

class User extends Authenticatable
{
    use HasFactory, Notifiable; // , SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'company_id',
        'name',
        'email',
        'password',
        'global_role',
        'company_role',
        'is_active',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'is_active' => 'boolean',
    ];

    // --- Constants optional, biar nggak magic string ---
    public const GLOBAL_ROLE_SUPER_ADMIN = 'super_admin';
    public const GLOBAL_ROLE_USER = 'user';

    public const COMPANY_ROLE_ADMIN = 'admin';
    public const COMPANY_ROLE_MARKETING = 'marketing';
    public const COMPANY_ROLE_CS = 'customer_service';

    // --- Relasi ---
    public function company()
    {
        return $this->belongsTo(Company::class);
    }

    // Customer yang di-handle sebagai marketing
    public function marketingCustomers()
    {
        return $this->hasMany(Customer::class, 'assigned_marketing_id');
    }

    // Customer yang di-handle sebagai CS
    public function csCustomers()
    {
        return $this->hasMany(Customer::class, 'assigned_cs_id');
    }

    public function interactions()
    {
        return $this->hasMany(Interaction::class);
    }

    public function pipelineStageHistories()
    {
        return $this->hasMany(PipelineStageHistory::class, 'changed_by');
    }

    public function activityLogs()
    {
        return $this->hasMany(ActivityLog::class);
    }

    // Helper simple
    public function isSuperAdmin(): bool
    {
        return $this->global_role === self::GLOBAL_ROLE_SUPER_ADMIN;
    }

    public function isCompanyAdmin(): bool
    {
        return $this->company_role === self::COMPANY_ROLE_ADMIN;
    }
}
