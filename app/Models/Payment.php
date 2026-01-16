<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Payment extends Model {
    use HasFactory, HasUuids, SoftDeletes;
    protected $fillable = ['payment_number','invoice_id','patient_id','branch_id','amount','payment_method','status','payment_date','reference_number','card_type','card_last_4','installment_number','total_installments','notes','created_by'];
    protected $casts = ['amount'=>'decimal:2','payment_date'=>'date'];
    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function patient() { return $this->belongsTo(Patient::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
}
