<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseSharedUser extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'course_purchase_id',
        'owner_patient_id',
        'shared_patient_id',
        'relationship',
        'notes',
        'is_active',
        'max_sessions',
        'used_sessions',
        'created_by',
    ];

    protected $casts = [
        'is_active' => 'boolean',
        'max_sessions' => 'integer',
        'used_sessions' => 'integer',
    ];

    // Relationships
    public function coursePurchase()
    {
        return $this->belongsTo(CoursePurchase::class);
    }

    public function ownerPatient()
    {
        return $this->belongsTo(Patient::class, 'owner_patient_id');
    }

    public function sharedPatient()
    {
        return $this->belongsTo(Patient::class, 'shared_patient_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    // Check if shared user can still use the course
    public function canUse()
    {
        if (!$this->is_active) {
            return false;
        }

        if ($this->max_sessions === null) {
            return true; // Unlimited sessions
        }

        return $this->used_sessions < $this->max_sessions;
    }

    // Increment session usage
    public function incrementUsage()
    {
        $this->increment('used_sessions');
    }

    // Get remaining sessions
    public function getRemainingSessionsAttribute()
    {
        if ($this->max_sessions === null) {
            return 'ไม่จำกัด';
        }

        $remaining = $this->max_sessions - $this->used_sessions;
        return max(0, $remaining);
    }
}
