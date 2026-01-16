<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Schedule extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'staff_id', 'branch_id', 'schedule_date', 'start_time', 'end_time', 'schedule_type',
        'status', 'is_available', 'break_start', 'break_end', 'is_recurring',
        'recurrence_pattern', 'recurrence_end_date', 'notes', 'created_by',
    ];

    protected $casts = [
        'schedule_date' => 'date',
        'is_available' => 'boolean',
        'is_recurring' => 'boolean',
        'recurrence_end_date' => 'date',
    ];

    public function staff() { return $this->belongsTo(Staff::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
}
