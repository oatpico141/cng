<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AuditLog extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'user_id','action','module','model_type','model_id','old_values','new_values','ip_address','user_agent','url','method','description','branch_id'
    ];

    protected $casts = [
        'old_values'=>'array','new_values'=>'array'
    ];

    public function user() { return $this->belongsTo(User::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
}
