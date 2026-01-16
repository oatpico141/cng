<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class MaintenanceLog extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'maintenance_number','equipment_id','branch_id','maintenance_type','maintenance_date','description','work_performed','performed_by','service_provider','cost','status','next_maintenance_date','parts_used','notes','created_by'
    ];

    protected $casts = [
        'maintenance_date'=>'date','cost'=>'decimal:2','next_maintenance_date'=>'date','parts_used'=>'array'
    ];

    public function equipment() { return $this->belongsTo(Equipment::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
}
