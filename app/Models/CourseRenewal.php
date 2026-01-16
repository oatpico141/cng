<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseRenewal extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'renewal_number','course_purchase_id','patient_id','branch_id','renewal_date','old_expiry_date','new_expiry_date','extension_days','renewal_fee','renewal_reason','notes','invoice_id','created_by'
    ];

    protected $casts = [
        'renewal_date'=>'date','old_expiry_date'=>'date','new_expiry_date'=>'date','renewal_fee'=>'decimal:2'
    ];

    public function coursePurchase() { return $this->belongsTo(CoursePurchase::class); }
    public function patient() { return $this->belongsTo(Patient::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function invoice() { return $this->belongsTo(Invoice::class); }
}
