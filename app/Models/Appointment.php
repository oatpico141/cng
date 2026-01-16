<?php

namespace App\Models;

use App\Models\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Appointment extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The "booted" method of the model.
     * Apply BranchScope to ensure data isolation by branch
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new BranchScope);

        // Automatically set branch_id when creating new Appointment
        static::creating(function (Appointment $appointment) {
            if (!$appointment->branch_id) {
                $appointment->branch_id = session('selected_branch_id');
            }
        });
    }

    protected $fillable = [
        'patient_id', 'branch_id', 'pt_id', 'appointment_date', 'appointment_time',
        'booking_channel', 'status', 'notes', 'purpose', 'requested_pt_id', 'pt_change_reason',
        'pt_changed_at', 'pt_changed_by', 'cancellation_reason', 'cancelled_at',
        'cancelled_by', 'created_by',
    ];

    protected $casts = [
        'appointment_date' => 'date',
        'pt_changed_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    public function patient() { return $this->belongsTo(Patient::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function pt() { return $this->belongsTo(User::class, 'pt_id'); }
    public function requestedPt() { return $this->belongsTo(User::class, 'requested_pt_id'); }
    public function queue() { return $this->hasOne(Queue::class); }
    public function confirmationList() { return $this->hasOne(ConfirmationList::class); }
    public function ptRequests() { return $this->hasMany(PtRequest::class); }
    public function treatments() { return $this->hasMany(Treatment::class); }
}
