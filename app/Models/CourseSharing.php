<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CourseSharing extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'course_purchase_id','from_branch_id','to_branch_id','is_active','max_sessions','used_sessions','notes','approved_at','approved_by','created_by'
    ];

    protected $casts = ['is_active'=>'boolean','approved_at'=>'datetime'];

    public function coursePurchase() { return $this->belongsTo(CoursePurchase::class); }
    public function fromBranch() { return $this->belongsTo(Branch::class, 'from_branch_id'); }
    public function toBranch() { return $this->belongsTo(Branch::class, 'to_branch_id'); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }
}
