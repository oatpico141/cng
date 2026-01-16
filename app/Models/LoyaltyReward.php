<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class LoyaltyReward extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'reward_code','name','description','reward_type','points_required','discount_amount','discount_percentage','service_id','is_active','valid_from','valid_to','max_redemptions','max_per_patient','current_redemptions','minimum_tier','allowed_branches','terms_and_conditions','created_by'
    ];

    protected $casts = [
        'discount_amount'=>'decimal:2','discount_percentage'=>'decimal:2','is_active'=>'boolean','valid_from'=>'date','valid_to'=>'date','allowed_branches'=>'array'
    ];

    public function service() { return $this->belongsTo(Service::class); }
    public function redemptions() { return $this->hasMany(LoyaltyTransaction::class, 'reward_id'); }
}
