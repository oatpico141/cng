<?php

namespace App\Models;

use App\Models\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CoursePackage extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The "booted" method of the model.
     * Apply BranchScope to ensure data isolation by branch
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new BranchScope);

        // Automatically set branch_id when creating new CoursePackage
        static::creating(function (CoursePackage $package) {
            if (!$package->branch_id) {
                $package->branch_id = session('selected_branch_id');
            }
        });
    }

    protected $fillable = [
        'name',
        'code',
        'description',
        'price',
        'paid_sessions',
        'bonus_sessions',
        'total_sessions',
        'validity_days',
        'is_active',
        'service_id',
        'commission_rate',
        'commission_installment',
        'per_session_commission_rate',
        'df_rate',
        'df_amount',
        'allow_buy_and_use',
        'allow_buy_for_later',
        'allow_retroactive',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'is_active' => 'boolean',
        'commission_rate' => 'decimal:2',
        'commission_installment' => 'decimal:2',
        'per_session_commission_rate' => 'decimal:2',
        'df_rate' => 'decimal:2',
        'allow_buy_and_use' => 'boolean',
        'allow_buy_for_later' => 'boolean',
        'allow_retroactive' => 'boolean',
    ];

    // Relationships
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function coursePurchases()
    {
        return $this->hasMany(CoursePurchase::class, 'package_id');
    }

    public function commissionRates()
    {
        return $this->hasMany(CommissionRate::class, 'package_id');
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
