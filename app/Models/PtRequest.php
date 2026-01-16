<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PtRequest extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'appointment_id','patient_id','branch_id','original_pt_id','requested_pt_id','status','reason','rejection_reason','requested_at','processed_at','processed_by','created_by'
    ];

    protected $casts = ['requested_at'=>'datetime','processed_at'=>'datetime'];

    public function appointment() { return $this->belongsTo(Appointment::class); }
    public function patient() { return $this->belongsTo(Patient::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function originalPt() { return $this->belongsTo(User::class, 'original_pt_id'); }
    public function requestedPt() { return $this->belongsTo(User::class, 'requested_pt_id'); }
    public function processedBy() { return $this->belongsTo(User::class, 'processed_by'); }
}
