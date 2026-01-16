<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoyaltyTransaction extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'transaction_number','patient_id','branch_id','transaction_type','points','balance_before','balance_after','transaction_date','invoice_id','spending_amount','points_rate','reward_id','discount_amount','expiry_date','is_expired','description','created_by'
    ];

    protected $casts = [
        'transaction_date'=>'date','spending_amount'=>'decimal:2','points_rate'=>'decimal:2','discount_amount'=>'decimal:2','expiry_date'=>'date','is_expired'=>'boolean'
    ];

    public function patient() { return $this->belongsTo(Patient::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function reward() { return $this->belongsTo(LoyaltyReward::class, 'reward_id'); }
}
