<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Role;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rule;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('selected_branch_id');

        $users = User::with(['role', 'branch'])
            ->when($branchId, function ($query, $branchId) {
                // Filter users by selected branch
                $query->where('branch_id', $branchId);
            })
            ->when($request->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('name', 'like', "%{$search}%")
                      ->orWhere('username', 'like', "%{$search}%")
                      ->orWhere('email', 'like', "%{$search}%");
                });
            })
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $roles = Role::all();
        $branches = Branch::where('is_active', true)->get();

        return view('users.index', compact('users', 'roles', 'branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => 'required|string|max:255|unique:users,username',
            'email' => 'required|email|max:255|unique:users,email',
            'password' => 'required|string|min:6',
            'role_id' => 'required|exists:roles,id',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $validated['password'] = Hash::make($validated['password']);
        $validated['is_active'] = $request->boolean('is_active');

        $user = User::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'เพิ่มผู้ใช้สำเร็จ'
            ]);
        }

        return redirect()->route('users.index')->with('success', 'เพิ่มผู้ใช้สำเร็จ');
    }

    public function edit($id)
    {
        $user = User::with(['role', 'branch'])->findOrFail($id);

        if (request()->ajax()) {
            return response()->json($user);
        }

        return view('users.edit', compact('user'));
    }

    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'username' => ['required', 'string', 'max:255', Rule::unique('users')->ignore($id)],
            'email' => ['required', 'email', 'max:255', Rule::unique('users')->ignore($id)],
            'role_id' => 'required|exists:roles,id',
            'branch_id' => 'nullable|exists:branches,id',
        ]);

        $validated['is_active'] = $request->boolean('is_active');

        // Update password only if provided
        if ($request->filled('password')) {
            $request->validate(['password' => 'string|min:6']);
            $validated['password'] = Hash::make($request->password);
        }

        $user->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'อัปเดตผู้ใช้สำเร็จ'
            ]);
        }

        return redirect()->route('users.index')->with('success', 'อัปเดตผู้ใช้สำเร็จ');
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'ลบผู้ใช้สำเร็จ'
            ]);
        }

        return redirect()->route('users.index')->with('success', 'ลบผู้ใช้สำเร็จ');
    }
}
