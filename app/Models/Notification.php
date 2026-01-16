<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Notification extends Model
{
    use HasFactory, HasUuids, SoftDeletes;

    protected $fillable = [
        'user_id','notification_type','title','message','data','is_read','read_at','priority','channel','action_url','action_text','is_sent','sent_at','expires_at','created_by'
    ];

    protected $casts = [
        'data'=>'array','is_read'=>'boolean','read_at'=>'datetime','is_sent'=>'boolean','sent_at'=>'datetime','expires_at'=>'datetime'
    ];

    public function user() { return $this->belongsTo(User::class); }

    // Helper: Mark as read
    public function markAsRead()
    {
        $this->update(['is_read' => true, 'read_at' => now()]);
    }
}
