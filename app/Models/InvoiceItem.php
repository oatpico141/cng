<?php
namespace App\Models;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
class InvoiceItem extends Model {
    use HasFactory, HasUuids, SoftDeletes;
    protected $fillable = ['invoice_id','service_id','package_id','treatment_id','item_type','description','quantity','unit_price','discount_amount','total_amount','pt_id'];
    protected $casts = ['unit_price'=>'decimal:2','discount_amount'=>'decimal:2','total_amount'=>'decimal:2'];
    public function invoice() { return $this->belongsTo(Invoice::class); }
    public function service() { return $this->belongsTo(Service::class); }
    public function package() { return $this->belongsTo(CoursePackage::class, 'package_id'); }
    public function treatment() { return $this->belongsTo(Treatment::class); }
    public function pt() { return $this->belongsTo(User::class, 'pt_id'); }
}
