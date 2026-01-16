<?php

namespace App\Http\Controllers;

use App\Models\{FollowUpList, Treatment};
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class FollowUpListController extends Controller
{
    /**
     * Display follow-up list
     */
    public function index(Request $request)
    {
        $date = $request->input('date', today());
        $branchId = $request->input('branch_id');

        $followUps = FollowUpList::where('follow_up_date', $date)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->with(['patient', 'treatment', 'pt'])
            ->orderBy('priority', 'desc')
            ->orderBy('created_at')
            ->get();

        return view('follow-up-lists.index', compact('followUps', 'date', 'branchId'));
    }

    /**
     * Auto-generate Follow-up List (ข้อ 30)
     * สร้างลิสต์คนไข้ที่ต้องโทรติดตามอาการ (จากคนที่มาเมื่อวาน)
     */
    public function autoGenerate(Request $request)
    {
        try {
            DB::beginTransaction();

            $targetDate = $request->input('target_date', yesterday());
            $branchId = $request->input('branch_id');

            // Find all treatments completed yesterday
            $treatments = Treatment::whereDate('completed_at', $targetDate)
                ->whereNotNull('completed_at')
                ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
                ->with(['patient', 'pt'])
                ->get();

            $created = 0;

            foreach ($treatments as $treatment) {
                // Check if already exists
                $existing = FollowUpList::where('treatment_id', $treatment->id)->first();

                if (!$existing) {
                    FollowUpList::create([
                        'patient_id' => $treatment->patient_id,
                        'treatment_id' => $treatment->id,
                        'branch_id' => $treatment->branch_id,
                        'pt_id' => $treatment->pt_id,
                        'follow_up_date' => today(), // โทรวันนี้สำหรับคนที่มาเมื่อวาน
                        'priority' => 'normal',
                        'notes' => 'Auto-generated follow-up for treatment on ' . $treatment->completed_at->toDateString(),
                        'status' => 'pending',
                    ]);
                    $created++;
                }
            }

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => "Generated {$created} follow-up items",
                'created' => $created,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to generate follow-up list: ' . $e->getMessage()
            ], 500);
        }
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show($id)
    {
        //
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        $followUp = FollowUpList::findOrFail($id);

        $validated = $request->validate([
            'status' => 'required|in:pending,contacted,completed',
            'contact_notes' => 'nullable|string',
        ]);

        $followUp->update([
            'status' => $validated['status'],
            'contact_notes' => $validated['contact_notes'] ?? null,
            'contacted_at' => $validated['status'] === 'contacted' ? now() : $followUp->contacted_at,
            'contacted_by' => $validated['status'] === 'contacted' ? auth()->id() : $followUp->contacted_by,
            'completed_at' => $validated['status'] === 'completed' ? now() : null,
        ]);

        return response()->json(['success' => true]);
    }

    public function destroy($id)
    {
        //
    }
}
