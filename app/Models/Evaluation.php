<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Evaluation extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'staff_id', 'evaluator_id', 'branch_id', 'evaluation_type', 'evaluation_date',
        'evaluation_period', 'ratings', 'overall_score', 'overall_rating', 'strengths',
        'areas_for_improvement', 'goals', 'action_plan', 'evaluator_comments',
        'staff_comments', 'next_evaluation_date', 'status', 'created_by',
    ];

    protected $casts = [
        'evaluation_date' => 'date',
        'ratings' => 'array',
        'overall_score' => 'decimal:1',
        'next_evaluation_date' => 'date',
    ];

    public function staff() { return $this->belongsTo(Staff::class); }
    public function evaluator() { return $this->belongsTo(User::class, 'evaluator_id'); }
    public function branch() { return $this->belongsTo(Branch::class); }
}
