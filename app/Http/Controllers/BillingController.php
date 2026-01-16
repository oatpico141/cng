<?php

namespace App\Http\Controllers;

use App\Models\{
    Queue, Invoice, InvoiceItem, Payment, CoursePurchase, CoursePackage,
    CourseUsageLog, Commission, DfPayment, Refund, Service, Treatment, Patient
};
use App\Services\RevenueAdjustmentService;
use App\Services\DfService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class BillingController extends Controller
{
    /**
     * Display billing page with patients waiting for payment
     * ข้อ 5: Billing UI
     *
     * Quick Course Purchase Feature: Pre-load patient data when coming from patient profile
     * URL: /billing?patient_id={uuid}
     */
    public function index(Request $request)
    {
        // Get patients who completed treatment but haven't paid
        $waitingQueues = Queue::with(['patient', 'appointment', 'pt', 'branch'])
            ->where('status', 'completed')
            ->whereDate('queued_at', today())
            ->orderBy('queue_number')
            ->get();

        $services = Service::where('is_active', true)->get();
        $packages = CoursePackage::where('is_active', true)->get();

        // Quick Course Purchase: Pre-load patient if patient_id is provided
        $preloadPatient = null;
        if ($request->has('patient_id')) {
            $preloadPatient = Patient::find($request->patient_id);
        }

        return view('billing.index', compact('waitingQueues', 'services', 'packages', 'preloadPatient'));
    }

    /**
     * Process payment and generate invoice/receipt
     * ข้อ 5: รับเงิน + ออกใบเสร็จ
     */
    public function processPayment(Request $request)
    {
        $validated = $request->validate([
            'queue_id' => 'required|exists:queues,id',
            'patient_id' => 'required|exists:patients,id',
            'opd_id' => 'nullable|exists:opd_records,id',
            'items' => 'required|array|min:1',
            'subtotal' => 'required|numeric|min:0',
            'discount' => 'nullable|numeric|min:0',
            'total' => 'required|numeric|min:0',
            'payment_method' => 'required|in:cash,card,bank_transfer,qr_code',
            'amount_paid' => 'required|numeric|min:0',
            'card_last_4' => 'nullable|string|max:4',
            'reference_number' => 'nullable|string',
            'notes' => 'nullable|string',
        ]);

        try {
            DB::beginTransaction();

            // Create Invoice
            $invoiceNumber = 'INV-' . now()->format('Ymd') . '-' . rand(10000, 99999);

            $invoice = Invoice::create([
                'invoice_number' => $invoiceNumber,
                'patient_id' => $validated['patient_id'],
                'opd_id' => $validated['opd_id'],
                'branch_id' => auth()->user()->branch_id ?? Queue::find($validated['queue_id'])->branch_id,
                'invoice_type' => 'walk_in',
                'subtotal' => $validated['subtotal'],
                'discount_amount' => $validated['discount'] ?? 0,
                'tax_amount' => 0,
                'total_amount' => $validated['total'],
                'paid_amount' => $validated['amount_paid'],
                'outstanding_amount' => max(0, $validated['total'] - $validated['amount_paid']),
                'status' => $validated['amount_paid'] >= $validated['total'] ? 'paid' : 'partial',
                'invoice_date' => today(),
                'created_by' => auth()->id() ?? null,
            ]);

            // Create Invoice Items & Handle Course Purchases
            foreach ($validated['items'] as $item) {
                $invoiceItem = InvoiceItem::create([
                    'invoice_id' => $invoice->id,
                    'service_id' => $item['type'] === 'service' ? $item['id'] : null,
                    'package_id' => $item['type'] === 'course' ? $item['id'] : null,
                    'item_type' => $item['type'],
                    'description' => $item['name'],
                    'quantity' => $item['quantity'],
                    'unit_price' => $item['price'],
                    'discount_amount' => 0,
                    'total_amount' => $item['total'],
                ]);

                // If it's a course purchase, call sellCourse
                if ($item['type'] === 'course') {
                    $this->sellCourse(
                        $validated['patient_id'],
                        $item['id'],
                        $invoice->id,
                        $item['pattern'] ?? 'buy_and_use',
                        $item['seller_ids'] ?? []
                    );
                }
            }

            // Create Payment Record
            $paymentNumber = 'PAY-' . now()->format('Ymd') . '-' . rand(10000, 99999);

            Payment::create([
                'payment_number' => $paymentNumber,
                'invoice_id' => $invoice->id,
                'patient_id' => $validated['patient_id'],
                'branch_id' => $invoice->branch_id,
                'amount' => $validated['amount_paid'],
                'payment_method' => $validated['payment_method'],
                'status' => 'completed',
                'payment_date' => today(),
                'reference_number' => $validated['reference_number'] ?? null,
                'card_last_4' => $validated['card_last_4'] ?? null,
                'notes' => $validated['notes'] ?? null,
                'created_by' => auth()->id() ?? null,
            ]);

            // Update Queue status
            $queue = Queue::find($validated['queue_id']);
            $queue->update(['status' => 'paid']);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment processed successfully',
                'invoice_number' => $invoiceNumber,
                'invoice_id' => $invoice->id,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to process payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Sell course package with 3 purchase patterns
     * ข้อ 6: ซื้อคอร์ส 3 แบบ (buy_and_use, buy_for_later, retroactive)
     */
    private function sellCourse($patientId, $packageId, $invoiceId, $purchasePattern, $sellerIds = [])
    {
        $package = CoursePackage::findOrFail($packageId);

        // Validate purchase pattern is allowed
        if ($purchasePattern === 'buy_and_use' && !$package->allow_buy_and_use) {
            throw new \Exception('Buy and use pattern not allowed for this package');
        }
        if ($purchasePattern === 'buy_for_later' && !$package->allow_buy_for_later) {
            throw new \Exception('Buy for later pattern not allowed for this package');
        }
        if ($purchasePattern === 'retroactive' && !$package->allow_retroactive) {
            throw new \Exception('Retroactive pattern not allowed for this package');
        }

        $courseNumber = 'CRS-' . now()->format('Ymd') . '-' . rand(10000, 99999);
        $purchaseDate = today();
        $expiryDate = today()->addDays($package->validity_days);

        // For buy_and_use and retroactive, activate immediately
        $activationDate = in_array($purchasePattern, ['buy_and_use', 'retroactive']) ? $purchaseDate : null;

        // For retroactive, calculate used sessions from previous treatments
        $usedSessions = 0;
        if ($purchasePattern === 'retroactive') {
            // Find unpaid treatments for this patient that could be covered by this course
            $unpaidTreatments = Treatment::where('patient_id', $patientId)
                ->where('service_id', $package->service_id)
                ->where(function ($query) {
                    $query->whereNull('course_purchase_id')
                          ->orWhereHas('coursePurchase', function ($q) {
                              $q->where('purchase_pattern', '!=', 'retroactive');
                          });
                })
                ->whereDate('created_at', '>=', $purchaseDate->copy()->subDays(30)) // Last 30 days
                ->limit($package->total_sessions)
                ->get();

            $usedSessions = $unpaidTreatments->count();

            // Link treatments to this course purchase
            foreach ($unpaidTreatments as $treatment) {
                // This will be updated after course purchase is created
                // We'll store the treatment IDs for later
            }
        }

        // Create course purchase
        if (is_string($sellerIds)) {
            $sellerIds = json_decode($sellerIds, true) ?? [];
        }

        $coursePurchase = CoursePurchase::create([
            'course_number' => $courseNumber,
            'patient_id' => $patientId,
            'package_id' => $packageId,
            'invoice_id' => $invoiceId,
            'purchase_branch_id' => Invoice::find($invoiceId)->branch_id,
            'purchase_pattern' => $purchasePattern,
            'purchase_date' => $purchaseDate,
            'activation_date' => $activationDate,
            'expiry_date' => $expiryDate,
            'total_sessions' => $package->total_sessions,
            'used_sessions' => $usedSessions,
            'status' => 'active',
            'allow_branch_sharing' => true,
            'created_by' => auth()->id() ?? null,
            'seller_ids' => $sellerIds,
        ]);

        // For retroactive, create usage logs for previous treatments
        if ($purchasePattern === 'retroactive' && isset($unpaidTreatments)) {
            foreach ($unpaidTreatments as $treatment) {
                CourseUsageLog::create([
                    'course_purchase_id' => $coursePurchase->id,
                    'treatment_id' => $treatment->id,
                    'patient_id' => $patientId,
                    'branch_id' => $treatment->branch_id,
                    'pt_id' => $treatment->pt_id,
                    'sessions_used' => 1,
                    'usage_date' => $treatment->created_at->toDateString(),
                    'status' => 'used',
                    'is_cross_branch' => false,
                    'purchase_branch_id' => $coursePurchase->purchase_branch_id,
                    'created_by' => auth()->id() ?? null,
                ]);

                // Update treatment to link to this course
                $treatment->update(['course_purchase_id' => $coursePurchase->id]);
            }
        }

        // For buy_and_use, automatically use 1 session
        if ($purchasePattern === 'buy_and_use') {
            // Assume there's a current treatment to link
            // This will be handled by the treatment recording system
        }

        // NOTE: DF is NOT recorded here at sale time
        // DF will be recorded when PT performs the treatment (see QueueController)
        // This ensures PT who does the work gets DF, not the seller

        return $coursePurchase;
    }

    /**
     * Cancel course and calculate refund
     * ข้อ 8: ยกเลิกคอร์ส + คำนวณเงินคืน (หักราคาเต็มของครั้งที่ใช้แล้ว)
     */
    public function cancelCourse(Request $request, $coursePurchaseId)
    {
        $validated = $request->validate([
            'reason' => 'required|string',
        ]);

        try {
            DB::beginTransaction();

            $coursePurchase = CoursePurchase::with(['package.service', 'invoice', 'usageLogs'])->findOrFail($coursePurchaseId);

            if ($coursePurchase->status === 'cancelled') {
                throw new \Exception('Course already cancelled');
            }

            // Calculate refund amount
            // Formula: Refund = (Total Price) - (Used Sessions × Full Price Per Session)
            $totalPrice = $coursePurchase->invoice ? $coursePurchase->invoice->total_amount : ($coursePurchase->package->price ?? 0);
            $usedSessions = $coursePurchase->used_sessions;
            $totalSessions = $coursePurchase->total_sessions;

            // Calculate full price per session (without course discount)
            $service = $coursePurchase->package->service ?? null;
            $sessionsForCalc = $totalSessions > 0 ? $totalSessions : 1;
            $fullPricePerSession = $service ? $service->default_price : ($coursePurchase->package->price / $sessionsForCalc); // Full price, not discounted course price

            // Calculate used amount at full price
            $usedAmount = $usedSessions * $fullPricePerSession;

            // Calculate refund amount
            $refundAmount = max(0, $totalPrice - $usedAmount);

            // Create refund record
            $refundNumber = 'REF-' . now()->format('Ymd') . '-' . rand(10000, 99999);

            $refund = Refund::create([
                'refund_number' => $refundNumber,
                'invoice_id' => $coursePurchase->invoice_id,
                'patient_id' => $coursePurchase->patient_id,
                'branch_id' => $coursePurchase->purchase_branch_id,
                'refund_type' => 'course_cancellation',
                'refund_amount' => $refundAmount,
                'status' => 'approved',
                'refund_date' => today(),
                'original_amount' => $totalPrice,
                'used_amount' => $usedAmount,
                'penalty_amount' => 0,
                'calculation_notes' => "Used sessions: {$usedSessions}/{$totalSessions}. Full price per session: {$fullPricePerSession}",
                'reason' => $validated['reason'],
                'approved_at' => now(),
                'approved_by' => auth()->id() ?? null,
                'refund_method' => 'bank_transfer',
                'created_by' => auth()->id() ?? null,
            ]);

            // Update course purchase status
            $coursePurchase->update([
                'status' => 'cancelled',
                'cancellation_reason' => $validated['reason'],
                'cancelled_at' => now(),
                'cancelled_by' => auth()->id() ?? null,
            ]);

            // CRITICAL: Create revenue adjustment backdated to original invoice date
            // This ensures P&L reports show accurate net revenue for the purchase period
            if ($coursePurchase->invoice) {
                RevenueAdjustmentService::createRefundAdjustment(
                    $coursePurchase->invoice,
                    $refund,
                    $refundAmount
                );
            }

            // ข้อ 21: Clawback commission
            $this->clawbackCommission($coursePurchase->invoice_id, $refund->id);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Course cancelled successfully',
                'refund_number' => $refundNumber,
                'refund_amount' => $refundAmount,
                'used_sessions' => $usedSessions,
                'used_amount' => $usedAmount,
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'success' => false,
                'message' => 'Failed to cancel course: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Clawback commission when course is cancelled
     * ข้อ 21: หักค่าคอมคืน แต่ไม่หัก DF ของ PT
     */
    private function clawbackCommission($invoiceId, $refundId)
    {
        // Find all commissions related to this invoice that are eligible for clawback
        $commissions = Commission::where('invoice_id', $invoiceId)
            ->where('is_clawback_eligible', true)
            ->where('status', '!=', 'clawed_back')
            ->get();

        foreach ($commissions as $commission) {
            // Clawback the commission
            $commission->update([
                'status' => 'clawed_back',
                'clawed_back_at' => now(),
                'clawed_back_by' => auth()->id() ?? null,
                'clawback_reason' => 'Course cancellation',
                'clawback_refund_id' => $refundId,
            ]);
        }

        // IMPORTANT: ข้อ 21 - DO NOT clawback DF payments
        // DF payments remain untouched as they are payment for services already rendered
        // We only clawback commissions from sales, not service fees (DF) from actual work done

        return $commissions->count();
    }

    /**
     * Public endpoint for course cancellation
     */
    public function storeCancellation(Request $request)
    {
        return $this->cancelCourse($request, $request->course_purchase_id);
    }
}
