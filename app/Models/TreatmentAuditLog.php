<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class TreatmentAuditLog extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'treatment_id','action','field_name','old_value','new_value','changes','performed_by','reason','ip_address','user_agent'
    ];

    protected $casts = ['changes'=>'array'];

    public function treatment() { return $this->belongsTo(Treatment::class); }
    public function performedBy() { return $this->belongsTo(User::class, 'performed_by'); }
}
