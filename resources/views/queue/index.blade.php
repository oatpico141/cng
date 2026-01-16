@extends('layouts.app')

@section('title', 'ระบบคิว - GCMS')

@push('styles')
<style>
    /* QUEUE PAGE - MODERN KANBAN DESIGN */

    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 50%, #38bdf8 100%);
        border-radius: 16px;
        padding: 1.25rem 1.5rem;
        color: white;
        margin-bottom: 1.25rem;
        box-shadow: 0 4px 20px rgba(14, 165, 233, 0.3);
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -50%;
        width: 100%;
        height: 200%;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        pointer-events: none;
    }

    .page-header h2 {
        font-size: 1.25rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .page-header p {
        font-size: 0.85rem;
        opacity: 0.9;
        margin: 0;
    }

    /* Stats Summary */
    .queue-stats {
        display: flex;
        gap: 1rem;
        margin-bottom: 1rem;
        flex-wrap: wrap;
    }

    .stat-item {
        background: white;
        border-radius: 12px;
        padding: 0.75rem 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.06);
        border: 1px solid #e2e8f0;
        flex: 1;
        min-width: 140px;
    }

    .stat-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
    }

    .stat-icon.waiting { background: #fef3c7; color: #d97706; }
    .stat-icon.calling { background: #fee2e2; color: #dc2626; }
    .stat-icon.treatment { background: #dbeafe; color: #2563eb; }
    .stat-icon.payment { background: #ede9fe; color: #7c3aed; }
    .stat-icon.completed { background: #dcfce7; color: #16a34a; }

    .stat-info h4 {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1;
        margin: 0;
    }

    .stat-info span {
        font-size: 0.75rem;
        color: #64748b;
    }

    /* Queue Columns Container */
    .queue-container {
        display: grid;
        grid-template-columns: repeat(5, 1fr);
        gap: 1rem;
        min-height: calc(100vh - 280px);
    }

    /* Column Cards */
    .queue-column {
        background: #fff;
        border-radius: 16px;
        border: none;
        box-shadow: 0 2px 12px rgba(0,0,0,0.06);
        display: flex;
        flex-direction: column;
        overflow: hidden;
    }

    .column-header {
        padding: 1rem;
        font-size: 0.9rem;
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        border-bottom: 3px solid;
    }

    .column-header i {
        font-size: 1.1rem;
    }

    .column-header .count {
        background: rgba(0,0,0,0.08);
        padding: 3px 10px;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 700;
        margin-left: auto;
    }

    .waiting-column .column-header {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        border-bottom-color: #f59e0b;
        color: #92400e;
    }
    .calling-column .column-header {
        background: linear-gradient(135deg, #fee2e2, #fecaca);
        border-bottom-color: #dc2626;
        color: #991b1b;
    }
    .treatment-column .column-header {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        border-bottom-color: #2563eb;
        color: #1e40af;
    }
    .payment-column .column-header {
        background: linear-gradient(135deg, #ede9fe, #ddd6fe);
        border-bottom-color: #7c3aed;
        color: #5b21b6;
    }
    .completed-column .column-header {
        background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        border-bottom-color: #16a34a;
        color: #166534;
    }

    .column-content {
        flex: 1;
        overflow-y: auto;
        padding: 0.75rem;
        background: #f8fafc;
    }

    /* Queue Card */
    .queue-card {
        background: white;
        border: none;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        box-shadow: 0 2px 8px rgba(0,0,0,0.04);
        transition: all 0.3s ease;
        border-left: 4px solid transparent;
    }

    .queue-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 6px 20px rgba(0,0,0,0.1);
    }

    .waiting-column .queue-card { border-left-color: #f59e0b; }
    .calling-column .queue-card { border-left-color: #dc2626; }
    .treatment-column .queue-card { border-left-color: #2563eb; }
    .payment-column .queue-card { border-left-color: #7c3aed; }
    .completed-column .queue-card { border-left-color: #16a34a; }

    .calling-card {
        background: linear-gradient(135deg, #fef2f2, #fee2e2) !important;
        animation: pulse-glow 2s infinite;
    }

    @keyframes pulse-glow {
        0%, 100% { box-shadow: 0 2px 8px rgba(220, 38, 38, 0.2); }
        50% { box-shadow: 0 4px 20px rgba(220, 38, 38, 0.4); }
    }

    .patient-name {
        font-size: 1rem;
        font-weight: 600;
        color: #1e293b;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .patient-name i {
        color: #64748b;
        font-size: 0.9rem;
    }

    .card-actions {
        display: flex;
        gap: 0.5rem;
        margin-top: 0.75rem;
    }

    .card-actions .btn {
        flex: 1;
        font-size: 0.8rem;
        padding: 0.5rem 0.75rem;
        border-radius: 8px;
        font-weight: 500;
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 0.4rem;
        transition: all 0.2s;
    }

    .card-actions .btn:hover {
        transform: translateY(-1px);
    }

    .card-actions .btn i {
        font-size: 0.9rem;
    }

    /* Treatment Timer */
    .treatment-timer {
        background: linear-gradient(135deg, #dbeafe, #bfdbfe);
        border-radius: 10px;
        padding: 0.75rem;
        margin-top: 0.75rem;
        text-align: center;
        border: 1px solid #93c5fd;
    }

    .timer-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e40af;
        font-family: 'SF Mono', 'Monaco', monospace;
    }

    .timer-label {
        font-size: 0.7rem;
        color: #3b82f6;
        font-weight: 500;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    /* Completed Card */
    .completed-card {
        background: linear-gradient(135deg, #f0fdf4, #dcfce7) !important;
    }

    /* Payment Card */
    .payment-card {
        background: linear-gradient(135deg, #faf5ff, #ede9fe) !important;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 2.5rem 1rem;
        color: #94a3b8;
    }

    .empty-state i {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
        opacity: 0.5;
    }

    .empty-state span {
        font-size: 0.85rem;
        display: block;
    }

    /* Modal Styles */
    .modal-header {
        background: linear-gradient(135deg, #0284c7, #0ea5e9);
        color: white;
        border-bottom: none;
        padding: 1rem 1.25rem;
        border-radius: 0;
    }

    .modal-header .modal-title {
        font-size: 1.1rem;
        font-weight: 600;
    }

    .modal-header .btn-close {
        filter: invert(1);
        opacity: 0.8;
    }

    .modal-body {
        padding: 1.25rem;
    }

    .form-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #334155;
    }

    .form-control, .form-select {
        font-size: 0.9rem;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        padding: 0.6rem 0.75rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: #0ea5e9;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
    }

    .treatment-summary {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
        border: 1px solid #e2e8f0;
    }

    .summary-row {
        display: flex;
        justify-content: space-between;
        padding: 0.4rem 0;
        font-size: 0.9rem;
    }

    .summary-label {
        color: #64748b;
    }

    .summary-value {
        font-weight: 600;
        color: #1e293b;
    }

    /* Payment Method Options */
    .payment-method-options {
        display: flex;
        flex-direction: column;
        gap: 0.5rem;
    }

    .payment-option {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.75rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .payment-option:hover {
        border-color: #0ea5e9;
        background: #f0f9ff;
    }

    .payment-option.selected {
        border-color: #0ea5e9;
        background: #f0f9ff;
    }

    .payment-option input[type="radio"] {
        display: none;
    }

    .option-header {
        display: flex;
        align-items: center;
        gap: 0.5rem;
        font-weight: 600;
        font-size: 0.9rem;
    }

    .option-desc {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 0.25rem;
        padding-left: 1.5rem;
    }

    /* Course Selection */
    .course-option, .existing-course {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .course-option:hover, .existing-course:hover {
        border-color: #0ea5e9;
        background: #f0f9ff;
    }

    .course-option.selected {
        border-color: #0ea5e9;
        background: #f0f9ff;
    }

    .existing-course.selected {
        border-color: #10b981;
        background: #f0fdf4;
    }

    .course-name {
        font-weight: 600;
        font-size: 0.9rem;
    }

    .course-price {
        font-size: 0.85rem;
        color: #0369a1;
        font-weight: 700;
    }

    .course-sessions, .course-remaining {
        font-size: 0.75rem;
        color: #64748b;
    }

    .course-remaining {
        color: #166534;
        font-weight: 600;
    }

    /* Payment Summary */
    .payment-summary {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        border-radius: 12px;
        padding: 1rem;
        margin-top: 1rem;
        border: 1px solid #e2e8f0;
    }

    .payment-row {
        display: flex;
        justify-content: space-between;
        padding: 0.4rem 0;
        font-size: 0.9rem;
    }

    .payment-total {
        border-top: 2px solid #e2e8f0;
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        font-weight: 700;
        font-size: 1rem;
    }

    .retroactive-discount {
        color: #dc2626;
    }

    /* Payment Buttons */
    .payment-buttons {
        display: flex;
        gap: 0.5rem;
        margin-top: 1rem;
    }

    .payment-btn {
        flex: 1;
        padding: 0.75rem 0.5rem;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        background: white;
        cursor: pointer;
        text-align: center;
        font-size: 0.8rem;
        font-weight: 500;
        transition: all 0.2s;
    }

    .payment-btn:hover {
        border-color: #0ea5e9;
        background: #f0f9ff;
        transform: translateY(-2px);
    }

    .payment-btn.selected {
        border-color: #0ea5e9;
        background: linear-gradient(135deg, #0ea5e9, #0284c7);
        color: white;
    }

    .payment-btn i {
        display: block;
        font-size: 1.25rem;
        margin-bottom: 0.25rem;
    }

    /* Cart System Styles */
    .cart-items {
        max-height: 300px;
        overflow-y: auto;
        margin-bottom: 1rem;
    }

    .cart-item {
        display: flex;
        justify-content: space-between;
        align-items: center;
        padding: 0.75rem;
        background: white;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        margin-bottom: 0.5rem;
    }

    .cart-item-info {
        flex: 1;
    }

    .cart-item-name {
        font-weight: 600;
        font-size: 0.9rem;
        color: #1e293b;
    }

    .cart-item-detail {
        font-size: 0.75rem;
        color: #64748b;
    }

    .cart-item-price {
        font-weight: 700;
        font-size: 1rem;
        color: #0369a1;
        margin-right: 0.75rem;
    }

    .cart-item-remove {
        color: #dc2626;
        cursor: pointer;
        padding: 0.5rem;
        border-radius: 8px;
        transition: all 0.2s;
    }

    .cart-item-remove:hover {
        background: #fee2e2;
        color: #991b1b;
    }

    .add-item-section {
        border: 2px dashed #cbd5e1;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
        background: #f8fafc;
    }

    .add-item-buttons {
        display: flex;
        gap: 0.75rem;
        margin-bottom: 0.75rem;
    }

    .add-item-btn {
        flex: 1;
        padding: 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        background: white;
        cursor: pointer;
        text-align: center;
        font-size: 0.85rem;
        font-weight: 500;
        transition: all 0.2s;
    }

    .add-item-btn:hover {
        border-color: #0ea5e9;
        background: #f0f9ff;
        transform: translateY(-2px);
    }

    .add-item-btn i {
        display: block;
        font-size: 1.5rem;
        margin-bottom: 0.3rem;
        color: #0ea5e9;
    }

    .item-selector {
        display: none;
        margin-top: 0.75rem;
    }

    .item-selector.active {
        display: block;
    }

    .selectable-item {
        padding: 0.75rem;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        margin-bottom: 0.4rem;
        cursor: pointer;
        display: flex;
        justify-content: space-between;
        align-items: center;
        font-size: 0.85rem;
        background: white;
        transition: all 0.2s;
    }

    .selectable-item:hover {
        border-color: #0ea5e9;
        background: #f0f9ff;
    }

    .empty-cart {
        text-align: center;
        padding: 2rem;
        color: #94a3b8;
    }

    .empty-cart i {
        font-size: 2.5rem;
        margin-bottom: 0.75rem;
        opacity: 0.5;
    }

    .cart-total-section {
        background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
        border-radius: 12px;
        padding: 1rem;
        margin-top: 0.75rem;
        border: 1px solid #bae6fd;
    }

    .cart-total-row {
        display: flex;
        justify-content: space-between;
        font-size: 0.9rem;
        padding: 0.3rem 0;
    }

    .cart-grand-total {
        border-top: 2px solid #0ea5e9;
        margin-top: 0.5rem;
        padding-top: 0.5rem;
        font-weight: 700;
        font-size: 1.1rem;
        color: #0369a1;
    }

    /* Responsive */
    @media (max-width: 1400px) {
        .queue-container {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 1200px) {
        .queue-container {
            grid-template-columns: repeat(2, 1fr);
        }
        .queue-column {
            min-height: 300px;
        }
    }

    @media (max-width: 768px) {
        .queue-container {
            grid-template-columns: 1fr;
        }
        .page-header {
            padding: 1rem;
            border-radius: 12px;
        }
        .page-header h2 {
            font-size: 1.1rem;
        }
        .queue-stats {
            flex-direction: column;
        }
        .stat-item {
            min-width: auto;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-list-ol"></i> ระบบจัดการคิว</h2>
                <p><i class="bi bi-calendar3 me-1"></i>{{ now()->locale('th')->isoFormat('วันdddd ที่ D MMMM YYYY') }}</p>
            </div>
            <div class="d-flex gap-2">
                <a href="{{ url('/queue/display') }}" target="_blank" class="btn btn-light btn-sm px-3">
                    <i class="bi bi-tv me-1"></i>จอแสดงผล
                </a>
            </div>
        </div>
    </div>

    @php
        // Get appointments for today
        $todayAppointments = \App\Models\Appointment::with(['patient', 'pt', 'branch'])
            ->whereDate('appointment_date', today())
            ->orderBy('appointment_time')
            ->get();

        // Build queue lists
        $waitingItems = collect();
        $callingItems = collect();
        $inTreatmentItems = collect();
        $awaitingPaymentItems = collect();
        $completedItems = collect();

        $counter = 1;
        foreach ($todayAppointments as $apt) {
            // Skip cancelled, rescheduled, no_show
            if (in_array($apt->status, ['cancelled', 'rescheduled', 'no_show'])) {
                continue;
            }

            $item = [
                'id' => $apt->id,
                'number' => $counter,
                'name' => $apt->patient->name ?? 'ไม่ระบุ',
                'phone' => $apt->patient->phone ?? '-',
                'time' => substr($apt->appointment_time, 0, 5),
                'status' => $apt->status,
                'type' => 'appointment',
                'started_at' => $apt->status === 'confirmed' ? $apt->updated_at : null,
                'completed_at' => $apt->status === 'completed' ? $apt->updated_at : null,
                'duration' => null,
                'is_overtime' => false,
                'patient_id' => $apt->patient_id,
                'pt_id' => $apt->pt_id,
                'pt_name' => $apt->pt->name ?? null
            ];

            // Calculate duration for awaiting_payment
            if ($apt->status === 'awaiting_payment') {
                // Get treatment to find duration
                $treatment = \App\Models\Treatment::where('appointment_id', $apt->id)->first();
                if ($treatment) {
                    $item['duration'] = $treatment->duration_minutes;
                    $item['is_overtime'] = $treatment->duration_minutes > 15;
                }
            }

            if ($apt->status === 'pending') {
                $waitingItems->push($item);
            } elseif ($apt->status === 'calling') {
                $callingItems->push($item);
            } elseif ($apt->status === 'confirmed') {
                $inTreatmentItems->push($item);
            } elseif ($apt->status === 'awaiting_payment') {
                $awaitingPaymentItems->push($item);
            } elseif ($apt->status === 'completed') {
                $completedItems->push($item);
            }
            $counter++;
        }

        // Get services and packages for modal
        $services = \App\Models\Service::where('is_active', true)->orderBy('name')->get();
        $coursePackages = \App\Models\CoursePackage::where('is_active', true)->orderBy('name')->get();
        $pts = \App\Models\User::whereHas('role', function($q) {
            $q->whereIn('name', ['PT', 'Physiotherapist', 'นักกายภาพบำบัด']);
        })->where('is_active', true)->get();
    @endphp

    <!-- Stats Summary -->
    <div class="queue-stats">
        <div class="stat-item">
            <div class="stat-icon waiting"><i class="bi bi-clock"></i></div>
            <div class="stat-info">
                <h4>{{ $waitingItems->count() }}</h4>
                <span>รอคิว</span>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon calling"><i class="bi bi-megaphone"></i></div>
            <div class="stat-info">
                <h4>{{ $callingItems->count() }}</h4>
                <span>กำลังเรียก</span>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon treatment"><i class="bi bi-heart-pulse"></i></div>
            <div class="stat-info">
                <h4>{{ $inTreatmentItems->count() }}</h4>
                <span>รักษาอยู่</span>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon payment"><i class="bi bi-credit-card"></i></div>
            <div class="stat-info">
                <h4>{{ $awaitingPaymentItems->count() }}</h4>
                <span>รอชำระ</span>
            </div>
        </div>
        <div class="stat-item">
            <div class="stat-icon completed"><i class="bi bi-check-circle"></i></div>
            <div class="stat-info">
                <h4>{{ $completedItems->count() }}</h4>
                <span>เสร็จสิ้น</span>
            </div>
        </div>
    </div>

    <!-- 5 Columns Kanban -->
    <div class="queue-container">
        <!-- Waiting Column -->
        <div class="queue-column waiting-column">
            <div class="column-header">
                <i class="bi bi-clock-fill"></i>
                รอคิว
                <span class="count">{{ $waitingItems->count() }}</span>
            </div>
            <div class="column-content">
                @forelse($waitingItems as $item)
                    <div class="queue-card">
                        <div class="patient-name"><i class="bi bi-person-fill"></i> {{ $item['name'] }}</div>
                        <div class="card-actions">
                            <button class="btn btn-warning" onclick="callQueue('{{ $item['id'] }}', '{{ $item['name'] }}', {{ $item['number'] }})">
                                <i class="bi bi-megaphone-fill"></i> เรียกคิว
                            </button>
                            <button class="btn btn-outline-danger btn-sm" onclick="cancelAppointment('{{ $item['id'] }}', '{{ $item['name'] }}')" style="flex: 0; padding: 0.5rem;">
                                <i class="bi bi-x-lg"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="bi bi-inbox d-block"></i>
                        <span>ไม่มีคิวรอ</span>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Calling Column -->
        <div class="queue-column calling-column">
            <div class="column-header">
                <i class="bi bi-megaphone-fill"></i>
                กำลังเรียก
                <span class="count">{{ $callingItems->count() }}</span>
            </div>
            <div class="column-content">
                @forelse($callingItems as $item)
                    <div class="queue-card calling-card">
                        <div class="patient-name" style="color: #dc2626;"><i class="bi bi-person-fill"></i> {{ $item['name'] }}</div>
                        <div class="card-actions">
                            <button class="btn btn-primary" onclick="startTreatment('{{ $item['id'] }}', '{{ $item['name'] }}')">
                                <i class="bi bi-play-fill"></i> เริ่มรักษา
                            </button>
                        </div>
                        <div class="card-actions">
                            <button class="btn btn-outline-secondary btn-sm" onclick="recallQueue('{{ $item['id'] }}', {{ $item['number'] }})">
                                <i class="bi bi-arrow-repeat"></i> เรียกซ้ำ
                            </button>
                            <button class="btn btn-outline-danger btn-sm" onclick="skipQueue('{{ $item['id'] }}', '{{ $item['name'] }}')" style="flex: 0; padding: 0.5rem 0.75rem;">
                                <i class="bi bi-skip-forward-fill"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="bi bi-megaphone d-block"></i>
                        <span>ไม่มีคิวกำลังเรียก</span>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- In Treatment Column -->
        <div class="queue-column treatment-column">
            <div class="column-header">
                <i class="bi bi-heart-pulse-fill"></i>
                กำลังรักษา
                <span class="count">{{ $inTreatmentItems->count() }}</span>
            </div>
            <div class="column-content">
                @forelse($inTreatmentItems as $item)
                    <div class="queue-card">
                        <div class="patient-name"><i class="bi bi-person-fill"></i> {{ $item['name'] }}</div>
                        <div class="treatment-timer">
                            <div class="timer-value" id="timer-{{ $item['id'] }}">
                                @if($item['started_at'])
                                    {{ now()->diffInMinutes($item['started_at']) }}:{{ str_pad(now()->diffInSeconds($item['started_at']) % 60, 2, '0', STR_PAD_LEFT) }}
                                @else
                                    00:00
                                @endif
                            </div>
                            <div class="timer-label">เวลารักษา</div>
                        </div>
                        <div class="card-actions">
                            <button class="btn btn-success" onclick="finishTreatment('{{ $item['id'] }}', '{{ $item['name'] }}', '{{ $item['patient_id'] }}', '{{ $item['started_at'] ?? now() }}')">
                                <i class="bi bi-stop-fill"></i> เสร็จสิ้น
                            </button>
                            <button class="btn btn-outline-danger btn-sm" onclick="cancelTreatmentAppointment('{{ $item['id'] }}', '{{ $item['name'] }}')" style="flex: 0; padding: 0.5rem;">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="bi bi-heart-pulse d-block"></i>
                        <span>ไม่มีคนไข้กำลังรักษา</span>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Awaiting Payment Column -->
        <div class="queue-column payment-column">
            <div class="column-header">
                <i class="bi bi-credit-card-fill"></i>
                รอชำระเงิน
                <span class="count">{{ $awaitingPaymentItems->count() }}</span>
            </div>
            <div class="column-content">
                @forelse($awaitingPaymentItems as $item)
                    <div class="queue-card payment-card">
                        <div class="patient-name"><i class="bi bi-person-fill"></i> {{ $item['name'] }}</div>
                        <div class="card-actions">
                            <button class="btn btn-success" onclick="showPaymentModal('{{ $item['id'] }}', '{{ $item['name'] }}', '{{ $item['patient_id'] }}', {{ $item['duration'] ?? 0 }})">
                                <i class="bi bi-wallet2"></i> ชำระเงิน
                            </button>
                            <button class="btn btn-outline-danger btn-sm" onclick="cancelPayment('{{ $item['id'] }}', '{{ $item['name'] }}')" style="flex: 0; padding: 0.5rem;">
                                <i class="bi bi-arrow-counterclockwise"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="bi bi-cash-stack d-block"></i>
                        <span>ไม่มีคนไข้รอชำระ</span>
                    </div>
                @endforelse
            </div>
        </div>

        <!-- Completed Column -->
        <div class="queue-column completed-column">
            <div class="column-header">
                <i class="bi bi-check-circle-fill"></i>
                เสร็จสิ้น
                <span class="count">{{ $completedItems->count() }}</span>
            </div>
            <div class="column-content">
                @forelse($completedItems as $item)
                    <div class="queue-card completed-card">
                        <div class="patient-name"><i class="bi bi-person-check-fill"></i> {{ $item['name'] }}</div>
                        <div class="card-actions">
                            <button class="btn btn-outline-primary btn-sm" onclick="printReceipt('{{ $item['id'] }}')">
                                <i class="bi bi-printer"></i> พิมพ์ใบเสร็จ
                            </button>
                            <button class="btn btn-outline-warning btn-sm" onclick="editCompletedPayment('{{ $item['id'] }}', '{{ $item['name'] }}', '{{ $item['patient_id'] }}')" style="flex: 0; padding: 0.5rem 0.75rem;">
                                <i class="bi bi-pencil"></i>
                            </button>
                        </div>
                    </div>
                @empty
                    <div class="empty-state">
                        <i class="bi bi-check-circle d-block"></i>
                        <span>ยังไม่มีคนไข้เสร็จสิ้น</span>
                    </div>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Finish Treatment Modal (Simple - just stop timer) -->
<div class="modal fade" id="finishModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-stop-circle me-2"></i>เสร็จสิ้นการรักษา</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="finish_appointment_id">
                <input type="hidden" id="finish_patient_id">

                <div class="treatment-summary">
                    <div class="summary-row">
                        <span class="summary-label">คนไข้:</span>
                        <span class="summary-value" id="finish_patient_name"></span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">เวลาเริ่ม:</span>
                        <span class="summary-value" id="finish_start_time"></span>
                    </div>
                    <div class="summary-row">
                        <span class="summary-label">เวลาสิ้นสุด:</span>
                        <span class="summary-value" id="finish_end_time"></span>
                    </div>
                    <div class="summary-row" style="border-top: 1px solid #e2e8f0; padding-top: 0.4rem; margin-top: 0.25rem;">
                        <span class="summary-label" style="font-weight: 600;">ระยะเวลา:</span>
                        <span class="summary-value" id="finish_duration" style="font-size: 1rem; color: #0369a1;"></span>
                    </div>
                </div>

                <p style="font-size: 0.8rem; color: #64748b; text-align: center;">
                    <i class="bi bi-info-circle me-1"></i>
                    หยุดเวลาและย้ายไปรอชำระเงิน
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-warning" onclick="confirmFinishTreatment()">
                    <i class="bi bi-check-lg me-1"></i>ยืนยันเสร็จสิ้น
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal - Cart System -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-cart3 me-2"></i>ชำระเงิน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="paymentForm" autocomplete="off">
                    <input type="hidden" id="payment_appointment_id">
                    <input type="hidden" id="payment_patient_id">

                    <!-- Patient Info -->
                    <div class="treatment-summary">
                        <div class="summary-row">
                            <span class="summary-label">คนไข้:</span>
                            <span class="summary-value" id="modal_patient_name"></span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">ระยะเวลารักษา:</span>
                            <span class="summary-value" id="modal_duration"></span>
                        </div>
                    </div>

                    <!-- PT Selection -->
                    <div class="row g-2 mb-3">
                        <div class="col-md-6">
                            <label class="form-label">นักกายภาพบำบัด <span class="text-danger">*</span></label>
                            <select class="form-select" id="pt_id" name="pt_id" required>
                                <option value="">เลือกนักกายภาพ</option>
                                @foreach($pts as $pt)
                                    <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">บันทึกการรักษา</label>
                            <input type="text" class="form-control" id="treatment_notes" name="treatment_notes" placeholder="บันทึกเพิ่มเติม...">
                        </div>
                    </div>

                    <!-- Cart Items -->
                    <label class="form-label" style="font-weight: 600;">รายการสินค้า/บริการ</label>
                    <div class="cart-items" id="cartItems">
                        <div class="empty-cart" id="emptyCart">
                            <i class="bi bi-cart-x d-block"></i>
                            <span>ยังไม่มีรายการ</span>
                        </div>
                    </div>

                    <!-- Add Item Section -->
                    <div class="add-item-section">
                        <div class="add-item-buttons">
                            <div class="add-item-btn" onclick="showItemSelector('service')">
                                <i class="bi bi-clipboard2-pulse"></i>
                                เพิ่มหัตถการ
                            </div>
                            <div class="add-item-btn" onclick="showItemSelector('course')">
                                <i class="bi bi-box-seam"></i>
                                เพิ่มคอร์ส
                            </div>
                            <div class="add-item-btn" onclick="showItemSelector('use_course')">
                                <i class="bi bi-ticket-perforated"></i>
                                ตัดคอร์สเดิม
                            </div>
                        </div>

                        <!-- Service Selector -->
                        <div class="item-selector" id="serviceSelector">
                            <label class="form-label">เลือกหัตถการ:</label>
                            @foreach($services as $service)
                                <div class="selectable-item" onclick="addServiceToCart('{{ $service->id }}', '{{ $service->name }}', {{ $service->default_price }})">
                                    <span>{{ $service->name }}</span>
                                    <span class="text-primary fw-bold">฿{{ number_format($service->default_price) }}</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Course Package Selector -->
                        <div class="item-selector" id="courseSelector">
                            <label class="form-label">เลือกคอร์สที่ต้องการซื้อ:</label>
                            @foreach($coursePackages as $package)
                                <div class="selectable-item" onclick="showCourseOptions('{{ $package->id }}', '{{ $package->name }}', {{ $package->price }}, {{ $package->total_sessions }})">
                                    <div>
                                        <span>{{ $package->name }}</span>
                                        <small class="text-muted d-block">{{ $package->total_sessions }} ครั้ง</small>
                                    </div>
                                    <span class="text-primary fw-bold">฿{{ number_format($package->price) }}</span>
                                </div>
                            @endforeach
                        </div>

                        <!-- Existing Course Selector -->
                        <div class="item-selector" id="useCourseSelector">
                            <label class="form-label">เลือกคอร์สที่ต้องการใช้:</label>
                            <div id="existingCourseList">
                                <div class="text-muted text-center py-2" style="font-size: 0.75rem;">กำลังโหลด...</div>
                            </div>
                        </div>
                    </div>

                    <!-- Cart Total -->
                    <div class="cart-total-section">
                        <div class="cart-total-row">
                            <span>รวมค่าบริการ:</span>
                            <span id="totalServices">฿0</span>
                        </div>
                        <div class="cart-total-row">
                            <span>รวมค่าคอร์ส:</span>
                            <span id="totalCourses">฿0</span>
                        </div>
                        <div class="cart-total-row cart-grand-total">
                            <span>ยอดชำระทั้งหมด:</span>
                            <span id="grandTotal">฿0</span>
                        </div>
                    </div>

                    <!-- Payment Method -->
                    <div id="finalPaymentSection" style="display: none;">
                        <label class="form-label mt-3">วิธีชำระเงิน</label>
                        <div class="payment-buttons">
                            <div class="payment-btn" onclick="selectFinalPayment('cash')">
                                <i class="bi bi-cash"></i>
                                เงินสด
                            </div>
                            <div class="payment-btn" onclick="selectFinalPayment('card')">
                                <i class="bi bi-credit-card"></i>
                                บัตร
                            </div>
                            <div class="payment-btn" onclick="selectFinalPayment('qr')">
                                <i class="bi bi-qr-code"></i>
                                QR
                            </div>
                            <div class="payment-btn" onclick="selectFinalPayment('transfer')">
                                <i class="bi bi-bank"></i>
                                โอน
                            </div>
                        </div>
                        <input type="hidden" id="final_payment_method" value="">
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-success" onclick="submitPayment()">
                    <i class="bi bi-receipt me-1"></i>ยืนยันชำระเงิน
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Success Modal -->
<div class="modal fade" id="successModal" tabindex="-1" data-bs-backdrop="static">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-5">
                <div class="mb-4">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 5rem;"></i>
                </div>
                <h4 class="mb-3">ชำระเงินสำเร็จ</h4>
                <p class="text-muted mb-4" id="successMessage">บันทึกข้อมูลเรียบร้อยแล้ว</p>
                <div class="d-flex justify-content-center gap-3">
                    <button type="button" class="btn btn-outline-primary" onclick="printReceiptFromSuccess()">
                        <i class="bi bi-printer me-1"></i>พิมพ์ใบเสร็จ
                    </button>
                    <button type="button" class="btn btn-primary" onclick="closeSuccessAndReload()">
                        <i class="bi bi-check me-1"></i>ตกลง
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Notification Modal (Reusable) -->
<div class="modal fade" id="notifyModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-sm">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div class="mb-3">
                    <i id="notifyIcon" class="bi bi-check-circle-fill text-success" style="font-size: 3.5rem;"></i>
                </div>
                <h5 class="mb-2" id="notifyTitle">สำเร็จ</h5>
                <p class="text-muted mb-0" id="notifyMessage"></p>
            </div>
            <div class="modal-footer border-0 pt-0 justify-content-center">
                <button type="button" class="btn btn-primary px-4" onclick="closeNotifyModal()">ตกลง</button>
            </div>
        </div>
    </div>
</div>

<!-- Change Seller Modal -->
<div class="modal fade" id="changeSellerModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="changeSellerTitle"><i class="bi bi-person-badge me-2"></i>เลือกคนขาย</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p class="text-muted mb-3" style="font-size: 0.85rem;">เลือกพนักงานที่ขายคอร์สนี้ (เลือกได้หลายคน)</p>
                <div class="seller-list">
                    @foreach($salesStaff as $staff)
                    <div class="form-check mb-2">
                        <input class="form-check-input change-seller-checkbox" type="checkbox" value="{{ $staff->id }}" id="changeSeller_{{ $staff->id }}">
                        <label class="form-check-label" for="changeSeller_{{ $staff->id }}">
                            {{ $staff->name }}
                        </label>
                    </div>
                    @endforeach
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" onclick="confirmChangeSeller()">
                    <i class="bi bi-check-lg me-1"></i>ยืนยัน
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Course Options Modal -->
<div class="modal fade" id="courseOptionsModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-box-seam me-2"></i>ตัวเลือกการซื้อคอร์ส</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="selected_course_id">
                <input type="hidden" id="selected_course_name">
                <input type="hidden" id="selected_course_price">
                <input type="hidden" id="selected_course_sessions">

                <div class="mb-3">
                    <h6 class="fw-bold mb-2" id="courseOptionsTitle"></h6>
                    <p class="text-muted small mb-0" id="courseOptionsSubtitle"></p>
                </div>

                <!-- Step 1: Payment Type -->
                <div id="paymentTypeStep">
                    <h6 class="fw-bold text-muted mb-3">ขั้นตอนที่ 1: เลือกประเภทการชำระ</h6>
                    <div class="course-option-list">
                        <div class="course-option-item" onclick="selectPaymentType('full')">
                            <div class="option-icon bg-success bg-opacity-10">
                                <i class="bi bi-cash-stack text-success"></i>
                            </div>
                            <div class="option-content">
                                <div class="option-title">จ่ายเต็มจำนวน</div>
                                <div class="option-desc" id="fullPayDesc">ชำระเต็มจำนวน</div>
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </div>

                        <div class="course-option-item" onclick="selectPaymentType('installment')">
                            <div class="option-icon bg-info bg-opacity-10">
                                <i class="bi bi-calendar2-week text-info"></i>
                            </div>
                            <div class="option-content">
                                <div class="option-title">ผ่อนชำระ 3 งวด</div>
                                <div class="option-desc" id="installmentDesc">จ่ายงวดละ ฿X เมื่อมาใช้บริการ 3 ครั้งแรก</div>
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </div>
                    </div>
                </div>

                <!-- Step 2: Select Sellers -->
                <div id="sellerStep" style="display: none;">
                    <div class="d-flex align-items-center mb-3">
                        <button class="btn btn-sm btn-outline-secondary me-2" onclick="backToPaymentType()">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        <h6 class="fw-bold text-muted mb-0">ขั้นตอนที่ 2: เลือกคนขาย</h6>
                    </div>
                    <div class="alert alert-info py-2 mb-3" id="selectedPaymentInfo"></div>
                    <label class="form-label small">เลือกพนักงานที่ขายคอร์ส (เลือกได้หลายคน)</label>
                    <div class="seller-checkbox-list mb-3" style="max-height: 200px; overflow-y: auto;">
                        @foreach($salesStaff ?? [] as $staff)
                        <div class="form-check">
                            <input class="form-check-input seller-checkbox" type="checkbox" value="{{ $staff->id }}" id="seller_{{ $staff->id }}">
                            <label class="form-check-label" for="seller_{{ $staff->id }}">
                                {{ $staff->name }}
                            </label>
                        </div>
                        @endforeach
                    </div>
                    <button class="btn btn-primary w-100" onclick="proceedToUsageOption()">
                        <i class="bi bi-arrow-right me-2"></i>ถัดไป
                    </button>
                </div>

                <!-- Step 3: Usage Option -->
                <div id="usageOptionStep" style="display: none;">
                    <div class="d-flex align-items-center mb-3">
                        <button class="btn btn-sm btn-outline-secondary me-2" onclick="backToSellerStep()">
                            <i class="bi bi-arrow-left"></i>
                        </button>
                        <h6 class="fw-bold text-muted mb-0">ขั้นตอนที่ 3: เลือกการใช้คอร์ส</h6>
                    </div>
                    <div class="alert alert-info py-2 mb-3" id="selectedSellerInfo"></div>
                    <div class="course-option-list">
                        <!-- Option 1: Buy only (only show if has service in cart) -->
                        <div class="course-option-item" id="buyOnlyOption" onclick="selectCourseOption('buy_only')">
                            <div class="option-icon bg-primary bg-opacity-10">
                                <i class="bi bi-bag-plus text-primary"></i>
                            </div>
                            <div class="option-content">
                                <div class="option-title">ซื้อคอร์สอย่างเดียว</div>
                                <div class="option-desc">ยังไม่หักครั้งนี้ เก็บไว้ใช้ครั้งหน้า</div>
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </div>

                        <!-- Option 2: Buy and use now -->
                        <div class="course-option-item" onclick="selectCourseOption('buy_and_use')">
                            <div class="option-icon bg-success bg-opacity-10">
                                <i class="bi bi-check2-circle text-success"></i>
                            </div>
                            <div class="option-content">
                                <div class="option-title">ซื้อคอร์สและหักครั้งนี้ทันที</div>
                                <div class="option-desc">หักคอร์ส 1 ครั้งสำหรับการรักษาวันนี้</div>
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </div>

                        <!-- Option 3: Buy and retroactive (only if has previous treatment) -->
                        <div class="course-option-item" id="retroactiveOption" style="display: none;" onclick="selectCourseOption('retroactive')">
                            <div class="option-icon bg-warning bg-opacity-10">
                                <i class="bi bi-arrow-counterclockwise text-warning"></i>
                            </div>
                            <div class="option-content">
                                <div class="option-title">ซื้อคอร์สและตัดย้อนหลัง</div>
                                <div class="option-desc" id="retroactiveDesc">หักคอร์สแทนครั้งก่อนหน้า และคืนเงิน</div>
                            </div>
                            <i class="bi bi-chevron-right text-muted"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<style>
.course-option-list {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}
.course-option-item {
    display: flex;
    align-items: center;
    gap: 1rem;
    padding: 1rem;
    border: 1px solid #e2e8f0;
    border-radius: 10px;
    cursor: pointer;
    transition: all 0.2s;
    position: relative;
    z-index: 1;
}
.course-option-item:hover {
    border-color: #0284c7;
    background: #f0f9ff;
}
.option-icon {
    width: 48px;
    height: 48px;
    border-radius: 12px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-size: 1.25rem;
}
.option-content {
    flex: 1;
}
.option-title {
    font-weight: 600;
    margin-bottom: 0.25rem;
}
.option-desc {
    font-size: 0.75rem;
    color: #64748b;
}
</style>

@push('scripts')
<script>
// Base URL for API calls
const BASE_URL = '{{ url('/') }}';

// Global variables - Cart System
let currentPatientId = null;
let selectedFinalPayment = null;
let patientCourses = [];
let cartItems = []; // Array of {type, id, name, price, detail}
let lastCompletedAppointmentId = null;
let shouldReloadAfterNotify = false;
let isEditMode = false; // Flag for edit mode vs new payment

// Notification Modal Functions
function showNotify(type, title, message, reload = true) {
    const icon = document.getElementById('notifyIcon');
    const titleEl = document.getElementById('notifyTitle');
    const messageEl = document.getElementById('notifyMessage');

    // Set icon and color based on type
    if (type === 'success') {
        icon.className = 'bi bi-check-circle-fill text-success';
    } else if (type === 'error') {
        icon.className = 'bi bi-x-circle-fill text-danger';
    } else if (type === 'warning') {
        icon.className = 'bi bi-exclamation-circle-fill text-warning';
    } else if (type === 'info') {
        icon.className = 'bi bi-info-circle-fill text-primary';
    }
    icon.style.fontSize = '3.5rem';

    titleEl.textContent = title;
    messageEl.innerHTML = message;
    shouldReloadAfterNotify = reload;

    new bootstrap.Modal(document.getElementById('notifyModal')).show();
}

function closeNotifyModal() {
    bootstrap.Modal.getInstance(document.getElementById('notifyModal')).hide();
    if (shouldReloadAfterNotify) {
        location.reload();
    }
}

// Cart Functions
function showItemSelector(type) {
    // Hide all selectors
    document.querySelectorAll('.item-selector').forEach(el => el.classList.remove('active'));

    // Show selected selector
    if (type === 'service') {
        document.getElementById('serviceSelector').classList.add('active');
    } else if (type === 'course') {
        document.getElementById('courseSelector').classList.add('active');
    } else if (type === 'use_course') {
        document.getElementById('useCourseSelector').classList.add('active');
        loadPatientCourses();
    }
}

function addServiceToCart(id, name, price) {
    cartItems.push({
        type: 'service',
        id: id,
        name: name,
        price: price,
        detail: 'หัตถการ'
    });
    updateCartDisplay();
    hideSelectors();
}

// Show course options modal
let selectedPaymentType = 'full'; // full or installment

function showCourseOptions(id, name, price, sessions) {
    document.getElementById('selected_course_id').value = id;
    document.getElementById('selected_course_name').value = name;
    document.getElementById('selected_course_price').value = price;
    document.getElementById('selected_course_sessions').value = sessions;

    document.getElementById('courseOptionsTitle').textContent = name;
    document.getElementById('courseOptionsSubtitle').textContent = sessions + ' ครั้ง • ฿' + Number(price).toLocaleString();

    // Calculate installment amount
    const installmentAmount = Math.ceil(price / 3);
    document.getElementById('fullPayDesc').textContent = 'ชำระ ฿' + Number(price).toLocaleString() + ' วันนี้';
    document.getElementById('installmentDesc').textContent = 'จ่ายงวดละ ฿' + Number(installmentAmount).toLocaleString() + ' เมื่อมาใช้บริการ 3 ครั้งแรก';

    // Reset to step 1
    document.getElementById('paymentTypeStep').style.display = 'block';
    document.getElementById('usageOptionStep').style.display = 'none';
    selectedPaymentType = 'full';

    // Check if patient has previous treatment for retroactive
    const patientId = document.getElementById('payment_patient_id').value;
    fetch(BASE_URL + '/api/patient-last-treatment/' + patientId)
        .then(res => res.json())
        .then(data => {
            if (data.treatment) {
                document.getElementById('retroactiveOption').style.display = 'flex';
                document.getElementById('retroactiveDesc').textContent =
                    'หักแทน ' + data.treatment.service_name + ' (฿' + Number(data.treatment.price).toLocaleString() + ') วันที่ ' + data.treatment.date;
                window.lastTreatmentData = data.treatment;
            } else {
                document.getElementById('retroactiveOption').style.display = 'none';
                window.lastTreatmentData = null;
            }
        });

    new bootstrap.Modal(document.getElementById('courseOptionsModal')).show();
}

let selectedSellerIds = [];
let selectedSellerNames = [];

function selectPaymentType(type) {
    selectedPaymentType = type;
    const priceEl = document.getElementById('selected_course_price');
    const price = priceEl ? parseFloat(priceEl.value) : 0;
    const installmentAmount = Math.ceil(price / 3);

    // Update info display
    const infoEl = document.getElementById('selectedPaymentInfo');
    if (infoEl) {
        if (type === 'full') {
            infoEl.innerHTML = '<i class="bi bi-check-circle me-2"></i>จ่ายเต็มจำนวน ฿' + Number(price).toLocaleString();
        } else {
            infoEl.innerHTML = '<i class="bi bi-calendar2-week me-2"></i>ผ่อน 3 งวด • งวดละ ฿' + Number(installmentAmount).toLocaleString() + ' (จ่ายงวดแรกวันนี้)';
        }
    }

    // Clear previous seller selections
    document.querySelectorAll('.seller-checkbox').forEach(cb => cb.checked = false);
    selectedSellerIds = [];
    selectedSellerNames = [];

    // Show step 2 (seller selection)
    const paymentTypeStep = document.getElementById('paymentTypeStep');
    const sellerStep = document.getElementById('sellerStep');
    const usageOptionStep = document.getElementById('usageOptionStep');

    if (paymentTypeStep) paymentTypeStep.style.display = 'none';
    if (sellerStep) sellerStep.style.display = 'block';
    if (usageOptionStep) usageOptionStep.style.display = 'none';
}

function backToPaymentType() {
    document.getElementById('paymentTypeStep').style.display = 'block';
    document.getElementById('sellerStep').style.display = 'none';
    document.getElementById('usageOptionStep').style.display = 'none';
}

function proceedToUsageOption() {
    // Collect selected sellers
    selectedSellerIds = [];
    selectedSellerNames = [];
    document.querySelectorAll('.seller-checkbox:checked').forEach(cb => {
        selectedSellerIds.push(cb.value);
        selectedSellerNames.push(cb.nextElementSibling.textContent.trim());
    });

    if (selectedSellerIds.length === 0) {
        alert('กรุณาเลือกคนขายอย่างน้อย 1 คน');
        return;
    }

    // Get seller names for display
    let sellerNames = selectedSellerNames;

    const sellerInfoEl = document.getElementById('selectedSellerInfo');
    if (sellerInfoEl) {
        sellerInfoEl.innerHTML = '<i class="bi bi-people me-2"></i>คนขาย: ' + sellerNames.join(', ');
    }

    // Check if cart has any service items
    const hasServiceInCart = cartItems.some(item => item.type === 'service');
    const buyOnlyOption = document.getElementById('buyOnlyOption');
    if (buyOnlyOption) {
        // Show "ซื้อคอร์สอย่างเดียว" only if there's a service in cart
        buyOnlyOption.style.display = hasServiceInCart ? 'flex' : 'none';
    }

    // Show step 3
    const sellerStep = document.getElementById('sellerStep');
    const usageOptionStep = document.getElementById('usageOptionStep');
    if (sellerStep) sellerStep.style.display = 'none';
    if (usageOptionStep) usageOptionStep.style.display = 'block';
}

function backToSellerStep() {
    document.getElementById('sellerStep').style.display = 'block';
    document.getElementById('usageOptionStep').style.display = 'none';
}

// Select course option
function selectCourseOption(option) {
    const id = document.getElementById('selected_course_id').value;
    const name = document.getElementById('selected_course_name').value;
    const price = parseFloat(document.getElementById('selected_course_price').value);
    const sessions = parseInt(document.getElementById('selected_course_sessions').value);
    const installmentAmount = Math.ceil(price / 3);

    bootstrap.Modal.getInstance(document.getElementById('courseOptionsModal')).hide();

    // Build cart item based on payment type and option
    let cartItem = {
        id: id,
        name: name,
        total_sessions: sessions,
        payment_type: selectedPaymentType,
        installment_total: selectedPaymentType === 'installment' ? 3 : 0,
        installment_amount: selectedPaymentType === 'installment' ? installmentAmount : 0,
        seller_ids: selectedSellerIds,
        seller_names: selectedSellerNames
    };

    // Set price based on payment type (installment = first installment only)
    const chargePrice = selectedPaymentType === 'installment' ? installmentAmount : price;
    const paymentLabel = selectedPaymentType === 'installment' ? ' (ผ่อนงวด 1/3)' : '';

    if (option === 'buy_only') {
        // กรณี 1: ซื้อคอร์สอย่างเดียว (ไม่หักครั้งนี้)
        cartItems.push({
            ...cartItem,
            type: 'course',
            price: chargePrice,
            original_price: price,
            detail: sessions + ' ครั้ง (ไม่หักครั้งนี้)' + paymentLabel,
            use_now: false
        });
    } else if (option === 'buy_and_use') {
        // กรณี 2: ซื้อคอร์สและหักทันที
        cartItems.push({
            ...cartItem,
            type: 'course',
            price: chargePrice,
            original_price: price,
            detail: sessions + ' ครั้ง (หักครั้งนี้ทันที)' + paymentLabel,
            use_now: true
        });
    } else if (option === 'retroactive' && window.lastTreatmentData) {
        // กรณี 3: ซื้อคอร์สและตัดย้อนหลัง
        const refund = window.lastTreatmentData.price;
        const finalPrice = selectedPaymentType === 'installment' ? installmentAmount - refund : price - refund;
        cartItems.push({
            ...cartItem,
            type: 'course_retroactive',
            price: finalPrice,
            original_price: price,
            refund_amount: refund,
            detail: sessions + ' ครั้ง (ตัดย้อนหลัง -฿' + Number(refund).toLocaleString() + ')' + paymentLabel,
            use_now: true,
            retroactive_treatment_id: window.lastTreatmentData.id
        });
    }

    updateCartDisplay();
    hideSelectors();
}

function addCourseToCart(id, name, price, sessions) {
    cartItems.push({
        type: 'course',
        id: id,
        name: name,
        price: price,
        detail: sessions + ' ครั้ง'
    });
    updateCartDisplay();
    hideSelectors();
}

function addUseCourseToCart(purchaseId, name, remaining, paymentType = 'full', installmentPaid = 0, installmentTotal = 0, installmentAmount = 0, sellerIds = [], sellerNames = []) {
    // Check if already added
    if (cartItems.find(item => item.type === 'use_course' && item.id === purchaseId)) {
        alert('คอร์สนี้ถูกเพิ่มแล้ว');
        return;
    }

    // Get sessions used from input
    const sessionsInput = document.getElementById('sessions_' + purchaseId);
    const sessionsUsed = sessionsInput ? parseInt(sessionsInput.value) || 1 : 1;

    // Validate sessions
    if (sessionsUsed < 1 || sessionsUsed > remaining) {
        alert('จำนวนครั้งไม่ถูกต้อง (ต้องอยู่ระหว่าง 1 - ' + remaining + ' ครั้ง)');
        return;
    }

    // Check if need to pay installment
    let price = 0;
    let detail = 'ใช้ ' + sessionsUsed + ' ครั้ง (เหลือ ' + (remaining - sessionsUsed) + ' ครั้ง)';
    let hasInstallment = false;
    if (paymentType === 'installment' && installmentPaid < installmentTotal) {
        price = installmentAmount;
        const nextInstallment = installmentPaid + 1;
        detail = 'ใช้ ' + sessionsUsed + ' ครั้ง (เหลือ ' + (remaining - sessionsUsed) + ' ครั้ง) - ผ่อนงวด ' + nextInstallment + '/' + installmentTotal;
        hasInstallment = true;
    }

    cartItems.push({
        type: 'use_course',
        id: purchaseId,
        name: name,
        price: price,
        detail: detail,
        sessions_used: sessionsUsed,  // จำนวนครั้งที่ใช้
        has_installment: hasInstallment,  // Mark if mandatory installment
        installment_amount: installmentAmount,
        seller_ids: sellerIds,      // Original sellers from course purchase
        seller_names: sellerNames   // Original seller names
    });
    updateCartDisplay();
    hideSelectors();
}

function removeFromCart(index) {
    const item = cartItems[index];
    // Prevent removing items with mandatory installment payment
    if (item && item.has_installment) {
        alert('ไม่สามารถลบรายการนี้ได้ เนื่องจากต้องชำระค่างวดผ่อน');
        return;
    }
    cartItems.splice(index, 1);
    updateCartDisplay();
}

function hideSelectors() {
    document.querySelectorAll('.item-selector').forEach(el => el.classList.remove('active'));
}

function updateCartDisplay() {
    const container = document.getElementById('cartItems');
    const emptyCart = document.getElementById('emptyCart');

    if (cartItems.length === 0) {
        container.innerHTML = '<div class="empty-cart" id="emptyCart"><i class="bi bi-cart-x d-block"></i><span>ยังไม่มีรายการ</span></div>';
        document.getElementById('finalPaymentSection').style.display = 'none';
    } else {
        let html = '';
        cartItems.forEach((item, index) => {
            const typeIcon = item.type === 'service' ? 'bi-clipboard2-pulse' :
                           item.type === 'course' ? 'bi-box-seam' : 'bi-ticket-perforated';
            const typeColor = item.type === 'service' ? 'text-primary' :
                            item.type === 'course' ? 'text-warning' : 'text-success';

            // Show seller names if available (for courses)
            let sellerInfo = '';
            if (item.type === 'course' && item.seller_names && item.seller_names.length > 0) {
                sellerInfo = `<div class="cart-item-seller" style="font-size: 0.7rem; color: #6366f1;">
                    <i class="bi bi-person-badge me-1"></i>ขายโดย: ${item.seller_names.join(', ')}
                    <button type="button" class="btn btn-link btn-sm p-0 ms-2" style="font-size: 0.65rem;" onclick="changeSeller(${index})">
                        <i class="bi bi-pencil-square"></i> เปลี่ยน
                    </button>
                </div>`;
            } else if (item.type === 'course') {
                sellerInfo = `<div class="cart-item-seller" style="font-size: 0.7rem; color: #dc3545;">
                    <i class="bi bi-exclamation-triangle me-1"></i>ยังไม่ระบุคนขาย
                    <button type="button" class="btn btn-link btn-sm p-0 ms-2" style="font-size: 0.65rem;" onclick="changeSeller(${index})">
                        <i class="bi bi-plus-circle"></i> เพิ่ม
                    </button>
                </div>`;
            }

            html += `
                <div class="cart-item">
                    <div class="cart-item-info">
                        <div class="cart-item-name">
                            <i class="bi ${typeIcon} ${typeColor} me-1"></i>
                            ${item.name}
                        </div>
                        <div class="cart-item-detail">${item.detail}</div>
                        ${sellerInfo}
                    </div>
                    <span class="cart-item-price">${item.price > 0 ? '฿' + item.price.toLocaleString() : 'ตัดคอร์ส'}</span>
                    <i class="bi bi-x-circle cart-item-remove" onclick="removeFromCart(${index})"></i>
                </div>
            `;
        });
        container.innerHTML = html;

        // Show payment method if there's something to pay
        const total = calculateTotal();
        document.getElementById('finalPaymentSection').style.display = total > 0 ? 'block' : 'none';
    }

    updateCartTotals();
}

function calculateTotal() {
    let serviceTotal = 0;
    let courseTotal = 0;

    cartItems.forEach(item => {
        if (item.type === 'service') {
            serviceTotal += item.price || 0;
        } else if (item.type === 'course' || item.type === 'course_retroactive' || item.type === 'use_course') {
            courseTotal += item.price || 0;
        }
    });

    return serviceTotal + courseTotal;
}

function updateCartTotals() {
    let serviceTotal = 0;
    let courseTotal = 0;

    cartItems.forEach(item => {
        if (item.type === 'service') {
            serviceTotal += item.price || 0;
        } else if (item.type === 'course' || item.type === 'course_retroactive' || item.type === 'use_course') {
            courseTotal += item.price || 0;
        }
    });

    document.getElementById('totalServices').textContent = '฿' + serviceTotal.toLocaleString();
    document.getElementById('totalCourses').textContent = '฿' + courseTotal.toLocaleString();
    document.getElementById('grandTotal').textContent = '฿' + (serviceTotal + courseTotal).toLocaleString();
}

function loadPatientCourses() {
    if (!currentPatientId) {
        console.log('No patient ID');
        return;
    }

    console.log('Loading courses for patient:', currentPatientId);
    fetch(BASE_URL + '/api/patient-courses/' + currentPatientId)
        .then(res => res.json())
        .then(data => {
            console.log('Courses data:', data);
            const container = document.getElementById('existingCourseList');
            if (data.courses && data.courses.length > 0) {
                let html = '';
                data.courses.forEach(course => {
                    const remaining = course.total_sessions - course.used_sessions;
                    const paymentType = course.payment_type || 'full';
                    const installmentPaid = course.installment_paid || 0;
                    const installmentTotal = course.installment_total || 0;
                    const installmentAmount = course.installment_amount || 0;
                    const sellerIds = JSON.stringify(course.seller_ids || []);
                    const sellerNames = JSON.stringify(course.seller_names || []);

                    // Show installment badge
                    let installmentBadge = '';
                    if (paymentType === 'installment' && installmentPaid < installmentTotal) {
                        installmentBadge = `<span class="badge bg-warning text-dark ms-1" style="font-size: 0.6rem;">+฿${Number(installmentAmount).toLocaleString()}</span>`;
                    }

                    html += `
                        <div class="selectable-item" style="cursor: default; padding: 0.75rem;">
                            <div style="flex: 1;">
                                <span>${course.package_name}${installmentBadge}</span>
                                <small class="text-success d-block">เหลือ ${remaining}/${course.total_sessions} ครั้ง</small>
                            </div>
                            <div class="d-flex align-items-center gap-2">
                                <input type="number"
                                    id="sessions_${course.id}"
                                    class="form-control form-control-sm"
                                    value="1"
                                    min="1"
                                    max="${remaining}"
                                    style="width: 60px;"
                                    onclick="event.stopPropagation();">
                                <button class="btn btn-sm btn-success"
                                    onclick='event.stopPropagation(); addUseCourseToCart("${course.id}", "${course.package_name}", ${remaining}, "${paymentType}", ${installmentPaid}, ${installmentTotal}, ${installmentAmount}, ${sellerIds}, ${sellerNames})'>
                                    <i class="bi bi-check2"></i> ตัด
                                </button>
                            </div>
                        </div>
                    `;
                });
                container.innerHTML = html;
            } else {
                container.innerHTML = '<div class="text-muted text-center py-2" style="font-size: 0.75rem;">ไม่มีคอร์สที่ใช้ได้</div>';
            }
        })
        .catch(err => {
            document.getElementById('existingCourseList').innerHTML = '<div class="text-danger text-center py-2" style="font-size: 0.75rem;">โหลดข้อมูลล้มเหลว</div>';
        });
}

function selectFinalPayment(method) {
    selectedFinalPayment = method;
    document.querySelectorAll('.payment-btn').forEach(btn => btn.classList.remove('selected'));
    event.target.closest('.payment-btn').classList.add('selected');
    document.getElementById('final_payment_method').value = method;
}

function resetCart() {
    cartItems = [];
    selectedFinalPayment = null;
    document.querySelectorAll('.payment-btn').forEach(btn => btn.classList.remove('selected'));
    document.getElementById('final_payment_method').value = '';
    updateCartDisplay();
    hideSelectors();
}

// Change seller for a cart item
let editingSellerIndex = null;

function changeSeller(index) {
    editingSellerIndex = index;
    const item = cartItems[index];

    // Reset checkboxes
    document.querySelectorAll('.change-seller-checkbox').forEach(cb => cb.checked = false);

    // Pre-select current sellers if any
    if (item.seller_ids && item.seller_ids.length > 0) {
        item.seller_ids.forEach(id => {
            const cb = document.querySelector(`.change-seller-checkbox[value="${id}"]`);
            if (cb) cb.checked = true;
        });
    }

    document.getElementById('changeSellerTitle').textContent = 'เลือกคนขาย: ' + item.name;
    new bootstrap.Modal(document.getElementById('changeSellerModal')).show();
}

function confirmChangeSeller() {
    if (editingSellerIndex === null) return;

    const selectedIds = [];
    const selectedNames = [];

    document.querySelectorAll('.change-seller-checkbox:checked').forEach(cb => {
        selectedIds.push(cb.value);
        selectedNames.push(cb.nextElementSibling.textContent.trim());
    });

    if (selectedIds.length === 0) {
        alert('กรุณาเลือกคนขายอย่างน้อย 1 คน');
        return;
    }

    // Update cart item
    const index = editingSellerIndex;
    cartItems[index].seller_ids = selectedIds;
    cartItems[index].seller_names = selectedNames;

    console.log('Updated cart item', index, 'with sellers:', selectedNames, selectedIds);

    // Hide modal first, then update display
    const modal = bootstrap.Modal.getInstance(document.getElementById('changeSellerModal'));
    modal.hide();

    // Update display after modal animation
    setTimeout(() => {
        updateCartDisplay();
    }, 150);

    editingSellerIndex = null;
}

// Timer update
const timers = {};
@foreach($inTreatmentItems as $item)
    @if($item['started_at'])
        timers['{{ $item['id'] }}'] = new Date('{{ $item['started_at'] }}');
    @endif
@endforeach

function updateTimers() {
    const now = new Date();
    for (const [id, startTime] of Object.entries(timers)) {
        const diff = Math.floor((now - startTime) / 1000);
        const minutes = Math.floor(diff / 60);
        const seconds = diff % 60;
        const timerEl = document.getElementById('timer-' + id);
        if (timerEl) {
            timerEl.textContent = minutes + ':' + String(seconds).padStart(2, '0');
        }
    }
}

setInterval(updateTimers, 1000);

// Call Queue - change status to calling
function callQueue(id, name, queueNumber) {
    fetch(BASE_URL + '/appointments/' + id + '/status', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status: 'calling' })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Play sound or show notification
            announceQueue(queueNumber, name);
            showNotify('success', 'เรียกคิวสำเร็จ', 'เรียกคิวหมายเลข ' + queueNumber + '<br>' + name);
        } else {
            showNotify('error', 'เกิดข้อผิดพลาด', 'ไม่สามารถเรียกคิวได้');
        }
    });
}

// Recall Queue - announce again
function recallQueue(id, queueNumber) {
    announceQueue(queueNumber, '');
    showNotify('info', 'เรียกคิวอีกครั้ง', 'เรียกคิวหมายเลข ' + queueNumber + ' อีกครั้ง', false);
}

// Skip Queue - move back to waiting
function skipQueue(id, name) {
    if (!confirm('ข้ามคิว ' + name + ' กลับไปรอคิว?')) return;

    fetch(BASE_URL + '/appointments/' + id + '/status', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status: 'pending' })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotify('success', 'ข้ามคิวสำเร็จ', name + ' กลับไปรอคิวแล้ว');
        } else {
            showNotify('error', 'เกิดข้อผิดพลาด', 'ไม่สามารถข้ามคิวได้');
        }
    });
}

// Announce queue number (sound/speech)
function announceQueue(queueNumber, name) {
    // Use Web Speech API if available
    if ('speechSynthesis' in window) {
        const utterance = new SpeechSynthesisUtterance('เรียกคิวหมายเลข ' + queueNumber);
        utterance.lang = 'th-TH';
        utterance.rate = 0.9;
        speechSynthesis.speak(utterance);
    }
}

// Start Treatment
function startTreatment(id, name) {
    if (!confirm('เริ่มรักษา ' + name + ' ?')) return;

    fetch(BASE_URL + '/appointments/' + id + '/status', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status: 'confirmed' })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotify('success', 'เริ่มรักษาแล้ว', name + ' เข้ารับการรักษา');
        } else {
            showNotify('error', 'เกิดข้อผิดพลาด', 'ไม่สามารถเริ่มรักษาได้');
        }
    });
}

// Finish Treatment (stop timer, move to awaiting payment)
function finishTreatment(id, name, patientId, startedAt) {
    document.getElementById('finish_appointment_id').value = id;
    document.getElementById('finish_patient_id').value = patientId;
    document.getElementById('finish_patient_name').textContent = name;

    const startTime = new Date(startedAt);
    const endTime = new Date();
    const duration = Math.floor((endTime - startTime) / 1000 / 60);

    document.getElementById('finish_start_time').textContent = startTime.toLocaleTimeString('th-TH', {hour: '2-digit', minute:'2-digit'});
    document.getElementById('finish_end_time').textContent = endTime.toLocaleTimeString('th-TH', {hour: '2-digit', minute:'2-digit'});
    document.getElementById('finish_duration').textContent = duration + ' นาที';

    new bootstrap.Modal(document.getElementById('finishModal')).show();
}

// Confirm Finish Treatment
function confirmFinishTreatment() {
    const appointmentId = document.getElementById('finish_appointment_id').value;
    const patientId = document.getElementById('finish_patient_id').value;

    fetch(BASE_URL + '/queue/finish-treatment/' + appointmentId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ patient_id: patientId })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            bootstrap.Modal.getInstance(document.getElementById('finishModal')).hide();
            showNotify('success', 'รักษาเสร็จสิ้น', 'พร้อมรอชำระเงิน');
        } else {
            showNotify('error', 'เกิดข้อผิดพลาด', data.message || 'ไม่สามารถบันทึกได้');
        }
    });
}

// Show Payment Modal
function showPaymentModal(id, name, patientId, duration) {
    document.getElementById('payment_appointment_id').value = id;
    document.getElementById('payment_patient_id').value = patientId;
    document.getElementById('modal_patient_name').textContent = name;
    document.getElementById('modal_duration').textContent = duration + ' นาที';
    currentPatientId = patientId;
    isEditMode = false; // New payment, not edit

    // Reset cart system
    resetCart();

    // Reset form if exists
    const paymentForm = document.getElementById('paymentForm');
    if (paymentForm) paymentForm.reset();

    // Load patient courses for "use course" option
    loadPatientCourses();

    new bootstrap.Modal(document.getElementById('paymentModal')).show();
}

// Reset payment sections
function resetPaymentSections() {
    selectedPaymentMethod = null;
    selectedFinalPayment = null;
    currentServicePrice = 0;
    currentCoursePrice = 0;
    retroactiveAmount = 0;

    document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('selected'));
    document.getElementById('existingCourseSection').style.display = 'none';
    document.getElementById('sharedCourseSection').style.display = 'none';
    document.getElementById('buyCourseSection').style.display = 'none';
    document.getElementById('finalPaymentSection').style.display = 'none';
    document.getElementById('retroactiveSection').style.display = 'none';
    document.querySelectorAll('.course-option').forEach(el => el.classList.remove('selected'));
    document.querySelectorAll('.existing-course').forEach(el => el.classList.remove('selected'));

    updatePaymentSummary();
}

// Load patient's existing courses - uses global currentPatientId
// (duplicate function removed - using the one defined earlier)

// Load patient's last treatment
function loadPatientLastTreatment(patientId) {
    fetch(BASE_URL + '/api/patient-last-treatment/' + patientId)
        .then(res => res.json())
        .then(data => {
            lastTreatment = data.treatment || null;
        })
        .catch(err => {
            lastTreatment = null;
        });
}

// Render existing courses
function renderExistingCourses() {
    const container = document.getElementById('existingCourseList');
    if (patientCourses.length === 0) {
        container.innerHTML = '<div class="text-muted text-center py-2" style="font-size: 0.75rem;"><i class="bi bi-exclamation-circle me-1"></i>ไม่มีคอร์สที่ใช้ได้</div>';
        return;
    }

    let html = '';
    patientCourses.forEach(course => {
        // Check for pending installments
        let installmentBadge = '';
        if (course.payment_type === 'installment' && course.installment_paid < course.installment_total) {
            installmentBadge = `<span class="badge bg-warning text-dark ms-1" style="font-size: 0.6rem;">ผ่อนงวด ${course.installment_paid}/${course.installment_total} +฿${Number(course.installment_amount).toLocaleString()}</span>`;
        }

        html += `
            <div class="existing-course" onclick="selectExistingCourse('${course.id}')">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="course-name">${course.name}${installmentBadge}</div>
                        <div class="course-sessions">หมดอายุ: ${course.expiry_date || '-'}</div>
                    </div>
                    <div class="course-remaining">เหลือ ${course.remaining} ครั้ง</div>
                </div>
                <input type="radio" name="existing_course_id" value="${course.id}" style="display: none;">
            </div>
        `;
    });
    container.innerHTML = html;
}

// Render shared courses
function renderSharedCourses() {
    const container = document.getElementById('sharedCourseList');
    if (!container) return; // Element doesn't exist in this mode

    if (patientSharedCourses.length === 0) {
        container.innerHTML = '<div class="text-muted text-center py-2" style="font-size: 0.75rem;"><i class="bi bi-exclamation-circle me-1"></i>ไม่มีคอร์สร่วมที่ใช้ได้</div>';
        return;
    }

    let html = '';
    patientSharedCourses.forEach(course => {
        html += `
            <div class="existing-course" onclick="selectSharedCourse('${course.id}')">
                <div class="d-flex justify-content-between">
                    <div>
                        <div class="course-name">${course.name}</div>
                        <div class="course-sessions">เจ้าของ: ${course.owner_name}</div>
                    </div>
                    <div class="course-remaining">เหลือ ${course.remaining} ครั้ง</div>
                </div>
                <input type="radio" name="shared_course_id" value="${course.id}" style="display: none;">
            </div>
        `;
    });
    container.innerHTML = html;
}

// Update service price
function updateServicePrice() {
    const select = document.getElementById('service_id');
    const option = select.options[select.selectedIndex];
    currentServicePrice = parseFloat(option.dataset.price) || 0;
    updatePaymentSummary();
}

// Select payment method
function selectPaymentMethod(method) {
    selectedPaymentMethod = method;
    document.querySelectorAll('.payment-option').forEach(el => el.classList.remove('selected'));
    event.currentTarget.classList.add('selected');

    document.getElementById('existingCourseSection').style.display = 'none';
    document.getElementById('sharedCourseSection').style.display = 'none';
    document.getElementById('buyCourseSection').style.display = 'none';
    document.getElementById('retroactiveSection').style.display = 'none';

    if (method === 'cash') {
        document.getElementById('finalPaymentSection').style.display = 'block';
        currentCoursePrice = 0;
    } else if (method === 'use_course') {
        document.getElementById('existingCourseSection').style.display = 'block';
        document.getElementById('finalPaymentSection').style.display = 'none';
        currentCoursePrice = 0;
        currentServicePrice = 0;
    } else if (method === 'shared_course') {
        document.getElementById('sharedCourseSection').style.display = 'block';
        document.getElementById('finalPaymentSection').style.display = 'none';
        currentCoursePrice = 0;
        currentServicePrice = 0;
    } else if (method === 'buy_course') {
        document.getElementById('buyCourseSection').style.display = 'block';
        document.getElementById('finalPaymentSection').style.display = 'block';
        currentServicePrice = 0;
    }

    updatePaymentSummary();
}

// Select existing course
function selectExistingCourse(courseId) {
    document.querySelectorAll('#existingCourseList .existing-course').forEach(el => el.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
    event.currentTarget.querySelector('input[type=radio]').checked = true;
    updatePaymentSummary();
}

// Select shared course
function selectSharedCourse(courseId) {
    document.querySelectorAll('#sharedCourseList .existing-course').forEach(el => el.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
    event.currentTarget.querySelector('input[type=radio]').checked = true;
    updatePaymentSummary();
}

// Select new course
function selectNewCourse(packageId, price, allowRetroactive) {
    document.querySelectorAll('.course-option').forEach(el => el.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
    event.currentTarget.querySelector('input[type=radio]').checked = true;

    currentCoursePrice = price;

    if (allowRetroactive && lastTreatment) {
        document.getElementById('retroactiveSection').style.display = 'block';
        document.getElementById('retroactiveInfo').textContent =
            `หัก ${lastTreatment.service_name} ฿${lastTreatment.price.toLocaleString()} (${lastTreatment.date})`;
        retroactiveAmount = lastTreatment.price;
    } else {
        document.getElementById('retroactiveSection').style.display = 'none';
        retroactiveAmount = 0;
    }

    updatePaymentSummary();
}

// Select final payment
function selectFinalPayment(method) {
    selectedFinalPayment = method;
    document.querySelectorAll('.payment-btn').forEach(el => el.classList.remove('selected'));
    event.currentTarget.classList.add('selected');
    document.getElementById('final_payment_method').value = method;
}

// Update payment summary
function updatePaymentSummary() {
    let servicePrice = currentServicePrice;
    let coursePrice = currentCoursePrice;
    let retroactive = 0;
    let total = 0;

    if (selectedPaymentMethod === 'cash') {
        total = servicePrice;
        document.getElementById('summaryCourseRow').style.display = 'none';
        document.getElementById('summaryRetroactiveRow').style.display = 'none';
    } else if (selectedPaymentMethod === 'use_course' || selectedPaymentMethod === 'shared_course') {
        servicePrice = 0;
        total = 0;
        document.getElementById('summaryCourseRow').style.display = 'none';
        document.getElementById('summaryRetroactiveRow').style.display = 'none';
    } else if (selectedPaymentMethod === 'buy_course') {
        servicePrice = 0;
        document.getElementById('summaryCourseRow').style.display = 'flex';

        if (document.getElementById('use_retroactive') && document.getElementById('use_retroactive').checked) {
            retroactive = retroactiveAmount;
            document.getElementById('summaryRetroactiveRow').style.display = 'flex';
            document.getElementById('retroactiveDetails').style.display = 'block';
        } else {
            document.getElementById('summaryRetroactiveRow').style.display = 'none';
            if (document.getElementById('retroactiveDetails')) {
                document.getElementById('retroactiveDetails').style.display = 'none';
            }
        }

        total = coursePrice - retroactive;
    }

    document.getElementById('summaryServicePrice').textContent = '฿' + servicePrice.toLocaleString();
    document.getElementById('summaryCoursePrice').textContent = '฿' + coursePrice.toLocaleString();
    document.getElementById('summaryRetroactive').textContent = '-฿' + retroactive.toLocaleString();
    document.getElementById('summaryTotal').textContent = '฿' + Math.max(0, total).toLocaleString();
}

// Submit payment with cart system
function submitPayment() {
    const appointmentId = document.getElementById('payment_appointment_id').value;
    const patientId = document.getElementById('payment_patient_id').value;
    const ptId = document.getElementById('pt_id').value;

    if (!ptId) {
        alert('กรุณาเลือกนักกายภาพ');
        return;
    }

    // Edit mode: only update PT and sellers
    if (isEditMode) {
        const data = {
            pt_id: ptId,
            cart_items: cartItems,
            payment_method: selectedFinalPayment || 'none'
        };

        fetch(BASE_URL + '/api/update-payment/' + appointmentId, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify(data)
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
                showNotify('success', 'อัพเดทสำเร็จ', result.message);
                isEditMode = false; // Reset flag
            } else {
                alert('เกิดข้อผิดพลาด: ' + result.message);
            }
        })
        .catch(err => {
            alert('เกิดข้อผิดพลาด: ' + err.message);
        });
        return;
    }

    // New payment mode
    if (cartItems.length === 0) {
        alert('กรุณาเพิ่มรายการสินค้าหรือบริการ');
        return;
    }

    const total = calculateTotal();
    if (total > 0 && !selectedFinalPayment) {
        alert('กรุณาเลือกวิธีชำระเงิน');
        return;
    }

    const data = {
        pt_id: ptId,
        patient_id: patientId,
        cart_items: cartItems,
        payment_method: selectedFinalPayment || 'none',
        total_amount: total
    };

    fetch(BASE_URL + '/queue/process-payment/' + appointmentId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(result => {
        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
            lastCompletedAppointmentId = appointmentId;

            // Show success modal
            const total = calculateTotal();
            document.getElementById('successMessage').innerHTML =
                'ยอดชำระ <strong>' + total.toLocaleString() + ' บาท</strong><br>บันทึกข้อมูลเรียบร้อยแล้ว';
            new bootstrap.Modal(document.getElementById('successModal')).show();
        } else {
            alert('เกิดข้อผิดพลาด: ' + (result.message || 'Unknown error'));
        }
    })
    .catch(err => {
        alert('เกิดข้อผิดพลาด: ' + err.message);
    });
}

function printReceiptFromSuccess() {
    if (lastCompletedAppointmentId) {
        window.open('/queue/' + lastCompletedAppointmentId + '/receipt', '_blank');
    }
}

function closeSuccessAndReload() {
    bootstrap.Modal.getInstance(document.getElementById('successModal')).hide();
    location.reload();
}

// Legacy function kept for compatibility
function submitPaymentLegacy() {
    const appointmentId = document.getElementById('payment_appointment_id').value;
    const patientId = document.getElementById('payment_patient_id').value;
    const serviceId = document.getElementById('service_id') ? document.getElementById('service_id').value : null;
    const ptId = document.getElementById('pt_id') ? document.getElementById('pt_id').value : null;
    const notes = document.getElementById('treatment_notes') ? document.getElementById('treatment_notes').value : '';

    if (!serviceId || !ptId) {
        alert('กรุณาเลือกหัตถการและนักกายภาพ');
        return;
    }

    if (!selectedPaymentMethod) {
        alert('กรุณาเลือกวิธีชำระเงิน');
        return;
    }

    const data = {
        service_id: serviceId,
        pt_id: ptId,
        treatment_notes: notes,
        patient_id: patientId,
        payment_method: selectedPaymentMethod
    };

    if (selectedPaymentMethod === 'cash') {
        if (!selectedFinalPayment) {
            alert('กรุณาเลือกวิธีจ่ายเงิน');
            return;
        }
        data.final_payment = selectedFinalPayment;
        data.amount = currentServicePrice;
    } else if (selectedPaymentMethod === 'use_course') {
        const selectedCourse = document.querySelector('input[name="existing_course_id"]:checked');
        if (!selectedCourse) {
            alert('กรุณาเลือกคอร์สที่ต้องการใช้');
            return;
        }
        data.course_purchase_id = selectedCourse.value;
    } else if (selectedPaymentMethod === 'shared_course') {
        const selectedCourse = document.querySelector('input[name="shared_course_id"]:checked');
        if (!selectedCourse) {
            alert('กรุณาเลือกคอร์สร่วมที่ต้องการใช้');
            return;
        }
        data.shared_course_id = selectedCourse.value;
    } else if (selectedPaymentMethod === 'buy_course') {
        const selectedPackage = document.querySelector('input[name="package_id"]:checked');
        if (!selectedPackage) {
            alert('กรุณาเลือกคอร์สที่ต้องการซื้อ');
            return;
        }
        if (!selectedFinalPayment) {
            alert('กรุณาเลือกวิธีจ่ายเงิน');
            return;
        }
        data.package_id = selectedPackage.value;
        data.final_payment = selectedFinalPayment;
        data.use_retroactive = document.getElementById('use_retroactive') && document.getElementById('use_retroactive').checked;
        data.retroactive_amount = data.use_retroactive ? retroactiveAmount : 0;
        data.amount = currentCoursePrice - data.retroactive_amount;
    }

    fetch(BASE_URL + '/queue/process-payment/' + appointmentId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(data)
    })
    .then(res => res.json())
    .then(result => {
        if (result.success) {
            bootstrap.Modal.getInstance(document.getElementById('paymentModal')).hide();
            alert('ชำระเงินเรียบร้อย!');
            location.reload();
        } else {
            alert('เกิดข้อผิดพลาด: ' + (result.message || 'Unknown error'));
        }
    })
    .catch(err => {
        alert('เกิดข้อผิดพลาด: ' + err.message);
    });
}

// Auto refresh every 60 seconds
setInterval(function() {
    location.reload();
}, 60000);

// Cancel Appointment (for appointments not in Queue table)
function cancelAppointment(id, name) {
    if (!confirm('ยกเลิกนัดหมาย ' + name + ' ?\n\nนัดหมายจะถูกเปลี่ยนสถานะเป็น "ยกเลิก"')) return;

    fetch(BASE_URL + '/appointments/' + id + '/cancel', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotify('success', 'ยกเลิกนัดหมาย', data.message || 'ยกเลิกนัดหมายสำเร็จ');
        } else {
            showNotify('error', 'เกิดข้อผิดพลาด', data.message || 'ไม่สามารถยกเลิกได้');
        }
    })
    .catch(err => {
        showNotify('error', 'เกิดข้อผิดพลาด', err.message);
    });
}

// Cancel Queue (waiting status) - delete everything if temporary patient
function cancelQueue(id, name) {
    if (!confirm('ยกเลิกคิว ' + name + ' ?\n\nหากเป็นลูกค้าใหม่ที่ยังไม่เคยรักษา ข้อมูลจะถูกลบออกทั้งหมด')) return;

    fetch(BASE_URL + '/queue/' +id + '/cancel', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotify('success', 'ยกเลิกคิว', data.message);
        } else {
            showNotify('error', 'เกิดข้อผิดพลาด', data.message || 'ไม่สามารถยกเลิกได้');
        }
    })
    .catch(err => {
        showNotify('error', 'เกิดข้อผิดพลาด', err.message);
    });
}

// Cancel Treatment - revert from in_treatment to waiting (for Queue model)
function cancelTreatment(id, name) {
    if (!confirm('ยกเลิกการรักษา ' + name + ' กลับไปสถานะรอคิว?\n\nหากเป็นลูกค้าใหม่ HN และ OPD จะถูกลบออก')) return;

    fetch(BASE_URL + '/queue/' +id + '/cancel-treatment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotify('success', 'ยกเลิกการรักษา', data.message);
        } else {
            showNotify('error', 'เกิดข้อผิดพลาด', data.message || 'ไม่สามารถยกเลิกได้');
        }
    })
    .catch(err => {
        showNotify('error', 'เกิดข้อผิดพลาด', err.message);
    });
}

// Cancel Treatment for Appointment - revert from confirmed to pending
function cancelTreatmentAppointment(id, name) {
    if (!confirm('ยกเลิกการรักษา ' + name + ' กลับไปสถานะรอคิว?')) return;

    fetch(BASE_URL + '/appointments/' + id + '/status', {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status: 'pending' })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotify('success', 'ยกเลิกการรักษา', 'กลับไปสถานะรอคิวแล้ว');
        } else {
            showNotify('error', 'เกิดข้อผิดพลาด', data.message || 'ไม่สามารถยกเลิกได้');
        }
    })
    .catch(err => {
        showNotify('error', 'เกิดข้อผิดพลาด', err.message);
    });
}

// Cancel Payment - revert from awaiting_payment to in_treatment (confirmed)
function cancelPayment(id, name) {
    if (!confirm('ยกเลิกการชำระเงิน ' + name + ' กลับไปสถานะกำลังรักษา?\n\nข้อมูล Treatment และ OPD จะถูกลบออก (หากเป็นลูกค้าใหม่)')) return;

    fetch(BASE_URL + '/queue/' +id + '/cancel-payment', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotify('success', 'ยกเลิกการชำระเงิน', data.message);
        } else {
            showNotify('error', 'เกิดข้อผิดพลาด', data.message || 'ไม่สามารถยกเลิกได้');
        }
    })
    .catch(err => {
        showNotify('error', 'เกิดข้อผิดพลาด', err.message);
    });
}

// Print receipt for completed appointment
function printReceipt(appointmentId) {
    window.open('/queue/' + appointmentId + '/receipt', '_blank');
}

// Edit completed payment - load existing invoice data and show modal
function editCompletedPayment(appointmentId, patientName, patientId) {
    // Set basic info
    document.getElementById('payment_appointment_id').value = appointmentId;
    document.getElementById('payment_patient_id').value = patientId;
    document.getElementById('modal_patient_name').textContent = patientName + ' (แก้ไข)';
    document.getElementById('modal_duration').textContent = '-';
    currentPatientId = patientId;
    isEditMode = true; // Set edit mode flag

    // Reset cart
    resetCart();

    // Fetch existing invoice data
    fetch(BASE_URL + '/api/appointment-invoice/' + appointmentId)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                // Set PT (นักกายภาพที่ทำการรักษา)
                if (data.treatment && data.treatment.pt_id) {
                    const ptSelect = document.getElementById('pt_id');
                    if (ptSelect) {
                        ptSelect.value = data.treatment.pt_id;
                    }
                }

                // Load existing items into cart from invoice items
                if (data.items && data.items.length > 0) {
                    data.items.forEach(item => {
                        let itemType = 'service';
                        let detail = 'หัตถการ';
                        let sellerNames = [];

                        let sellerIds = [];
                        if (item.item_type === 'course_package') {
                            itemType = 'course';
                            detail = 'คอร์ส';
                            // Find seller names from purchased_courses by matching package name
                            if (data.purchased_courses && data.purchased_courses.length > 0) {
                                const matchingCourse = data.purchased_courses.find(c =>
                                    c.package_name == item.description ||
                                    c.package_id == item.item_id ||
                                    c.id == item.item_id
                                );
                                if (matchingCourse) {
                                    if (matchingCourse.seller_names) {
                                        sellerNames = matchingCourse.seller_names;
                                    }
                                    if (matchingCourse.seller_ids) {
                                        sellerIds = matchingCourse.seller_ids;
                                    }
                                }
                            }
                        } else if (item.item_type === 'course_installment') {
                            itemType = 'use_course';
                            detail = 'ตัดคอร์ส (ผ่อน)';
                        }

                        cartItems.push({
                            type: itemType,
                            id: item.item_id,
                            name: item.description,
                            price: parseFloat(item.total_amount),
                            detail: detail,
                            seller_ids: sellerIds,
                            seller_names: sellerNames
                        });
                    });
                }

                // If used course but no invoice item for it (free usage), add to cart
                if (data.course_purchase && !data.items.some(i => i.item_type === 'course_installment')) {
                    // Check if this was a use_course that was free
                    if (data.treatment.course_purchase_id) {
                        cartItems.push({
                            type: 'use_course',
                            id: data.treatment.course_purchase_id,
                            name: data.course_purchase.package_name,
                            price: 0,
                            detail: 'ตัดคอร์ส',
                            seller_ids: data.course_purchase.seller_ids || [],
                            seller_names: data.course_purchase.seller_names || []
                        });
                    }
                }

                updateCartDisplay();

                // Set payment method
                if (data.invoice && data.invoice.notes) {
                    const method = data.invoice.notes.replace('Payment method: ', '');
                    selectedFinalPayment = method;
                    document.querySelectorAll('.final-payment-btn').forEach(btn => {
                        if (btn.dataset.method === method) {
                            btn.classList.add('selected');
                        }
                    });
                }
            }

            // Load patient courses
            loadPatientCourses();

            // Show modal
            new bootstrap.Modal(document.getElementById('paymentModal')).show();
        })
        .catch(err => {
            console.error('Error loading invoice:', err);
            // Still show modal even if no invoice found
            loadPatientCourses();
            new bootstrap.Modal(document.getElementById('paymentModal')).show();
        });
}

function revertComplete(id, name) {
    if (!confirm('ย้อนกลับ ' + name + ' ไปสถานะรอชำระเงิน?')) return;

    fetch(BASE_URL + '/queue/' +id + '/revert-complete', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            showNotify('success', 'ย้อนกลับสำเร็จ', data.message);
        } else {
            showNotify('error', 'เกิดข้อผิดพลาด', data.message || 'ไม่สามารถย้อนกลับได้');
        }
    })
    .catch(err => {
        showNotify('error', 'เกิดข้อผิดพลาด', err.message);
    });
}
</script>
@endpush
@endsection
