<?php

namespace App\Http\Controllers;

use App\Models\Schedule;
use App\Models\User;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ScheduleController extends Controller
{
    public function index(Request $request)
    {
        $branchId = session('selected_branch_id');

        // Get PTs for this branch
        $pts = User::with('role')
            ->whereHas('role', fn($q) => $q->where('name', 'PT'))
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->where('is_active', true)
            ->get();

        // Get schedules - use staff_id which links to users table
        $query = Schedule::with(['branch']);

        if ($branchId) {
            $query->where('branch_id', $branchId);
        }

        // Filter by date range (default: current week)
        $startDate = $request->start_date ?? now()->startOfWeek()->toDateString();
        $endDate = $request->end_date ?? now()->endOfWeek()->toDateString();
        $query->whereBetween('schedule_date', [$startDate, $endDate]);

        // Filter by PT
        if ($request->filled('pt_id')) {
            $query->where('staff_id', $request->pt_id);
        }

        $schedules = $query->orderBy('schedule_date')->orderBy('start_time')->get();

        // Attach PT info manually (staff_id points to users table)
        $ptMap = $pts->keyBy('id');
        foreach ($schedules as $schedule) {
            $schedule->pt = $ptMap->get($schedule->staff_id);
        }

        // Group by date for calendar view
        $schedulesByDate = $schedules->groupBy(fn($s) => $s->schedule_date->format('Y-m-d'));

        // Generate dates for the week
        $dates = [];
        $current = \Carbon\Carbon::parse($startDate);
        $end = \Carbon\Carbon::parse($endDate);
        while ($current <= $end) {
            $dates[] = $current->copy();
            $current->addDay();
        }

        $branches = Branch::where('is_active', true)->get();

        return view('schedules.index', compact(
            'schedules',
            'schedulesByDate',
            'pts',
            'branches',
            'branchId',
            'startDate',
            'endDate',
            'dates'
        ));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'staff_id' => 'required|exists:users,id',
            'schedule_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'schedule_type' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_available' => 'nullable',
        ]);

        $validated['branch_id'] = session('selected_branch_id') ?? Auth::user()->branch_id;
        $validated['is_available'] = $request->has('is_available');
        $validated['status'] = 'confirmed';
        $validated['created_by'] = Auth::id();

        Schedule::create($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'บันทึกตารางงานเรียบร้อย']);
        }

        return redirect()->route('schedules.index')->with('success', 'บันทึกตารางงานเรียบร้อย');
    }

    public function show($id)
    {
        $schedule = Schedule::with(['branch'])->findOrFail($id);
        $schedule->pt = User::find($schedule->staff_id);

        if (request()->ajax()) {
            return response()->json($schedule);
        }

        return view('schedules.show', compact('schedule'));
    }

    public function edit($id)
    {
        $schedule = Schedule::findOrFail($id);

        if (request()->ajax()) {
            return response()->json($schedule);
        }

        $pts = User::whereHas('role', fn($q) => $q->where('name', 'PT'))->get();
        return view('schedules.edit', compact('schedule', 'pts'));
    }

    public function update(Request $request, $id)
    {
        $schedule = Schedule::findOrFail($id);

        $validated = $request->validate([
            'staff_id' => 'required|exists:users,id',
            'schedule_date' => 'required|date',
            'start_time' => 'required',
            'end_time' => 'required|after:start_time',
            'schedule_type' => 'nullable|string',
            'notes' => 'nullable|string',
            'is_available' => 'nullable',
        ]);

        $validated['is_available'] = $request->has('is_available');

        $schedule->update($validated);

        if ($request->ajax()) {
            return response()->json(['success' => true, 'message' => 'อัปเดตตารางงานเรียบร้อย']);
        }

        return redirect()->route('schedules.index')->with('success', 'อัปเดตตารางงานเรียบร้อย');
    }

    public function destroy($id)
    {
        $schedule = Schedule::findOrFail($id);
        $schedule->delete();

        if (request()->ajax()) {
            return response()->json(['success' => true, 'message' => 'ลบตารางงานเรียบร้อย']);
        }

        return redirect()->route('schedules.index')->with('success', 'ลบตารางงานเรียบร้อย');
    }

    /**
     * Get PT availability for a specific date (for appointment booking)
     */
    public function getAvailability(Request $request)
    {
        $date = $request->date ?? now()->toDateString();
        $branchId = $request->branch_id ?? session('selected_branch_id');

        $schedules = Schedule::where('schedule_date', $date)
            ->where('is_available', true)
            ->when($branchId, fn($q) => $q->where('branch_id', $branchId))
            ->get();

        // Attach PT info
        $ptIds = $schedules->pluck('staff_id')->unique();
        $pts = User::whereIn('id', $ptIds)->get()->keyBy('id');

        foreach ($schedules as $schedule) {
            $schedule->pt = $pts->get($schedule->staff_id);
        }

        return response()->json($schedules);
    }
}