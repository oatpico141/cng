<?php
namespace App\Models;
use App\Models\Scopes\BranchScope;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Invoice extends Model {
    use HasFactory, HasUuids, SoftDeletes;

    /**
     * The "booted" method of the model.
     * Apply BranchScope to ensure data isolation by branch
     */
    protected static function booted(): void
    {
        static::addGlobalScope(new BranchScope);

        // Automatically set branch_id when creating new Invoice
        static::creating(function (Invoice $invoice) {
            if (!$invoice->branch_id) {
                $invoice->branch_id = session('selected_branch_id');
            }
        });
    }
    protected $fillable = ['invoice_number','patient_id','opd_id','branch_id','invoice_type','subtotal','discount_amount','tax_amount','total_amount','paid_amount','outstanding_amount','status','invoice_date','due_date','installment_months','installment_amount','down_payment','notes','created_by'];
    protected $casts = ['subtotal'=>'decimal:2','discount_amount'=>'decimal:2','tax_amount'=>'decimal:2','total_amount'=>'decimal:2','paid_amount'=>'decimal:2','outstanding_amount'=>'decimal:2','invoice_date'=>'date','due_date'=>'date','installment_amount'=>'decimal:2','down_payment'=>'decimal:2'];
    public function patient() { return $this->belongsTo(Patient::class); }
    public function opdRecord() { return $this->belongsTo(OpdRecord::class, 'opd_id'); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function items() { return $this->hasMany(InvoiceItem::class); }
    public function payments() { return $this->hasMany(Payment::class); }
    public function documents() { return $this->hasMany(Document::class); }
    public function refunds() { return $this->hasMany(Refund::class); }
    public function coursePurchases() { return $this->hasMany(CoursePurchase::class); }
    public function createdBy() { return $this->belongsTo(User::class, 'created_by'); }
}
