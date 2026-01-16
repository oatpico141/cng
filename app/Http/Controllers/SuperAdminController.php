<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

class SuperAdminController extends Controller
{
    /**
     * Show Super Admin Dashboard
     */
    public function index()
    {
        // Only allow admin username
        if (Auth::user()->username !== 'admin') {
            abort(403, 'ไม่มีสิทธิ์เข้าถึงหน้านี้');
        }

        return view('super-admin.index');
    }

    /**
     * Get all users (API)
     */
    public function getUsers()
    {
        $users = User::with(['role', 'branch'])
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($users);
    }

    /**
     * Get single user (API)
     */
    public function getUser($id)
    {
        $user = User::with(['role', 'branch'])->findOrFail($id);
        return response()->json($user);
    }

    /**
     * Create new user (API)
     */
    public function storeUser(Request $request)
    {
        $validated = $request->validate([
            'username' => 'required|string|unique:users,username',
            'password' => 'required|string|min:6',
            'name' => 'required|string',
            'email' => 'nullable|email',
            'role_id' => 'required|exists:roles,id',
            'branch_id' => 'required|exists:branches,id',
            'phone' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        $validated['password'] = Hash::make($validated['password']);

        $user = User::create($validated);

        return response()->json([
            'success' => true,
            'message' => 'สร้างผู้ใช้สำเร็จ',
            'user' => $user
        ]);
    }

    /**
     * Update user (API)
     */
    public function updateUser(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'username' => 'required|string|unique:users,username,' . $id,
            'password' => 'nullable|string|min:6',
            'name' => 'required|string',
            'email' => 'nullable|email',
            'role_id' => 'required|exists:roles,id',
            'branch_id' => 'required|exists:branches,id',
            'phone' => 'nullable|string',
            'is_active' => 'boolean',
        ]);

        if (!empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        $user->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'อัปเดตผู้ใช้สำเร็จ',
            'user' => $user
        ]);
    }

    /**
     * Delete user (API)
     */
    public function deleteUser($id)
    {
        $user = User::findOrFail($id);

        // Prevent deleting admin
        if ($user->username === 'admin') {
            return response()->json([
                'success' => false,
                'message' => 'ไม่สามารถลบ Admin ได้'
            ], 403);
        }

        $user->delete();

        return response()->json([
            'success' => true,
            'message' => 'ลบผู้ใช้สำเร็จ'
        ]);
    }
}
