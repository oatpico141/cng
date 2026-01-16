<?php

namespace App\Models;

use App\Models\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Service extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The "booted" method of the model.
     * Apply BranchScope to ensure data isolation by branch
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new BranchScope);

        // Automatically set branch_id when creating new Service
        static::creating(function (Service $service) {
            if (!$service->branch_id) {
                $service->branch_id = session('selected_branch_id');
            }
        });
    }

    protected $fillable = [
        'name',
        'code',
        'description',
        'category',
        'category_id',
        'default_price',
        'default_duration_minutes',
        'is_active',
        'is_package',
        'package_sessions',
        'package_validity_days',
        'default_commission_rate',
        'default_df_rate',
        'branch_id',
        'created_by',
    ];

    protected $casts = [
        'default_price' => 'decimal:2',
        'is_active' => 'boolean',
        'is_package' => 'boolean',
        'default_commission_rate' => 'decimal:2',
        'default_df_rate' => 'decimal:2',
    ];

    // Relationships
    public function ptServiceRates()
    {
        return $this->hasMany(PtServiceRate::class);
    }

    public function invoiceItems()
    {
        return $this->hasMany(InvoiceItem::class);
    }

    public function treatments()
    {
        return $this->hasMany(Treatment::class);
    }

    public function commissionRates()
    {
        return $this->hasMany(CommissionRate::class);
    }

    public function createdBy()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function serviceCategory()
    {
        return $this->belongsTo(ServiceCategory::class, 'category_id');
    }
}
