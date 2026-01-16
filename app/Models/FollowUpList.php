<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class FollowUpList extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'patient_id','treatment_id','branch_id','pt_id','follow_up_date','priority','notes','status','contacted_at','contacted_by','contact_notes','appointment_id','completed_at','created_by'
    ];

    protected $casts = ['follow_up_date'=>'date','contacted_at'=>'datetime','completed_at'=>'datetime'];

    public function patient() { return $this->belongsTo(Patient::class); }
    public function treatment() { return $this->belongsTo(Treatment::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
    public function pt() { return $this->belongsTo(User::class, 'pt_id'); }
    public function contactedBy() { return $this->belongsTo(User::class, 'contacted_by'); }
    public function appointment() { return $this->belongsTo(Appointment::class); }
}
