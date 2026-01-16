<?php

namespace App\Http\Controllers;

use App\Models\CoursePackage;
use App\Models\Service;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CoursePackageController extends Controller
{
    public function index()
    {
        $packages = CoursePackage::with('service')
            ->orderBy('created_at', 'desc')
            ->paginate(15);

        $services = Service::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('course-packages.index', compact('packages', 'services'));
    }

    public function create()
    {
        $services = Service::where('is_active', true)
            ->orderBy('name')
            ->get();

        return view('course-packages.create', compact('services'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'paid_sessions' => 'required|integer|min:1',
            'bonus_sessions' => 'nullable|integer|min:0',
            'validity_days' => 'nullable|integer|min:1',
            'is_active' => 'nullable',
            'service_id' => 'required|exists:services,id',
            'commission_rate' => 'nullable|numeric|min:0',
            'commission_installment' => 'nullable|numeric|min:0',
            'per_session_commission_rate' => 'nullable|numeric|min:0',
        ]);

        // Map per_session_commission_rate to df_amount
        if (isset($validated['per_session_commission_rate'])) {
            $validated['df_amount'] = $validated['per_session_commission_rate'];
        }

        // Calculate total sessions from paid + bonus
        $validated['total_sessions'] = $validated['paid_sessions'] + ($validated['bonus_sessions'] ?? 0);

        $validated['is_active'] = $request->has('is_active');
        $validated['created_by'] = Auth::id();

        // Set unlimited validity (null or very large number)
        if (empty($validated['validity_days'])) {
            $validated['validity_days'] = 36500; // ~100 years = unlimited
        }

        CoursePackage::create($validated);

        return redirect()->route('course-packages.index')
            ->with('success', 'สร้างแพ็คเกจคอร์สสำเร็จ');
    }

    public function show($id)
    {
        // Redirect to index since we use modal for editing
        return redirect()->route('course-packages.index');
    }

    public function edit($id)
    {
        // Redirect to index since we use modal for editing
        return redirect()->route('course-packages.index');
    }

    public function update(Request $request, $id)
    {
        $package = CoursePackage::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'nullable|string|max:50',
            'description' => 'nullable|string',
            'price' => 'required|numeric|min:0',
            'paid_sessions' => 'required|integer|min:1',
            'bonus_sessions' => 'nullable|integer|min:0',
            'validity_days' => 'nullable|integer|min:1',
            'is_active' => 'nullable',
            'service_id' => 'required|exists:services,id',
            'commission_rate' => 'nullable|numeric|min:0',
            'commission_installment' => 'nullable|numeric|min:0',
            'per_session_commission_rate' => 'nullable|numeric|min:0',
        ]);

        // Map per_session_commission_rate to df_amount
        if (isset($validated['per_session_commission_rate'])) {
            $validated['df_amount'] = $validated['per_session_commission_rate'];
        }

        // Calculate total sessions from paid + bonus
        $validated['total_sessions'] = $validated['paid_sessions'] + ($validated['bonus_sessions'] ?? 0);

        $validated['is_active'] = $request->has('is_active');

        // Set unlimited validity (null or very large number)
        if (empty($validated['validity_days'])) {
            $validated['validity_days'] = 36500; // ~100 years = unlimited
        }

        $package->update($validated);

        return redirect()->route('course-packages.index')
            ->with('success', 'อัปเดตแพ็คเกจคอร์สสำเร็จ');
    }

    public function destroy($id)
    {
        $package = CoursePackage::findOrFail($id);
        $package->delete();

        return redirect()->route('course-packages.index')
            ->with('success', 'ลบแพ็คเกจคอร์สสำเร็จ');
    }
}
