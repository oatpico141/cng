<?php

namespace App\Http\Controllers;

use App\Models\{Equipment, MaintenanceLog, Branch};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EquipmentController extends Controller
{
    /**
     * Display equipment list (ข้อ 25)
     */
    public function index(Request $request)
    {
        $query = Equipment::with(['branch']);

        if ($request->branch_id) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->search) {
            $query->where(function($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('equipment_code', 'like', '%' . $request->search . '%');
            });
        }

        // Filter maintenance due
        if ($request->maintenance_due) {
            $query->whereNotNull('next_maintenance_date')
                  ->where('next_maintenance_date', '<=', now()->addDays(7));
        }

        $equipment = $query->orderBy('name')->paginate(20);
        $branches = Branch::all();
        $categories = Equipment::distinct()->pluck('category')->filter();

        // Stats
        $totalEquipment = Equipment::count();
        $activeCount = Equipment::where('status', 'available')->count();
        $maintenanceDue = Equipment::whereNotNull('next_maintenance_date')
            ->where('next_maintenance_date', '<=', now()->addDays(7))
            ->count();
        $totalValue = Equipment::sum('current_value') ?? 0;

        return view('equipment.index', compact(
            'equipment', 'branches', 'categories',
            'totalEquipment', 'activeCount', 'maintenanceDue', 'totalValue'
        ));
    }

    /**
     * Show form for creating new equipment
     */
    public function create()
    {
        $branches = Branch::all();
        return view('equipment.create', compact('branches'));
    }

    /**
     * Store new equipment (ข้อ 25 - Register Equipment)
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'equipment_code' => 'required|string|max:50|unique:equipment,equipment_code',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:treatment_equipment,office_equipment,furniture',
            'branch_id' => 'required|exists:branches,id',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:100',
            'warranty_number' => 'nullable|string|max:100',
            'warranty_expiry' => 'nullable|date',
            'maintenance_interval_days' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        try {
            $validated['status'] = 'available';
            $validated['created_by'] = auth()->id() ?? null;
            $validated['current_value'] = $validated['purchase_price'] ?? null;

            // Calculate next maintenance date if interval is set
            if (!empty($validated['maintenance_interval_days']) && !empty($validated['purchase_date'])) {
                $validated['next_maintenance_date'] = date('Y-m-d', strtotime($validated['purchase_date'] . ' + ' . $validated['maintenance_interval_days'] . ' days'));
            }

            $equipment = Equipment::create($validated);

            return response()->json([
                'success' => true,
                'message' => 'Equipment registered successfully',
                'equipment' => $equipment,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to register equipment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Display equipment details
     */
    public function show($id)
    {
        $equipment = Equipment::with(['branch', 'maintenanceLogs'])->findOrFail($id);
        return view('equipment.show', compact('equipment'));
    }

    /**
     * Show form for editing equipment
     */
    public function edit($id)
    {
        $equipment = Equipment::findOrFail($id);
        $branches = Branch::all();
        return view('equipment.edit', compact('equipment', 'branches'));
    }

    /**
     * Update equipment
     */
    public function update(Request $request, $id)
    {
        $equipment = Equipment::findOrFail($id);

        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'category' => 'required|in:treatment_equipment,office_equipment,furniture',
            'branch_id' => 'required|exists:branches,id',
            'status' => 'required|in:available,in_use,maintenance,retired',
            'purchase_date' => 'nullable|date',
            'purchase_price' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'serial_number' => 'nullable|string|max:100',
            'warranty_number' => 'nullable|string|max:100',
            'warranty_expiry' => 'nullable|date',
            'maintenance_interval_days' => 'nullable|integer|min:1',
            'notes' => 'nullable|string',
        ]);

        try {
            $equipment->update($validated);

            return response()->json([
                'success' => true,
                'message' => 'Equipment updated successfully',
                'equipment' => $equipment,
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to update equipment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Delete equipment
     */
    public function destroy($id)
    {
        try {
            $equipment = Equipment::findOrFail($id);
            $equipment->delete();

            return response()->json([
                'success' => true,
                'message' => 'Equipment deleted successfully',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Failed to delete equipment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Record maintenance (ข้อ 25 - Maintenance Tracking)
     */
    public function recordMaintenance(Request $request, $equipmentId)
    {
        $validated = $request->validate([
            'maintenance_type' => 'required|in:preventive,corrective,emergency,inspection',
            'maintenance_date' => 'required|date',
            'description' => 'nullable|string',
            'work_performed' => 'nullable|string',
            'performed_by' => 'nullable|string|max:255',
            'service_provider' => 'nullable|string|max:255',
            'cost' => 'nullable|numeric|min:0',
            'parts_used' => 'nullable|array',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            $equipment = Equipment::findOrFail($equipmentId);

            // Generate maintenance number
            $maintenanceNumber = 'MNT-' . now()->format('Ymd') . '-' . rand(1000, 9999);

            // Create maintenance log
            $maintenance = MaintenanceLog::create([
                'maintenance_number' => $maintenanceNumber,
                'equipment_id' => $equipment->id,
                'branch_id' => $equipment->branch_id,
                'maintenance_type' => $validated['maintenance_type'],
                'maintenance_date' => $validated['maintenance_date'],
                'description' => $validated['description'] ?? null,
                'work_performed' => $validated['work_performed'] ?? null,
                'performed_by' => $validated['performed_by'] ?? null,
                'service_provider' => $validated['service_provider'] ?? null,
                'cost' => $validated['cost'] ?? null,
                'status' => 'completed',
                'parts_used' => $validated['parts_used'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id() ?? null,
            ]);

            // Update equipment's last maintenance date
            $updateData = [
                'last_maintenance_date' => $validated['maintenance_date'],
            ];

            // Calculate next maintenance date if interval is set
            if ($equipment->maintenance_interval_days) {
                $nextDate = date('Y-m-d', strtotime($validated['maintenance_date'] . ' + ' . $equipment->maintenance_interval_days . ' days'));
                $updateData['next_maintenance_date'] = $nextDate;
                $maintenance->next_maintenance_date = $nextDate;
                $maintenance->save();
            }

            $equipment->update($updateData);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Maintenance recorded successfully',
                'maintenance' => $maintenance,
                'next_maintenance_date' => $equipment->next_maintenance_date,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to record maintenance: ' . $e->getMessage()
            ], 500);
        }
    }
}
