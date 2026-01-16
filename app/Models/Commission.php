<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Commission extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'commission_number','pt_id','invoice_id','invoice_item_id','treatment_id','branch_id','commission_type','base_amount','commission_rate','commission_amount','status','commission_date','is_clawback_eligible','clawed_back_at','clawed_back_by','clawback_reason','clawback_refund_id','paid_at','paid_by','payment_reference','notes','created_by'
    ];

    protected $casts = [
        'base_amount'=>'decimal:2','commission_rate'=>'decimal:2','commission_amount'=>'decimal:2','commission_date'=>'date','is_clawback_eligible'=>'boolean','clawed_back_at'=>'datetime','paid_at'=>'datetime'
    ];

    public function pt() { return $this->belongsTo(User::class, 'pt_id'); }
    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function invoiceItem() { return $this->belongsTo(InvoiceItem::class); }
    public function treatment() { return $this->belongsTo(Treatment::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function clawbackRefund() { return $this->belongsTo(Refund::class, 'clawback_refund_id'); }
    public function splits() { return $this->hasMany(CommissionSplit::class); }

    // Helper: Clawback commission
    public function clawback()
    {
        $this->update(['status' => 'clawed_back', 'clawed_back_at' => now()]);
    }
}
