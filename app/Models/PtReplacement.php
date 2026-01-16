<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class PtReplacement extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'original_pt_id', 'replacement_pt_id', 'appointment_id', 'treatment_id', 'branch_id',
        'replacement_date', 'reason', 'notes', 'commission_handling',
        'commission_split_percentage', 'created_by',
    ];

    protected $casts = [
        'replacement_date' => 'date',
        'commission_split_percentage' => 'decimal:2',
    ];

    public function originalPt() { return $this->belongsTo(User::class, 'original_pt_id'); }
    public function replacementPt() { return $this->belongsTo(User::class, 'replacement_pt_id'); }
    public function appointment() { return $this->belongsTo(Appointment::class); }
    public function treatment() { return $this->belongsTo(Treatment::class); }
    public function branch() { return $this->belongsTo(Branch::class); }
}
