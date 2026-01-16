<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class CrmCall extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'patient_id',
        'branch_id',
        'appointment_id',
        'treatment_id',
        'call_type',
        'scheduled_date',
        'cutoff_time',
        'status',
        'notes',
        'patient_feedback',
        'called_by',
        'called_at',
    ];

    protected $casts = [
        'scheduled_date' => 'date',
        'called_at' => 'datetime',
    ];

    // Relationships
    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }

    public function caller()
    {
        return $this->belongsTo(User::class, 'called_by');
    }

    // Scopes
    public function scopeConfirmation($query)
    {
        return $query->where('call_type', 'confirmation');
    }

    public function scopeFollowUp($query)
    {
        return $query->where('call_type', 'follow_up');
    }

    public function scopePending($query)
    {
        return $query->where('status', 'pending');
    }

    public function scopeForDate($query, $date)
    {
        return $query->whereDate('scheduled_date', $date);
    }

    // Helper methods
    public function getStatusLabelAttribute()
    {
        return match($this->status) {
            'pending' => 'รอโทร',
            'called' => 'โทรแล้ว',
            'no_answer' => 'ไม่รับสาย',
            'confirmed' => 'ยืนยัน',
            'cancelled' => 'ยกเลิก',
            'rescheduled' => 'เลื่อนนัด',
            default => $this->status,
        };
    }

    public function getCallTypeLabelAttribute()
    {
        return match($this->call_type) {
            'confirmation' => 'ยืนยันนัดหมาย',
            'follow_up' => 'ติดตามอาการ',
            default => $this->call_type,
        };
    }
}
