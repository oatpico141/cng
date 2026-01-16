<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Equipment extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'equipment_code','name','description','category','branch_id','status','purchase_date','purchase_price','supplier','serial_number','warranty_number','warranty_expiry','last_maintenance_date','next_maintenance_date','maintenance_interval_days','current_value','useful_life_years','notes','created_by'
    ];

    protected $casts = [
        'purchase_date'=>'date','purchase_price'=>'decimal:2','warranty_expiry'=>'date','last_maintenance_date'=>'date','next_maintenance_date'=>'date','current_value'=>'decimal:2'
    ];

    public function branch() { return $this->belongsTo(Branch::class); }
    public function maintenanceLogs() { return $this->hasMany(MaintenanceLog::class); }
}
