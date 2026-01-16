<?php

namespace App\Http\Controllers;

use App\Models\Service;
use App\Models\ServiceCategory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ServiceController extends Controller
{
    public function index()
    {
        $branchId = session('selected_branch_id');

        $query = Service::with('serviceCategory');

        // Filter by branch if selected
        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        $services = $query->orderBy('name')->paginate(15);
        $categories = ServiceCategory::active()->ordered()->get();
        return view('services.index', compact('services', 'categories'));
    }

    public function create()
    {
        return view('services.create');
    }

    public function store(Request $request)
    {
        try {
            $validated = $request->validate([
                'name' => 'required|string|max:255',
                'code' => 'nullable|string|max:50',
                'description' => 'nullable|string',
                'category_id' => 'required|exists:service_categories,id',
                'default_price' => 'required|numeric|min:0',
                'default_duration_minutes' => 'nullable|integer|min:1',
                'is_active' => 'nullable',
                'is_package' => 'nullable',
                'package_sessions' => 'nullable|integer|min:1',
                'package_validity_days' => 'nullable|integer|min:1',
                'default_commission_rate' => 'nullable|numeric|min:0|max:100',
                'default_df_rate' => 'nullable|numeric|min:0',
            ]);

            // Handle is_active - check for various truthy values from AJAX
            $validated['is_active'] = $request->has('is_active') &&
                                      in_array($request->input('is_active'), ['on', '1', 'true', true, 1], true);
            $validated['is_package'] = $request->has('is_package') &&
                                       in_array($request->input('is_package'), ['on', '1', 'true', true, 1], true);
            $validated['created_by'] = Auth::id();

            // Get category name from service_categories table
            $category = ServiceCategory::find($validated['category_id']);
            $validated['category'] = $category ? $category->name : '';

            // Set branch_id from session or user's branch
            $validated['branch_id'] = session('selected_branch_id') ?? Auth::user()->branch_id;

            Service::create($validated);

            if ($request->ajax()) {
                return response()->json(['success' => true, 'message' => 'เพิ่มบริการเรียบร้อยแล้ว']);
            }

            return redirect()->route('services.index')->with('success', 'เพิ่มบริการเรียบร้อยแล้ว');
        } catch (\Illuminate\Database\QueryException $e) {
            \Log::error('Service store error: ' . $e->getMessage());

            // Check for duplicate entry error
            $message = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
            if (str_contains($e->getMessage(), 'Duplicate entry') && str_contains($e->getMessage(), 'code')) {
                $message = 'รหัสบริการนี้มีอยู่แล้ว กรุณาใช้รหัสอื่น';
            }

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 422);
            }

            return redirect()->back()->withInput()->with('error', $message);
        } catch (\Exception $e) {
            \Log::error('Service store error: ' . $e->getMessage());

            if ($request->ajax()) {
                return response()->json([
                    'success' => false,
                    'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
                ], 500);
            }

            return redirect()->back()->withInput()->with('error', 'เกิดข้อผิดพลาด: ' . $e->getMessage());
        }
    }

    public function show($id)
    {
        $service = Service::findOrFail($id);
        return response()->json($service);
    }

    public function edit($id)
    {
        $service = Service::findOrFail($id);

        if (request()->ajax()) {
            return response()->json($service);
        }

        return view('services.edit', compact('service'));
    }

    public function update(Request $request, $id)
    {
        $service = Service::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'category_id' => 'required|exists:service_categories,id',
            'default_price' => 'required|numeric|min:0',
            'default_duration_minutes' => 'nullable|integer|min:1',
            'is_active' => 'nullable',
            'is_package' => 'nullable',
            'package_sessions' => 'nullable|integer|min:1',
            'package_validity_days' => 'nullable|integer|min:1',
            'default_commission_rate' => 'nullable|numeric|min:0|max:100',
            'default_df_rate' => 'nullable|numeric|min:0',
        ]);

        $validated['is_active'] = $request->has('is_active');
        $validated['is_package'] = $request->has('is_package');

        // Get category name from service_categories table
        $category = ServiceCategory::find($validated['category_id']);
        $validated['category'] = $category ? $category->name : '';

        $service->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'แก้ไขบริการเรียบร้อยแล้ว']);
        }

        return redirect()->route('services.index')->with('success', 'แก้ไขบริการเรียบร้อยแล้ว');
    }

    public function destroy($id)
    {
        $service = Service::findOrFail($id);

        // Mark code as deleted to allow reuse (add suffix)
        if ($service->code) {
            $service->code = $service->code . '_DEL_' . time();
            $service->save();
        }

        $service->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'ลบบริการเรียบร้อยแล้ว']);
        }

        return redirect()->route('services.index')->with('success', 'ลบบริการเรียบร้อยแล้ว');
    }
}
