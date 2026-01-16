<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Staff extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id', 'branch_id', 'employee_id', 'first_name', 'last_name', 'phone', 'email',
        'date_of_birth', 'gender', 'address', 'position', 'department', 'hire_date',
        'termination_date', 'employment_status', 'employment_type', 'license_number',
        'license_expiry', 'certifications', 'base_salary', 'salary_type', 'notes', 'created_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
        'hire_date' => 'date',
        'termination_date' => 'date',
        'license_expiry' => 'date',
        'certifications' => 'array',
        'base_salary' => 'decimal:2',
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function schedules() { return $this->hasMany(Schedule::class); }
    public function leaveRequests() { return $this->hasMany(LeaveRequest::class); }
    public function evaluations() { return $this->hasMany(Evaluation::class); }
    public function ptReplacementsAsOriginal() { return $this->hasMany(PtReplacement::class, 'original_pt_id'); }
    public function ptReplacementsAsReplacement() { return $this->hasMany(PtReplacement::class, 'replacement_pt_id'); }
}
