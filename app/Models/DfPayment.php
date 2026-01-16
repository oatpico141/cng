<?php

namespace App\Models;

use App\Models\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
class DfPayment extends Model
{
    use HasFactory, HasUuids;

    /**
     * The "booted" method of the model.
     * Apply BranchScope to ensure data isolation by branch
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new BranchScope);

        // Automatically set branch_id when creating new DfPayment
        static::creating(function (DfPayment $dfPayment) {
            if (!$dfPayment->branch_id) {
                $dfPayment->branch_id = session('selected_branch_id');
            }

            // Auto-generate df_number if not provided
            if (!$dfPayment->df_number) {
                $prefix = 'DF' . now()->format('Ymd');
                $latestDf = self::withoutGlobalScopes()
                    ->where('df_number', 'like', $prefix . '%')
                    ->orderBy('df_number', 'desc')
                    ->first();

                if ($latestDf) {
                    $lastNumber = intval(substr($latestDf->df_number, -4));
                    $nextNumber = $lastNumber + 1;
                } else {
                    $nextNumber = 1;
                }

                $dfPayment->df_number = $prefix . str_pad($nextNumber, 4, '0', STR_PAD_LEFT);
            }

            // Set default values
            if (!$dfPayment->payment_type) {
                $dfPayment->payment_type = 'treatment_df';
            }
            if (!$dfPayment->status) {
                $dfPayment->status = 'pending';
            }
            if (!$dfPayment->df_date) {
                $dfPayment->df_date = now()->format('Y-m-d');
            }
            if (!$dfPayment->payment_date) {
                $dfPayment->payment_date = $dfPayment->df_date;
            }

            // Calculate df_amount from base_amount and df_rate if not set
            if (!$dfPayment->df_amount && $dfPayment->base_amount && $dfPayment->df_rate) {
                $dfPayment->df_amount = $dfPayment->base_amount * ($dfPayment->df_rate / 100);
            }

            // Ensure amount matches df_amount if not explicitly set
            if (!$dfPayment->amount && $dfPayment->df_amount) {
                $dfPayment->amount = $dfPayment->df_amount;
            }

            // Default is_clawback_eligible to false
            if (is_null($dfPayment->is_clawback_eligible)) {
                $dfPayment->is_clawback_eligible = false;
            }
        });
    }

    protected $fillable = [
        'df_number',
        'treatment_id',
        'pt_id',
        'service_id',
        'course_purchase_id',
        'invoice_id',
        'branch_id',
        'payment_type',
        'base_amount',
        'df_rate',
        'df_amount',
        'amount',
        'status',
        'df_date',
        'source_type',
        'payment_date',
        'is_clawback_eligible',
        'paid_at',
        'paid_by',
        'payment_reference',
        'notes',
        'created_by',
    ];

    protected $casts = [
        'base_amount' => 'decimal:2',
        'df_rate' => 'decimal:2',
        'df_amount' => 'decimal:2',
        'amount' => 'decimal:2',
        'df_date' => 'date',
        'payment_date' => 'date',
        'paid_at' => 'datetime',
        'is_clawback_eligible' => 'boolean',
    ];

    public function service()
    {
        return $this->belongsTo(Service::class);
    }

    public function coursePurchase()
    {
        return $this->belongsTo(CoursePurchase::class);
    }

    public function pt() { return $this->belongsTo(User::class, 'pt_id'); }
    public function treatment() { return $this->belongsTo(Treatment::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
}
