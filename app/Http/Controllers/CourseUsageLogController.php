<?php

namespace App\Http\Controllers;

use App\Models\CourseUsageLog;
use Illuminate\Http\Request;

class CourseUsageLogController extends Controller
{
    public function index()
    {
        //
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    /**
     * Display usage history for a specific course purchase
     *
     * @param string $id Course Purchase ID
     */
    public function show($id)
    {
        $coursePurchase = \App\Models\CoursePurchase::with(['package', 'patient'])->findOrFail($id);

        // Get usage logs with relations
        $usageLogs = CourseUsageLog::with(['treatment', 'patient', 'pt', 'branch'])
            ->where('course_purchase_id', $id)
            ->orderBy('usage_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        return view('course-usage-logs.show', compact('coursePurchase', 'usageLogs'));
    }

    /**
     * Get usage history as JSON for AJAX
     *
     * @param string $id Course Purchase ID
     */
    public function getUsageHistory($id)
    {
        $coursePurchase = \App\Models\CoursePurchase::with(['package', 'patient'])->findOrFail($id);

        // Get usage logs with relations
        $usageLogs = CourseUsageLog::with(['treatment', 'patient', 'pt', 'branch'])
            ->where('course_purchase_id', $id)
            ->orderBy('usage_date', 'desc')
            ->orderBy('created_at', 'desc')
            ->get();

        // Format data for JSON response
        $formattedLogs = $usageLogs->map(function ($log) {
            return [
                'date' => \Carbon\Carbon::parse($log->usage_date)->locale('th')->isoFormat('D MMM YYYY'),
                'time' => $log->created_at->format('H:i') . ' น.',
                'sessions' => $log->sessions_used,
                'used_by_patient_name' => $log->patient->name ?? 'N/A',
                'pt_name' => $log->pt->name ?? 'N/A',
                'branch_name' => $log->branch->name ?? 'N/A',
                'is_cross_branch' => $log->is_cross_branch,
                'notes' => $log->treatment && $log->treatment->treatment_notes
                    ? $log->treatment->treatment_notes
                    : '-',
            ];
        });

        return response()->json([
            'success' => true,
            'course_name' => $coursePurchase->package->name ?? 'คอร์ส',
            'used_sessions' => $coursePurchase->used_sessions,
            'total_sessions' => $coursePurchase->total_sessions,
            'remaining_sessions' => $coursePurchase->total_sessions - $coursePurchase->used_sessions,
            'usage_history' => $formattedLogs,
        ]);
    }

    public function edit($id)
    {
        //
    }

    public function update(Request $request, $id)
    {
        //
    }

    public function destroy($id)
    {
        //
    }
}
