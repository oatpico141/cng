<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LeaveRequest extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'leave_number', 'staff_id', 'branch_id', 'leave_type', 'start_date', 'end_date',
        'total_days', 'status', 'reason', 'submitted_at', 'approved_at', 'approved_by',
        'approval_notes', 'rejection_reason', 'attachment_path', 'created_by',
    ];

    protected $casts = [
        'start_date' => 'date',
        'end_date' => 'date',
        'submitted_at' => 'datetime',
        'approved_at' => 'datetime',
    ];

    public function staff() { return $this->belongsTo(Staff::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }
}
