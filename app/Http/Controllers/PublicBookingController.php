<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\Appointment;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PublicBookingController extends Controller
{
    /**
     * แสดงหน้าจองคิวสาธารณะ
     */
    public function index()
    {
        return view('booking.public');
    }

    /**
     * บันทึกการจองคิว
     * สร้าง Patient (is_temporary=true) และ Appointment
     */
    public function store(Request $request)
    {
        try {
            DB::beginTransaction();

            // Get default branch
            $branch = Branch::first();
            if (!$branch) {
                return response()->json([
                    'success' => false,
                    'message' => 'ไม่พบข้อมูลสาขา กรุณาติดต่อเจ้าหน้าที่'
                ], 500);
            }

            // Parse name
            $fullName = $request->fullName;
            $nameParts = explode(' ', $fullName, 2);
            $firstName = $nameParts[0] ?? $fullName;
            $lastName = $nameParts[1] ?? '';

            // Check if patient with this phone already exists
            $patient = Patient::where('phone', $request->phone)->first();

            if (!$patient) {
                // Create new temporary patient (Lead)
                $patient = Patient::create([
                    'name' => $fullName,
                    'first_name' => $firstName,
                    'last_name' => $lastName,
                    'phone' => $request->phone,
                    'is_temporary' => true, // ลูกค้าจอง (ยังไม่เคยรักษา)
                    'first_visit_branch_id' => $branch->id,
                    'branch_id' => $branch->id,
                    'booking_channel' => $this->mapLeadSource($request->source),
                    'chief_complaint' => $this->getSymptomText($request),
                ]);
            }

            // Create appointment
            $appointment = Appointment::create([
                'patient_id' => $patient->id,
                'branch_id' => $branch->id,
                'appointment_date' => $request->date,
                'appointment_time' => $request->time . ':00',
                'status' => 'pending',
                'booking_channel' => $this->mapLeadSource($request->source),
                'purpose' => $request->service,
                'notes' => $this->buildAppointmentNotes($request),
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'จองคิวสำเร็จ',
                'patient_id' => $patient->id,
                'appointment_id' => $appointment->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Map lead source to booking channel
     */
    private function mapLeadSource($source)
    {
        $map = [
            'Ads' => 'ads',
            'Facebook' => 'facebook',
            'Call' => 'phone',
            'Line' => 'line',
            'Co' => 'company',
            'Walk-in' => 'walk_in',
            'Referral' => 'referral',
        ];

        return $map[$source] ?? 'other';
    }

    /**
     * Get symptom text from request
     */
    private function getSymptomText($request)
    {
        if ($request->symptoms === 'อื่นๆ' && $request->customSymptoms) {
            return $request->customSymptoms;
        }

        return $request->symptoms ?? '';
    }

    /**
     * Build appointment notes
     */
    private function buildAppointmentNotes($request)
    {
        $notes = "ช่องทาง: {$request->source}";

        $symptom = $this->getSymptomText($request);
        if ($symptom) {
            $notes .= "\nอาการ: {$symptom}";
        }

        $notes .= "\nจองผ่านระบบออนไลน์";

        return $notes;
    }
}
