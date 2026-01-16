<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class Document extends Model {
    use HasFactory, HasUuids, SoftDeletes;
    protected $fillable = ['document_type','document_number','invoice_id','payment_id','patient_id','branch_id','file_path','file_name','file_size','document_date','status','notes','created_by'];
    protected $casts = ['document_date'=>'date'];
    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function payment() { return $this->belongsTo(Payment::class); }
    public function patient() { return $this->belongsTo(Patient::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
}
