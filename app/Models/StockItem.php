<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class StockItem extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'item_code','name','description','category','unit','branch_id','quantity_on_hand','minimum_quantity','maximum_quantity','unit_cost','unit_price','supplier','supplier_item_code','is_active','notes','created_by'
    ];

    protected $casts = [
        'unit_cost'=>'decimal:2','unit_price'=>'decimal:2','is_active'=>'boolean'
    ];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function transactions() { return $this->hasMany(StockTransaction::class); }

    // Helper: Check if low stock
    public function isLowStock(): bool
    {
        return $this->quantity_on_hand <= $this->minimum_quantity;
    }
}
