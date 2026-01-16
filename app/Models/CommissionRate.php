<?php

namespace App\Models;

use App\Models\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommissionRate extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The "booted" method of the model.
     * Apply BranchScope to ensure data isolation by branch
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new BranchScope);

        // Automatically set branch_id when creating new CommissionRate
        static::creating(function (CommissionRate $rate) {
            if (!$rate->branch_id) {
                $rate->branch_id = session('selected_branch_id');
            }
        });
    }

    protected $fillable = [
        'rate_type',
        'service_id',
        'package_id',
        'pt_id',
        'branch_id',
        'commission_percentage',
        'df_percentage',
        'fixed_amount',
        'effective_from',
        'effective_to',
        'is_active',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'commission_percentage' => 'decimal:2',
        'df_percentage' => 'decimal:2',
        'fixed_amount' => 'decimal:2',
        'effective_from' => 'date',
        'effective_to' => 'date',
        'is_active' => 'boolean',
    ];

    // Relationships
    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function package()
    {
        return $this->belongsTo(CoursePackage::class, 'package_id');
    }

    public function pt()
    {
        return $this->belongsTo(User::class, 'pt_id');
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
