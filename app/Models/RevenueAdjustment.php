<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RevenueAdjustment extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'invoice_id',
        'refund_id',
        'branch_id',
        'adjustment_type',
        'adjustment_amount',
        'effective_date', // CRITICAL: Backdate to original invoice date
        'adjustment_date',
        'description',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'adjustment_amount' => 'decimal:2',
        'effective_date' => 'date',
        'adjustment_date' => 'date',
    ];

    // Relationships
    public function invoice()
    {
        return $this->belongsTo(Invoice::class);
    }

    public function refund()
    {
        return $this->belongsTo(Refund::class);
    }

    public function branch()
    {
        return $this->belongsTo(Branch::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
