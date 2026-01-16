<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    use HasFactory, HasUuids;

    protected $fillable = [
        'module',
        'action',
        'description',
    ];

    // Relationships
    public function roles()
    {
        return $this->belongsToMany(Role::class, 'role_permissions')
            ->withPivot('id')
            ->withTimestamps()
            ->using(\App\Models\RolePermission::class);
    }

    // Helper method
    public static function findByModuleAction(string $module, string $action)
    {
        return static::where('module', $module)
            ->where('action', $action)
            ->first();
    }
}
