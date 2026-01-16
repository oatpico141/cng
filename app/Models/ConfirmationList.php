<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class ConfirmationList extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'appointment_id', 'patient_id', 'branch_id', 'appointment_date', 'appointment_time',
        'confirmation_status', 'confirmed_at', 'confirmed_by', 'confirmation_notes',
        'call_attempts', 'last_call_attempt_at', 'is_auto_generated', 'generated_date',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'confirmed_at' => 'datetime',
        'last_call_attempt_at' => 'datetime',
        'is_auto_generated' => 'boolean',
        'generated_date' => 'date',
    ];

    public function appointment() { return $this->belongsTo(Appointment::class); }
    public function patient() { return $this->belongsTo(Patient::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function confirmedBy() { return $this->belongsTo(User::class, 'confirmed_by'); }
}
