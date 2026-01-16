<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseUsageLog extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'course_purchase_id','treatment_id','patient_id','branch_id','pt_id','sessions_used','usage_date','status','is_cross_branch','purchase_branch_id','cancellation_reason','cancelled_at','cancelled_by','created_by'
    ];

    protected $casts = ['usage_date'=>'date','is_cross_branch'=>'boolean','cancelled_at'=>'datetime'];

    public function coursePurchase() { return $this->belongsTo(CoursePurchase::class); }
    public function treatment() { return $this->belongsTo(Treatment::class); }
    public function patient() { return $this->belongsTo(Patient::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function pt() { return $this->belongsTo(User::class, 'pt_id'); }
    public function purchaseBranch() { return $this->belongsTo(Branch::class, 'purchase_branch_id'); }
}
