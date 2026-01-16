<?php

namespace App\Http\Controllers;

use App\Models\PatientNote;
use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PatientNoteController extends Controller
{
    /**
     * Display a listing of notes for a patient
     *
     * Web Route: Returns view with paginated notes and search/filter functionality
     * API Route: Returns JSON for specific patient_id
     */
    public function index(Request $request)
    {
        // API Mode: If patient_id is provided, return JSON for AJAX requests
        if ($request->input('patient_id')) {
            $notes = PatientNote::where('patient_id', $request->input('patient_id'))
                ->with('createdBy')
                ->orderBy('created_at', 'desc')
                ->get();

            return response()->json($notes);
        }

        // Web Mode: Return view with all notes and search/filter
        $query = PatientNote::with(['patient', 'createdBy'])
            ->orderBy('created_at', 'desc');

        // Search by patient name, phone, or HN
        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('patient', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%")
                  ->orWhere('hn', 'like', "%{$search}%");
            });
        }

        // Filter by note type
        if ($request->filled('note_type')) {
            $query->where('note_type', $request->note_type);
        }

        // Filter by important status
        if ($request->filled('is_important')) {
            $query->where('is_important', $request->is_important);
        }

        $notes = $query->paginate(20)->withQueryString();

        return view('patient-notes.index', compact('notes'));
    }

    /**
     * Store a newly created note
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'patient_id' => 'required|exists:patients,id',
            'note' => 'required|string',
            'note_type' => 'nullable|string|max:50',
            'is_important' => 'nullable|boolean',
        ], [
            'patient_id.required' => 'กรุณาระบุคนไข้',
            'patient_id.exists' => 'ไม่พบคนไข้ในระบบ',
            'note.required' => 'กรุณากรอกโน้ต',
        ]);

        try {
            $note = PatientNote::create([
                'patient_id' => $validated['patient_id'],
                'note' => $validated['note'],
                'note_type' => $validated['note_type'] ?? 'general',
                'is_important' => $validated['is_important'] ?? false,
                'created_by' => Auth::id() ?? null,
            ]);

            // Load the relationship for response
            $note->load('createdBy');

            return redirect()
                ->back()
                ->with('success', 'บันทึกโน้ตเรียบร้อย');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'ไม่สามารถบันทึกโน้ตได้: ' . $e->getMessage());
        }
    }

    /**
     * Display the specified note
     */
    public function show($id)
    {
        $note = PatientNote::with(['patient', 'createdBy'])->findOrFail($id);
        return response()->json($note);
    }

    /**
     * Update the specified note
     */
    public function update(Request $request, $id)
    {
        $note = PatientNote::findOrFail($id);

        $validated = $request->validate([
            'note' => 'required|string',
            'note_type' => 'nullable|string|max:50',
            'is_important' => 'nullable|boolean',
        ], [
            'note.required' => 'กรุณากรอกโน้ต',
        ]);

        try {
            $note->update([
                'note' => $validated['note'],
                'note_type' => $validated['note_type'] ?? $note->note_type,
                'is_important' => $validated['is_important'] ?? $note->is_important,
            ]);

            return redirect()
                ->back()
                ->with('success', 'แก้ไขโน้ตเรียบร้อย');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->withInput()
                ->with('error', 'ไม่สามารถแก้ไขโน้ตได้: ' . $e->getMessage());
        }
    }

    /**
     * Remove the specified note (soft delete)
     */
    public function destroy($id)
    {
        try {
            $note = PatientNote::findOrFail($id);
            $note->delete(); // Soft delete

            return redirect()
                ->back()
                ->with('success', 'ลบโน้ตเรียบร้อย');

        } catch (\Exception $e) {
            return redirect()
                ->back()
                ->with('error', 'ไม่สามารถลบโน้ตได้: ' . $e->getMessage());
        }
    }
}
