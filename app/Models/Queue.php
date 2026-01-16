<?php

namespace App\Models;

use App\Models\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Queue extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The "booted" method of the model.
     * Apply BranchScope to ensure data isolation by branch
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new BranchScope);

        // Automatically set branch_id when creating new Queue
        static::creating(function (Queue $queue) {
            if (!$queue->branch_id) {
                $queue->branch_id = session('selected_branch_id');
            }
        });
    }

    protected $fillable = [
        'appointment_id', 'patient_id', 'branch_id', 'pt_id', 'queue_number', 'status',
        'queued_at', 'called_at', 'started_at', 'completed_at', 'waiting_time_minutes',
        'is_overtime', 'overtime_warning_sent_at', 'notes', 'created_by',
    ];

    protected $casts = [
        'queued_at' => 'datetime',
        'called_at' => 'datetime',
        'started_at' => 'datetime',
        'completed_at' => 'datetime',
        'is_overtime' => 'boolean',
        'overtime_warning_sent_at' => 'datetime',
    ];

    public function appointment() { return $this->belongsTo(Appointment::class); }
    public function patient() { return $this->belongsTo(Patient::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function pt() { return $this->belongsTo(User::class, 'pt_id'); }
}
