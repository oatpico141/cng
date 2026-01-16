<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    public function index()
    {
        $branches = Branch::orderBy('created_at', 'desc')->paginate(15);
        return view('branches.index', compact('branches'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:branches,code',
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        Branch::create($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'เพิ่มสาขาเรียบร้อยแล้ว'
            ]);
        }

        return redirect()->route('branches.index')->with('success', 'เพิ่มสาขาเรียบร้อยแล้ว');
    }

    public function edit($id)
    {
        $branch = Branch::findOrFail($id);

        if (request()->ajax()) {
            return response()->json($branch);
        }

        return view('branches.edit', compact('branch'));
    }

    public function update(Request $request, $id)
    {
        $branch = Branch::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50|unique:branches,code,' . $id,
            'address' => 'nullable|string|max:500',
            'phone' => 'nullable|string|max:50',
            'email' => 'nullable|email|max:255',
            'is_active' => 'boolean',
        ]);

        $validated['is_active'] = $request->has('is_active');

        $branch->update($validated);

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'อัปเดตสาขาเรียบร้อยแล้ว'
            ]);
        }

        return redirect()->route('branches.index')->with('success', 'อัปเดตสาขาเรียบร้อยแล้ว');
    }

    public function destroy($id)
    {
        $branch = Branch::findOrFail($id);
        $branch->delete();

        if (request()->ajax()) {
            return response()->json([
                'success' => true,
                'message' => 'ลบสาขาเรียบร้อยแล้ว'
            ]);
        }

        return redirect()->route('branches.index')->with('success', 'ลบสาขาเรียบร้อยแล้ว');
    }
}
