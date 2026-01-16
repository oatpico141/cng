<?php

namespace App\Http\Controllers;

use App\Models\Role;
use App\Models\Permission;
use Illuminate\Http\Request;

class RoleController extends Controller
{
    public function index()
    {
        $roles = Role::withCount('users')->paginate(10);
        return view('roles.index', compact('roles'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name',
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
        ]);

        $role = Role::create([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
            'is_system' => false,
        ]);

        // Sync permissions
        if (!empty($validated['permissions'])) {
            $permissionIds = [];
            foreach ($validated['permissions'] as $module) {
                $permission = Permission::firstOrCreate(
                    ['module' => $module, 'action' => 'access'],
                    ['description' => 'Access to ' . $module]
                );
                $permissionIds[] = $permission->id;
            }
            $role->permissions()->sync($permissionIds);
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'เพิ่ม Role สำเร็จ',
                'role' => $role->load('permissions')
            ]);
        }

        return redirect()->route('roles.index')->with('success', 'เพิ่ม Role สำเร็จ');
    }

    public function edit($id)
    {
        $role = Role::with('permissions')->findOrFail($id);

        // Get permission modules for this role
        $rolePermissions = $role->permissions->pluck('module')->toArray();

        return response()->json([
            'id' => $role->id,
            'name' => $role->name,
            'description' => $role->description,
            'is_system' => $role->is_system,
            'permissions' => $rolePermissions,
        ]);
    }

    public function update(Request $request, $id)
    {
        $role = Role::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255|unique:roles,name,' . $id,
            'description' => 'nullable|string|max:500',
            'permissions' => 'nullable|array',
        ]);

        $role->update([
            'name' => $validated['name'],
            'description' => $validated['description'] ?? null,
        ]);

        // Sync permissions
        $permissionIds = [];
        if (!empty($validated['permissions'])) {
            foreach ($validated['permissions'] as $module) {
                $permission = Permission::firstOrCreate(
                    ['module' => $module, 'action' => 'access'],
                    ['description' => 'Access to ' . $module]
                );
                $permissionIds[] = $permission->id;
            }
        }
        $role->permissions()->sync($permissionIds);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'แก้ไข Role สำเร็จ',
                'role' => $role->load('permissions')
            ]);
        }

        return redirect()->route('roles.index')->with('success', 'แก้ไข Role สำเร็จ');
    }

    public function destroy($id)
    {
        $role = Role::withCount('users')->findOrFail($id);

        // Prevent deleting roles with users
        if ($role->users_count > 0) {
            if (request()->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่สามารถลบ Role ที่มีผู้ใช้อยู่ได้'
                ], 400);
            }
            return back()->with('error', 'ไม่สามารถลบ Role ที่มีผู้ใช้อยู่ได้');
        }

        // Detach all permissions before deleting
        $role->permissions()->detach();
        $role->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'ลบ Role สำเร็จ'
            ]);
        }

        return redirect()->route('roles.index')->with('success', 'ลบ Role สำเร็จ');
    }
}
