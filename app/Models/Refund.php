<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Refund extends Model {
    use HasFactory, HasUuids, SoftDeletes;
    protected $fillable = ['refund_number','invoice_id','patient_id','branch_id','refund_type','refund_amount','status','refund_date','original_amount','used_amount','penalty_amount','calculation_notes','reason','approved_at','approved_by','rejection_reason','refund_method','reference_number','created_by'];
    protected $casts = ['refund_amount'=>'decimal:2','refund_date'=>'date','original_amount'=>'decimal:2','used_amount'=>'decimal:2','penalty_amount'=>'decimal:2','approved_at'=>'datetime'];
    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function patient() { return $this->belongsTo(Patient::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function approvedBy() { return $this->belongsTo(User::class, 'approved_by'); }
    public function commissions() { return $this->hasMany(Commission::class, 'clawback_refund_id'); }
}
