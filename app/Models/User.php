<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable, HasUuids, SoftDeletes;

    protected $fillable = [
        'name',
        'username',
        'password',
        'role_id',
        'branch_id',
        'email',
        'is_active',
        'last_login_at',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'last_login_at' => 'datetime',
    ];

    // Relationships
    public function role()
    {
        return $this->belongsTo(Role::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function staff()
    {
        return $this->hasOne(Staff::class);
    }

    public function createdPatientNotes()
    {
        return $this->hasMany(PatientNote::class, 'created_by');
    }

    public function createdAppointments()
    {
        return $this->hasMany(Appointment::class, 'created_by');
    }

    public function notifications()
    {
        return $this->hasMany(Notification::class);
    }

    // RBAC Helper Methods
    public function hasPermission(string $module, string $action): bool
    {
        return $this->role && $this->role->hasPermission($module, $action);
    }

    public function can($ability, $arguments = [])
    {
        if (is_string($ability) && str_contains($ability, '.')) {
            [$module, $action] = explode('.', $ability, 2);
            return $this->hasPermission($module, $action);
        }

        return parent::can($ability, $arguments);
    }

    // Branch Access Control Methods
    public function isAdmin(): bool
    {
        return $this->role && strtolower($this->role->name) === 'admin';
    }

    public function isAreaManager(): bool
    {
        return $this->role && strtolower($this->role->name) === 'area manager';
    }

    public function needsBranchSelection(): bool
    {
        return $this->isAdmin() || $this->isAreaManager();
    }

    public function isStandardUser(): bool
    {
        return !$this->needsBranchSelection();
    }

    // Get current active branch from session
    public function getCurrentBranch()
    {
        if ($this->isStandardUser()) {
            return $this->branch;
        }

        $selectedBranchId = session('selected_branch_id');
        return $selectedBranchId ? Branch::find($selectedBranchId) : null;
    }
}
