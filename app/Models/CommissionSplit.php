<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class CommissionSplit extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'commission_id','pt_id','split_percentage','split_amount','split_reason','status','paid_at','paid_by','clawed_back_at','clawed_back_by','notes','created_by'
    ];

    protected $casts = [
        'split_percentage'=>'decimal:2','split_amount'=>'decimal:2','paid_at'=>'datetime','clawed_back_at'=>'datetime'
    ];

    public function commission() { return $this->belongsTo(Commission::class); }
    public function pt() { return $this->belongsTo(User::class, 'pt_id'); }
}
