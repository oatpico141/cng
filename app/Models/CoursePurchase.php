<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoursePurchase extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'course_number','patient_id','package_id','invoice_id','purchase_branch_id','purchase_pattern','purchase_date','activation_date','expiry_date','total_sessions','used_sessions','status','allow_branch_sharing','allowed_branches','cancellation_reason','cancelled_at','cancelled_by','created_by','payment_type','installment_total','installment_paid','installment_amount','seller_ids'
    ];

    protected $casts = [
        'purchase_date'=>'date','activation_date'=>'date','expiry_date'=>'date','allow_branch_sharing'=>'boolean','allowed_branches'=>'array','cancelled_at'=>'datetime','installment_amount'=>'decimal:2','seller_ids'=>'array'
    ];

    public function patient() { return $this->belongsTo(Patient::class); }
    public function package() { return $this->belongsTo(CoursePackage::class, 'package_id'); }
    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function purchaseBranch() { return $this->belongsTo(Branch::class, 'purchase_branch_id'); }
    public function usageLogs() { return $this->hasMany(CourseUsageLog::class); }
    public function sharingRecords() { return $this->hasMany(CourseSharing::class); }
    public function renewals() { return $this->hasMany(CourseRenewal::class); }
    public function sharedUsers() { return $this->hasMany(CourseSharedUser::class)->where('is_active', true); }

    // Helper: Get remaining sessions
    public function getRemainingSessionsAttribute()
    {
        return $this->total_sessions - $this->used_sessions;
    }

    // Helper: Can be used?
    public function canBeUsed(): bool
    {
        return $this->status === 'active' && $this->getRemainingSessionsAttribute() > 0;
    }

    // Helper: Has pending installments?
    public function hasPendingInstallment(): bool
    {
        return $this->payment_type === 'installment' && $this->installment_paid < $this->installment_total;
    }

    // Helper: Get remaining installments
    public function getRemainingInstallmentsAttribute(): int
    {
        if ($this->payment_type !== 'installment') return 0;
        return $this->installment_total - $this->installment_paid;
    }
}
