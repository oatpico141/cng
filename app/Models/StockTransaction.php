<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockTransaction extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'transaction_number','stock_item_id','branch_id','transaction_type','quantity','quantity_before','quantity_after','transaction_date','description','from_branch_id','to_branch_id','treatment_id','reference_number','unit_cost','total_cost','created_by'
    ];

    protected $casts = [
        'transaction_date'=>'date','unit_cost'=>'decimal:2','total_cost'=>'decimal:2'
    ];

    public function stockItem() { return $this->belongsTo(StockItem::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function fromBranch() { return $this->belongsTo(Branch::class, 'from_branch_id'); }
    public function toBranch() { return $this->belongsTo(Branch::class, 'to_branch_id'); }
    public function treatment() { return $this->belongsTo(Treatment::class); }
}
