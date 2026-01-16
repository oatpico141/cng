<?php

namespace App\Http\Controllers;

use App\Models\Refund;
use App\Models\CoursePurchase;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class RefundController extends Controller
{
    /**
     * Cancel course and create refund
     */
    public function cancelCourse(Request $request)
    {
        $validated = $request->validate([
            'course_purchase_id' => 'required|string',
            'reason' => 'required|string|min:5',
        ]);

        try {
            DB::beginTransaction();

            // Find course purchase
            $coursePurchase = CoursePurchase::with(['package', 'patient', 'invoice'])
                ->findOrFail($validated['course_purchase_id']);

            // Calculate refund amount based on PAID sessions and ACTUAL paid amount
            // Example: Buy 5 + Bonus 1 = 6 total, Price 6000 = 1200/session
            // If installment: paid 2/3 = 4000, used 2 sessions = 2400 used, refund = 4000 - 2400 = 1600

            $paidSessions = $coursePurchase->package->paid_sessions ?? $coursePurchase->total_sessions ?? 0;
            $usedSessions = $coursePurchase->used_sessions ?? 0;
            $packagePrice = $coursePurchase->package->price ?? 0;

            // Price per session based on paid sessions only
            $pricePerSession = $paidSessions > 0 ? $packagePrice / $paidSessions : 0;

            // Calculate actual paid amount (for installment customers)
            $actualPaidAmount = $packagePrice; // Default: full price
            if ($coursePurchase->payment_type === 'installment') {
                $installmentPaid = $coursePurchase->installment_paid ?? 0;
                $installmentAmount = $coursePurchase->installment_amount ?? 0;
                $actualPaidAmount = $installmentPaid * $installmentAmount;
            }

            // Used amount = sessions used * price per session
            $usedAmount = $usedSessions * $pricePerSession;

            // Refund = actual paid - used amount (minimum 0)
            $refundAmount = max(0, $actualPaidAmount - $usedAmount);

            // Generate refund number
            $refundNumber = 'REF-' . now()->format('Ymd') . '-' . str_pad(Refund::whereDate('created_at', today())->count() + 1, 4, '0', STR_PAD_LEFT);

            // Create refund record
            $refund = Refund::create([
                'refund_number' => $refundNumber,
                'invoice_id' => $coursePurchase->invoice_id,
                'patient_id' => $coursePurchase->patient_id,
                'branch_id' => session('selected_branch_id') ?? Auth::user()->branch_id ?? null,
                'refund_type' => 'course_cancellation',
                'refund_amount' => $refundAmount,
                'status' => 'completed',
                'refund_date' => now(),
                'original_amount' => $actualPaidAmount,
                'used_amount' => $usedAmount,
                'penalty_amount' => 0,
                'calculation_notes' => "จ่ายจริง " . number_format($actualPaidAmount, 2) . " บาท | ใช้ {$usedSessions} ครั้ง × " . number_format($pricePerSession, 2) . " = " . number_format($usedAmount, 2) . " บาท | คืน " . number_format($refundAmount, 2) . " บาท",
                'reason' => $validated['reason'],
                'approved_at' => now(),
                'approved_by' => Auth::id(),
                'created_by' => Auth::id(),
            ]);

            // Create audit log
            AuditLog::create([
                'user_id' => Auth::id(),
                'action' => 'cancel_course',
                'module' => 'refunds',
                'model_type' => 'App\Models\CoursePurchase',
                'model_id' => $coursePurchase->id,
                'old_values' => $coursePurchase->toArray(),
                'new_values' => ['status' => 'cancelled', 'refund_id' => $refund->id],
                'ip_address' => $request->ip(),
                'user_agent' => $request->userAgent(),
                'url' => $request->fullUrl(),
                'method' => $request->method(),
                'description' => 'ยกเลิกคอร์ส: ' . ($coursePurchase->package->name ?? 'N/A') .
                                ' | คืนเงิน: ' . number_format($refundAmount, 2) . ' บาท' .
                                ' | เหตุผล: ' . $validated['reason'],
                'branch_id' => session('selected_branch_id') ?? Auth::user()->branch_id ?? null,
            ]);

            // Soft delete course purchase
            $coursePurchase->delete();

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'ยกเลิกคอร์สสำเร็จ',
                'refund_number' => $refundNumber,
                'refund_amount' => $refundAmount,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'เกิดข้อผิดพลาด: ' . $e->getMessage()
            ], 500);
        }
    }

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
        //
    }

    public function destroy($id)
    {
        //
    }
}
