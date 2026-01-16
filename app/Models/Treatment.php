<?php

namespace App\Models;

use App\Models\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Treatment extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The "booted" method of the model.
     * Apply BranchScope to ensure data isolation by branch
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new BranchScope);

        // Automatically set branch_id when creating new Treatment
        static::creating(function (Treatment $treatment) {
            if (!$treatment->branch_id) {
                $treatment->branch_id = session('selected_branch_id');
            }
        });
    }

    protected $fillable = [
        'opd_id','patient_id','appointment_id','queue_id','branch_id','pt_id','service_id',
        'chief_complaint','vital_signs','assessment','diagnosis','treatment_plan','treatment_notes',
        'home_program','started_at','completed_at','duration_minutes','invoice_id','course_purchase_id','billing_status','created_by','df_amount'
    ];

    protected $casts = [
        'vital_signs'=>'array','started_at'=>'datetime','completed_at'=>'datetime'
    ];

    public function opdRecord() { return $this->belongsTo(OpdRecord::class, 'opd_id'); }
    public function patient() { return $this->belongsTo(Patient::class); }
    public function appointment() { return $this->belongsTo(Appointment::class); }
    public function queue() { return $this->belongsTo(Queue::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function pt() { return $this->belongsTo(User::class, 'pt_id'); }
    public function service() { return $this->belongsTo(Service::class); }
    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function coursePurchase() { return $this->belongsTo(CoursePurchase::class); }
    public function auditLogs() { return $this->hasMany(TreatmentAuditLog::class); }
    public function courseUsageLogs() { return $this->hasMany(CourseUsageLog::class); }
    public function dfPayments() { return $this->hasMany(DfPayment::class); }
}
