<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LoyaltyPoint extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'patient_id','total_points_earned','total_points_redeemed','membership_tier','tier_start_date','points_to_next_tier','expiring_points','next_expiry_date','lifetime_spending','total_visits','last_transaction_date'
    ];

    protected $casts = [
        'tier_start_date'=>'date','next_expiry_date'=>'date','lifetime_spending'=>'decimal:2','last_transaction_date'=>'date'
    ];

    public function patient() { return $this->belongsTo(Patient::class); }

    // Helper: Get current balance
    public function getCurrentBalanceAttribute()
    {
        return $this->total_points_earned - $this->total_points_redeemed;
    }
}
