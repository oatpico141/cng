@extends('layouts.app')

@section('title', 'ข้อมูลลูกค้า - GCMS')

@push('styles')
<style>
    /* MINIMAL PATIENT SHOW - Clean & Simple */

    /* Patient Header */
    .patient-header-card {
        background: linear-gradient(135deg, #0284c7, #0ea5e9);
        border-radius: 12px;
        padding: 1.5rem;
        color: #fff;
    }

    .patient-header-card h2 { color: #fff !important; font-size: 1.5rem !important; margin-bottom: 0.25rem !important; }
    .patient-header-card p, .patient-header-card span { color: #fff !important; }

    .patient-avatar-large {
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: #fff;
        color: #0369a1;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        font-weight: 600;
    }

    /* Tab Navigation - Simple */
    .nav-tabs-custom {
        border-bottom: 1px solid #e2e8f0;
        margin-bottom: 1.5rem;
        background: #f8fafc;
        padding: 0.5rem;
        border-radius: 8px;
    }

    .nav-tabs-custom .nav-link {
        color: #64748b;
        border: none;
        padding: 0.75rem 1rem;
        font-weight: 500;
        font-size: 0.875rem;
        border-radius: 6px;
        margin-right: 0.25rem;
    }

    .nav-tabs-custom .nav-link:hover {
        color: #0369a1;
        background: #fff;
    }

    .nav-tabs-custom .nav-link.active {
        color: #0369a1;
        background: #fff;
        font-weight: 600;
        box-shadow: 0 1px 3px rgba(0,0,0,0.1);
    }

    /* Cards - Minimal */
    .card {
        border: 1px solid #e2e8f0 !important;
        border-radius: 8px !important;
        box-shadow: none !important;
    }

    .card-header {
        background: #f8fafc !important;
        border-bottom: 1px solid #e2e8f0 !important;
        padding: 0.75rem 1rem !important;
        font-size: 0.9rem !important;
        font-weight: 600 !important;
        color: #334155 !important;
    }

    .card-body {
        padding: 1rem !important;
    }

    /* Table - Clean */
    .table { font-size: 0.875rem !important; }
    .table th {
        background: #f8fafc !important;
        color: #475569 !important;
        font-weight: 600 !important;
        padding: 0.75rem !important;
        font-size: 0.8rem !important;
        border-bottom: 1px solid #e2e8f0 !important;
    }
    .table td {
        padding: 0.75rem !important;
        color: #334155 !important;
        border-bottom: 1px solid #f1f5f9 !important;
    }

    /* Next Appointment - Simplified */
    .next-appointment-card {
        background: #eff6ff;
        border: 1px solid #bfdbfe !important;
        border-radius: 8px !important;
    }

    .next-appointment-card .card-body {
        padding: 1rem !important;
    }

    /* Allergy Card - Simple Red */
    .critical-allergy-card {
        border: 1px solid #fecaca !important;
        background: #fef2f2 !important;
        border-radius: 8px !important;
    }

    .critical-allergy-header {
        background: #ef4444;
        padding: 0.5rem 1rem;
        color: #fff;
        font-size: 0.875rem;
    }

    /* Info Grid */
    .info-label {
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 500;
        margin-bottom: 2px;
    }

    .info-value {
        font-size: 0.875rem;
        color: #1e293b;
        font-weight: 500;
    }

    /* Buttons - Smaller */
    .btn {
        font-size: 0.8rem !important;
        padding: 0.5rem 0.75rem !important;
        border-radius: 6px !important;
    }

    .btn-sm {
        padding: 0.375rem 0.5rem !important;
        font-size: 0.75rem !important;
    }

    /* Badge */
    .badge {
        font-size: 0.7rem !important;
        padding: 0.25rem 0.5rem !important;
    }

    /* Alert Badge */
    .alert-badge {
        background: #fef2f2;
        border: 1px solid #fecaca;
        color: #dc2626;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        display: inline-flex;
        align-items: center;
        gap: 4px;
    }

    /* Mobile */
    @media (max-width: 768px) {
        .nav-tabs-custom { overflow-x: auto; flex-wrap: nowrap; }
        .nav-tabs-custom .nav-link { white-space: nowrap; padding: 0.5rem 0.75rem; font-size: 0.8rem; }
    }

    .btn-icon {
        width: 32px;
        height: 32px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 1.1rem;
        border: 2px solid #bae6fd;
        background: #ffffff;
        transition: all 0.2s ease;
        cursor: pointer;
    }

    .btn-icon:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
    }

    .btn-icon.btn-view {
        color: #0284c7;
        border-color: #7dd3fc;
        background: #f0f9ff;
    }

    .btn-icon.btn-view:hover {
        background: #e0f2fe;
        border-color: #38bdf8;
    }

    .btn-icon.btn-edit {
        color: #f59e0b;
        border-color: #fed7aa;
        background: #fffbeb;
    }

    .btn-icon.btn-edit:hover {
        background: #fef3c7;
        border-color: #fbbf24;
    }

    .btn-icon.btn-delete {
        color: #dc2626;
        border-color: #fecaca;
        background: #fef2f2;
    }

    .btn-icon.btn-delete:hover {
        background: #fee2e2;
        border-color: #f87171;
    }

    /* Timeline Styles - Clean & Simple */
    .timeline {
        position: relative;
        padding: 0;
    }

    .timeline::before {
        content: '';
        position: absolute;
        left: 8px;
        top: 0;
        bottom: 0;
        width: 2px;
        background: #e2e8f0;
    }

    .timeline-item {
        position: relative;
        padding-left: 32px;
        margin-bottom: 1rem;
    }

    .timeline-icon {
        position: absolute;
        left: 0;
        top: 4px;
        width: 18px;
        height: 18px;
        border-radius: 50%;
        background: #fff;
        border: 2px solid #0ea5e9;
        z-index: 1;
    }

    .timeline-icon.success {
        border-color: #10b981;
        background: #ecfdf5;
    }

    .timeline-icon.warning {
        border-color: #f59e0b;
        background: #fffbeb;
    }

    .timeline-content {
        background: #fff;
        border-radius: 8px;
        padding: 0.75rem 1rem;
        border: 1px solid #e2e8f0;
    }

    .timeline-content h6 {
        font-size: 0.875rem !important;
        color: #1e293b !important;
        margin-bottom: 0.25rem !important;
    }

    .timeline-content p {
        font-size: 0.8rem !important;
        margin-bottom: 0.5rem !important;
    }

    .timeline-content .bg-light {
        background: #f8fafc !important;
        padding: 0.5rem !important;
        font-size: 0.75rem;
    }

    /* Info Cards - Thin Design with Strong Colors */
    .info-card {
        background: var(--white);
        border-radius: 10px;
        padding: 1rem;
        border: 1px solid var(--gray-100);
        margin-bottom: 0.75rem;
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02);
    }

    .info-card-header {
        font-weight: 600;
        color: var(--calm-blue-700);
        margin-bottom: 0.75rem;
        padding-bottom: 0.5rem;
        border-bottom: 1px solid var(--calm-blue-100);
    }

    /* Course Cards */
    .course-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        border: 1px solid #e5e7eb;
        transition: all 0.2s ease;
    }

    .course-card:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
        transform: translateY(-2px);
    }

    .progress-custom {
        height: 8px;
        background: #e5e7eb;
        border-radius: 10px;
        overflow: hidden;
    }

    .progress-bar-custom {
        background: linear-gradient(90deg, #10b981 0%, #059669 100%);
        height: 100%;
        transition: width 0.3s ease;
    }

    /* Financial Summary Cards */
    .financial-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        border-left: 4px solid;
        transition: transform 0.2s;
    }

    .financial-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08);
    }

    .financial-card.income { border-left-color: #10b981; }
    .financial-card.expense { border-left-color: #ef4444; }
    .financial-card.balance { border-left-color: #0284c7; }

    /* Note Card */
    .note-card {
        background: #fef3c7;
        border: 1px solid #fbbf24;
        border-radius: 12px;
        padding: 1rem;
        margin-bottom: 1rem;
    }

    .note-card .note-date {
        font-size: 0.85rem;
        color: #92400e;
        font-weight: 600;
    }

    .note-card .note-author {
        color: #78350f;
        font-size: 0.9rem;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .patient-avatar-large {
            width: 80px;
            height: 80px;
            font-size: 2rem;
        }

        .nav-tabs-custom .nav-link {
            padding: 0.75rem 1rem;
            font-size: 0.9rem;
        }

        .timeline-item {
            padding-left: 50px;
        }

        .timeline::before {
            left: 20px;
        }

        .timeline-icon {
            left: 10px;
        }
    }

    /* Global Overrides for Ultra Thin Layout */
    .card {
        box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02) !important;
        border: 1px solid var(--gray-100) !important;
    }

    .card-body {
        padding: 1rem !important;
    }

    .card-header {
        padding: 0.75rem 1rem !important;
        font-weight: 600 !important;
    }

    /* Reduce spacing globally */
    .mb-4 { margin-bottom: 1.5rem !important; }
    .mb-3 { margin-bottom: 1rem !important; }
    .p-4 { padding: 1rem !important; }
    .shadow-sm { box-shadow: 0 1px 2px rgba(0, 0, 0, 0.02) !important; }
    .shadow-lg { box-shadow: 0 2px 4px rgba(0, 0, 0, 0.04) !important; }

    /* Additional Readability Enhancements */

    /* Larger Text in Lists */
    .list-group-item {
        padding: 1rem 1.25rem !important;
        font-size: 1rem !important;
        border: 1px solid #e0f2fe !important;
        background: #ffffff !important;
    }

    .list-group-item:hover {
        background: #f0f9ff !important;
    }

    /* Better Spacing for Forms */
    .form-control, .form-select {
        font-size: 1rem !important;
        padding: 0.75rem 1rem !important;
        border: 2px solid #bae6fd !important;
        border-radius: 10px !important;
    }

    .form-control:focus, .form-select:focus {
        border-color: #0ea5e9 !important;
        box-shadow: 0 0 0 0.2rem rgba(14, 165, 233, 0.25) !important;
    }

    /* Enhanced Timeline */
    .timeline-date {
        color: #0369a1 !important;
        font-weight: 700 !important;
        font-size: 0.9rem !important;
        margin-bottom: 0.5rem !important;
    }

    /* Better OPD Record Display */
    .opd-record {
        background: #ffffff !important;
        border: 2px solid #e0f2fe !important;
        border-radius: 12px !important;
        padding: 1.25rem !important;
        margin-bottom: 1rem !important;
    }

    .opd-record:hover {
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.08) !important;
        border-color: #7dd3fc !important;
    }

    /* Enhanced Data Display */
    .data-row {
        padding: 0.75rem 0 !important;
        border-bottom: 1px solid #f1f5f9 !important;
    }

    .data-label {
        color: #0369a1 !important;
        font-weight: 600 !important;
        font-size: 0.95rem !important;
    }

    .data-value {
        color: #1e293b !important;
        font-size: 1rem !important;
        font-weight: 500 !important;
    }

    /* Enhanced Alert Boxes */
    .alert {
        padding: 1.25rem !important;
        font-size: 1rem !important;
        border-radius: 12px !important;
        border-width: 2px !important;
    }

    .alert-info {
        background: #f0f9ff !important;
        border-color: #7dd3fc !important;
        color: #0369a1 !important;
    }

    .alert-success {
        background: #f0fdf4 !important;
        border-color: #86efac !important;
        color: #14532d !important;
    }

    .alert-warning {
        background: #fef3c7 !important;
        border-color: #fde047 !important;
        color: #713f12 !important;
    }

    .alert-danger {
        background: #fef2f2 !important;
        border-color: #fca5a5 !important;
        color: #7f1d1d !important;
    }

    /* Make Icons Bigger */
    .bi {
        font-size: 1.1em !important;
    }

    /* Enhanced Dropdown */
    .dropdown-menu {
        border: 2px solid #e0f2fe !important;
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1) !important;
        border-radius: 12px !important;
    }

    .dropdown-item {
        padding: 0.75rem 1.25rem !important;
        font-size: 1rem !important;
        color: #334155 !important;
    }

    .dropdown-item:hover {
        background: #f0f9ff !important;
        color: #0369a1 !important;
    }

    /* Section Headers */
    .section-header {
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%) !important;
        padding: 1rem 1.5rem !important;
        border-radius: 12px !important;
        margin-bottom: 1.5rem !important;
    }

    .section-header h4, .section-header h5 {
        color: #0369a1 !important;
        margin: 0 !important;
        font-weight: 700 !important;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 px-md-4">
    <!-- Patient Header Card (Calm Blue Theme) -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="patient-header-card">
                <div class="row align-items-center">
                    <div class="col-auto">
                        <div class="patient-avatar-large">
                            {{ mb_substr($patient->name ?? 'ลูกค้า', 0, 1) }}
                        </div>
                    </div>
                    <div class="col">
                        <div class="row g-3">
                            <div class="col-12 col-md-8">
                                <h2 class="mb-1" style="color: var(--soft-blue-800);">{{ $patient->name ?? 'ชื่อลูกค้า' }}</h2>
                                <div class="d-flex flex-wrap gap-3 mb-2">
                                    <span style="color: var(--soft-blue-700);"><i class="bi bi-person-vcard"></i> HN: {{ $patient->hn }}</span>
                                    <span style="color: var(--soft-blue-700);">
                                        @if($patient->gender)
                                        <i class="bi bi-gender-{{ $patient->gender == 'male' ? 'male' : 'female' }}"></i>
                                        {{ $patient->gender == 'male' ? 'ชาย' : 'หญิง' }}
                                        @endif
                                        @if($patient->age){{ $patient->age }} ปี @endif
                                    </span>
                                    @if($patient->phone)<span><i class="bi bi-telephone"></i> {{ $patient->phone }}</span>@endif
                                    @if($patient->branch)<span><i class="bi bi-geo-alt"></i> {{ $patient->branch->name }}</span>@endif
                                </div>

                                <!-- Critical Alerts -->
                                <div class="d-flex flex-wrap mt-3">
                                    @if($patient->chronic_diseases)
                                        <div class="alert-badge me-2">
                                            <i class="bi bi-exclamation-triangle-fill"></i>
                                            <span>โรคประจำตัว: {{ $patient->chronic_diseases }}</span>
                                        </div>
                                    @endif
                                    @if($patient->drug_allergy)
                                        <div class="alert-badge">
                                            <i class="bi bi-capsule"></i>
                                            <span>แพ้ยา: {{ $patient->drug_allergy }}</span>
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-12 col-md-4 text-md-end">
                                <div class="btn-group" role="group">
                                    <a href="{{ route('patients.edit', $patient->id ?? 1) }}" class="btn btn-light">
                                        <i class="bi bi-pencil me-1"></i>แก้ไข
                                    </a>
                                    <button type="button" class="btn btn-light" data-bs-toggle="modal" data-bs-target="#quickAppointmentModal">
                                        <i class="bi bi-calendar-plus me-1"></i>นัดหมาย
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tab Navigation -->
    <ul class="nav nav-tabs-custom" id="patientTabs" role="tablist">
        <li class="nav-item">
            <a class="nav-link active" data-bs-toggle="tab" href="#profile">
                <i class="bi bi-person-vcard me-2"></i>ข้อมูลส่วนตัว & OPD
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#timeline">
                <i class="bi bi-clock-history me-2"></i>ประวัติการรักษา
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#courses">
                <i class="bi bi-box-seam me-2"></i>คอร์ส/แพ็คเกจ
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#financial">
                <i class="bi bi-cash-stack me-2"></i>ประวัติการเงิน
            </a>
        </li>
        <li class="nav-item">
            <a class="nav-link" data-bs-toggle="tab" href="#crm">
                <i class="bi bi-telephone me-2"></i>ประวัติ CRM
            </a>
        </li>
    </ul>

    <!-- Tab Content -->
    <div class="tab-content">
        <!-- Profile & OPD Tab (New First Tab) -->
        <div class="tab-pane fade show active" id="profile">
            <!-- CRITICAL: Next Appointment Card - MOST PROMINENT POSITION (Top of Left Column) -->
            <div class="row">
                <div class="col-12">
                    <!-- Next Appointment Card -->
                    @if($nextAppointment)
                    <div class="card next-appointment-card mb-3">
                        <div class="card-body">
                            <div class="d-flex align-items-center justify-content-between">
                                <div class="d-flex align-items-center gap-3">
                                    <i class="bi bi-calendar-check" style="font-size: 1.5rem; color: #2563eb;"></i>
                                    <div>
                                        <div class="info-label">นัดหมายครั้งต่อไป</div>
                                        <div class="fw-bold" style="color: #1e293b;">
                                            @php $apptDate = \Carbon\Carbon::parse($nextAppointment->appointment_date); @endphp
                                            {{ $apptDate->locale('th')->isoFormat('D MMM') }} {{ $apptDate->year + 543 }}
                                            เวลา {{ \Carbon\Carbon::parse($nextAppointment->appointment_time)->format('H:i') }} น.
                                        </div>
                                    </div>
                                </div>
                                <div>
                                    @php
                                        $appointmentDate = \Carbon\Carbon::parse($nextAppointment->appointment_date);
                                        $daysUntil = $appointmentDate->diffInDays(now());
                                    @endphp
                                    <span class="badge" style="background: #dbeafe; color: #1d4ed8;">
                                        @if($daysUntil == 0) วันนี้ @elseif($daysUntil == 1) พรุ่งนี้ @else อีก {{ $daysUntil }} วัน @endif
                                    </span>
                                </div>
                            </div>
                            @if(isset($nextAppointment->notes) && $nextAppointment->notes)
                            <div class="mt-2 pt-2" style="border-top: 1px solid #e0f2fe;">
                                <small style="color: #64748b;"><i class="bi bi-chat-text me-1"></i>{{ $nextAppointment->notes }}</small>
                            </div>
                            @endif
                        </div>
                    </div>
                    @else
                    <div class="card mb-3" style="border: 1px dashed #cbd5e1 !important;">
                        <div class="card-body text-center py-3">
                            <i class="bi bi-calendar-x" style="font-size: 1.5rem; color: #94a3b8;"></i>
                            <p class="mb-0 mt-1" style="color: #64748b; font-size: 0.8rem;">ไม่มีนัดหมาย</p>
                        </div>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Allergy Information Card -->
            <div class="card critical-allergy-card mb-3">
                <div class="critical-allergy-header">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <span style="font-weight: 600;">ข้อมูลการแพ้</span>
                </div>
                <div class="card-body">
                    <div class="row g-3">
                        <!-- Drug Allergy -->
                        <div class="col-md-6">
                            @if($patient->drug_allergy)
                                <div class="p-3 rounded" style="background: #fef2f2; border: 1px solid #fecaca;">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-capsule me-2" style="color: #dc2626; font-size: 1.25rem;"></i>
                                        <div>
                                            <div style="font-size: 0.75rem; color: #dc2626; font-weight: 600;">แพ้ยา</div>
                                            <div style="font-size: 0.9rem; color: #1e293b; font-weight: 600;">{{ $patient->drug_allergy }}</div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="p-3 rounded" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle me-2" style="color: #16a34a;"></i>
                                        <div>
                                            <div style="font-size: 0.75rem; color: #16a34a; font-weight: 600;">แพ้ยา</div>
                                            <div style="font-size: 0.8rem; color: #64748b;">ไม่มีประวัติ</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>

                        <!-- Food Allergy -->
                        <div class="col-md-6">
                            @if($patient->food_allergy)
                                <div class="p-3 rounded" style="background: #fffbeb; border: 1px solid #fde68a;">
                                    <div class="d-flex align-items-start">
                                        <i class="bi bi-egg-fried me-2" style="color: #d97706; font-size: 1.25rem;"></i>
                                        <div>
                                            <div style="font-size: 0.75rem; color: #d97706; font-weight: 600;">อาหารที่แพ้</div>
                                            <div style="font-size: 0.9rem; color: #1e293b; font-weight: 600;">{{ $patient->food_allergy }}</div>
                                        </div>
                                    </div>
                                </div>
                            @else
                                <div class="p-3 rounded" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                                    <div class="d-flex align-items-center">
                                        <i class="bi bi-check-circle me-2" style="color: #16a34a;"></i>
                                        <div>
                                            <div style="font-size: 0.75rem; color: #16a34a; font-weight: 600;">อาหารที่แพ้</div>
                                            <div style="font-size: 0.8rem; color: #64748b;">ไม่มี</div>
                                        </div>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>

            <div class="row">
                <!-- Left Column: Personal Information -->
                <div class="col-lg-6">
                    <!-- Basic Information Card (Calm Blue Theme) -->
                    <div class="card info-card mb-4">
                        <div class="card-header info-card-header bg-transparent">
                            <h6 class="mb-0 fw-bold">
                                <i class="bi bi-person-circle me-2"></i>ข้อมูลพื้นฐาน
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row mb-3">
                                <div class="col-4">
                                    <div class="text-center">
                                        <div class="patient-avatar-large mx-auto mb-2" style="width: 100px; height: 100px; font-size: 2.5rem;">
                                            {{ mb_substr($patient->name ?? 'ลูกค้า', 0, 1) }}
                                        </div>
                                        <small style="color: var(--soft-blue-600); font-weight: 500;">HN: {{ $patient->hn ?? 'PT-2024-001' }}</small>
                                    </div>
                                </div>
                                <div class="col-8">
                                    <div class="row g-2">
                                        <div class="col-12">
                                            <label class="text-muted small mb-1">ชื่อ-นามสกุล</label>
                                            <p class="fw-bold mb-2">{{ $patient->name ?? 'ชื่อลูกค้า' }}</p>
                                        </div>
                                        <div class="col-6">
                                            <label class="text-muted small mb-1">ชื่อเล่น</label>
                                            <p class="mb-2">{{ $patient->nickname ?: 'ไม่ระบุ' }}</p>
                                        </div>
                                        <div class="col-6">
                                            <label class="text-muted small mb-1">เพศ</label>
                                            <p class="mb-2">
                                                @if($patient->gender)
                                                    {{ $patient->gender == 'male' ? 'ชาย' : 'หญิง' }}
                                                @else
                                                    ไม่ระบุ
                                                @endif
                                            </p>
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <hr>

                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="text-muted small mb-1">
                                        <i class="bi bi-calendar-event text-primary me-1"></i>วันเกิด
                                    </label>
                                    <p class="mb-0">
                                        @if($patient->date_of_birth || $patient->birth_date)
                                            @php
                                                $birthDate = $patient->birth_date ?? $patient->date_of_birth;
                                                $buddhistYear = $birthDate->year + 543;
                                            @endphp
                                            {{ $birthDate->locale('th')->isoFormat('D MMMM') }} {{ $buddhistYear }}
                                        @else
                                            ไม่ระบุ
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small mb-1">
                                        <i class="bi bi-hourglass-split text-primary me-1"></i>อายุ
                                    </label>
                                    <p class="mb-0">{{ $patient->age ? $patient->age . ' ปี' : 'ไม่ระบุ' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small mb-1">
                                        <i class="bi bi-droplet-fill text-danger me-1"></i>กรุ๊ปเลือด
                                    </label>
                                    <p class="mb-0">
                                        @if($patient->blood_group)
                                            <span class="badge bg-danger fs-6">{{ $patient->blood_group }}</span>
                                        @else
                                            <span class="text-muted">ไม่ระบุ</span>
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small mb-1">
                                        <i class="bi bi-card-text text-primary me-1"></i>เลขบัตรประชาชน
                                    </label>
                                    <p class="mb-0">{{ $patient->id_card ?: 'ไม่ระบุ' }}</p>
                                </div>
                                <div class="col-12">
                                    <label class="text-muted small mb-1">
                                        <i class="bi bi-briefcase text-primary me-1"></i>อาชีพ
                                    </label>
                                    <p class="mb-0">{{ $patient->occupation ?: 'ไม่ระบุ' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Contact Information Card -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-bold text-primary">
                                <i class="bi bi-telephone me-2"></i>ข้อมูลติดต่อ
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="text-muted small mb-1">
                                        <i class="bi bi-phone text-success me-1"></i>เบอร์โทรศัพท์
                                    </label>
                                    <p class="mb-0">
                                        @if($patient->phone)
                                        <a href="tel:{{ $patient->phone }}" class="text-decoration-none">
                                            <i class="bi bi-telephone-fill text-success"></i> {{ $patient->phone }}
                                        </a>
                                        @else
                                        ไม่ระบุ
                                        @endif
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small mb-1">
                                        <i class="bi bi-line text-success me-1"></i>Line ID
                                    </label>
                                    <p class="mb-0">{{ $patient->line_id ?: 'ไม่ระบุ' }}</p>
                                </div>
                                <div class="col-12">
                                    <label class="text-muted small mb-1">
                                        <i class="bi bi-envelope text-primary me-1"></i>อีเมล
                                    </label>
                                    <p class="mb-0">{{ $patient->email ?: 'ไม่ระบุ' }}</p>
                                </div>
                                <div class="col-12">
                                    <label class="text-muted small mb-1">
                                        <i class="bi bi-geo-alt text-danger me-1"></i>ที่อยู่
                                    </label>
                                    <p class="mb-0">{{ $patient->address ?: 'ไม่ระบุ' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small mb-1">
                                        <i class="bi bi-person-plus text-warning me-1"></i>ผู้ติดต่อฉุกเฉิน
                                    </label>
                                    <p class="mb-0">{{ $patient->emergency_contact ?: 'ไม่ระบุ' }}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="text-muted small mb-1">
                                        <i class="bi bi-telephone-forward text-danger me-1"></i>เบอร์ฉุกเฉิน
                                    </label>
                                    <p class="mb-0">{{ $patient->emergency_phone ?: 'ไม่ระบุ' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column: Medical Information -->
                <div class="col-lg-6">
                    <!-- Medical History Card -->
                    <div class="card mb-4 shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-bold text-danger">
                                <i class="bi bi-clipboard2-pulse me-2"></i>ประวัติทางการแพทย์
                            </h6>
                        </div>
                        <div class="card-body">
                            <!-- Chief Complaint -->
                            <div class="mb-4">
                                <label class="form-label text-muted fw-bold mb-2">
                                    <i class="bi bi-chat-left-text text-primary me-1"></i>อาการแรกรับ (Chief Complaint)
                                </label>
                                @if($patient->chief_complaint)
                                    <div class="p-3 rounded" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%); border-left: 4px solid #3b82f6;">
                                        <p class="mb-0 text-dark fw-medium">
                                            <i class="bi bi-quote me-2 text-primary"></i>{{ $patient->chief_complaint }}
                                        </p>
                                    </div>
                                @else
                                    <p class="text-muted mb-0">ไม่มีข้อมูล</p>
                                @endif
                            </div>

                            <!-- Chronic Diseases -->
                            <div class="mb-4">
                                <label class="form-label text-muted fw-bold mb-2">
                                    <i class="bi bi-heart-pulse text-warning me-1"></i>โรคประจำตัว
                                </label>
                                @if($patient->chronic_diseases)
                                    <div class="p-3 bg-light rounded border-start border-warning border-3">
                                        <p class="mb-0">{{ $patient->chronic_diseases }}</p>
                                    </div>
                                @else
                                    <p class="text-muted mb-0">
                                        <i class="bi bi-check-circle text-success me-1"></i>ไม่มีโรคประจำตัว
                                    </p>
                                @endif
                            </div>

                            <!-- Surgery History -->
                            <div class="mb-0">
                                <label class="form-label text-muted fw-bold mb-2">
                                    <i class="bi bi-scissors text-info me-1"></i>ประวัติการผ่าตัด
                                </label>
                                @if($patient->surgery_history)
                                    <div class="p-3 bg-light rounded border-start border-info border-3">
                                        <p class="mb-0">{{ $patient->surgery_history }}</p>
                                    </div>
                                @else
                                    <p class="text-muted mb-0">
                                        <i class="bi bi-check-circle text-success me-1"></i>ไม่มีประวัติการผ่าตัด
                                    </p>
                                @endif
                            </div>
                        </div>
                    </div>

                    <!-- Visit Summary Card -->
                    <div class="card shadow-sm">
                        <div class="card-header bg-white border-bottom">
                            <h6 class="mb-0 fw-bold text-success">
                                <i class="bi bi-graph-up me-2"></i>สรุปการรักษา
                            </h6>
                        </div>
                        <div class="card-body">
                            <div class="row text-center g-3">
                                <div class="col-4">
                                    <div class="p-3 rounded" style="background: linear-gradient(135deg, #dbeafe 0%, #bfdbfe 100%);">
                                        <i class="bi bi-calendar-check text-primary fs-4 mb-2 d-block"></i>
                                        <h4 class="text-primary mb-1">{{ $patient->total_visits ?? 0 }}</h4>
                                        <small class="text-muted">ครั้งที่มา</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-3 rounded" style="background: linear-gradient(135deg, #dcfce7 0%, #bbf7d0 100%);">
                                        <i class="bi bi-clipboard-check text-success fs-4 mb-2 d-block"></i>
                                        <h4 class="text-success mb-1">{{ $patient->coursePurchases->where('status', 'active')->count() }}</h4>
                                        <small class="text-muted">คอร์สที่ใช้</small>
                                    </div>
                                </div>
                                <div class="col-4">
                                    <div class="p-3 rounded" style="background: linear-gradient(135deg, #fef3c7 0%, #fde68a 100%);">
                                        <i class="bi bi-cash-coin text-warning fs-4 mb-2 d-block"></i>
                                        <h4 class="text-warning mb-1">฿{{ number_format($patient->total_spent ?? 0) }}</h4>
                                        <small class="text-muted">ยอดใช้จ่าย</small>
                                    </div>
                                </div>
                            </div>
                            <hr>
                            <div class="d-flex justify-content-between small mb-2">
                                <span class="text-muted">
                                    <i class="bi bi-calendar-plus text-primary me-1"></i>ครั้งแรกที่มา
                                </span>
                                <span class="fw-bold">
                                    @if($patient->created_at)
                                        {{ $patient->created_at->locale('th')->isoFormat('D MMM') }} {{ $patient->created_at->year + 543 }}
                                    @else
                                        ไม่มีข้อมูล
                                    @endif
                                </span>
                            </div>
                            <div class="d-flex justify-content-between small">
                                <span class="text-muted">
                                    <i class="bi bi-calendar-event text-success me-1"></i>ครั้งล่าสุด
                                </span>
                                <span class="fw-bold">
                                    @if($patient->last_visit)
                                        @php $lastVisit = \Carbon\Carbon::parse($patient->last_visit); @endphp
                                        {{ $lastVisit->locale('th')->isoFormat('D MMM') }} {{ $lastVisit->year + 543 }}
                                    @else
                                        ไม่มีข้อมูล
                                    @endif
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Timeline Tab -->
        <div class="tab-pane fade" id="timeline">
            <div class="row">
                <div class="col-12 col-lg-8">
                    <!-- Summary Stats -->
                    <div class="d-flex gap-3 mb-3">
                        <div class="px-3 py-2 rounded" style="background: #f0fdf4; border: 1px solid #bbf7d0;">
                            <small style="color: #166534;">รักษาแล้ว <strong>{{ $patient->total_visits ?? 0 }}</strong> ครั้ง</small>
                        </div>
                        <div class="px-3 py-2 rounded" style="background: #eff6ff; border: 1px solid #bfdbfe;">
                            <small style="color: #1e40af;">ครั้งล่าสุด <strong>
                                @if($patient->last_visit)
                                    {{ \Carbon\Carbon::parse($patient->last_visit)->locale('th')->diffForHumans() }}
                                @else
                                    ไม่มีข้อมูล
                                @endif
                            </strong></small>
                        </div>
                    </div>

                    <div class="timeline">
                        @php
                            // ดึงประวัติการรักษาจาก treatments โดยตรง (รวมทั้งที่มีและไม่มี appointment)
                            $treatments = $patient->treatments()
                                ->with(['service', 'pt', 'appointment'])
                                ->orderBy('started_at', 'desc')
                                ->orderBy('created_at', 'desc')
                                ->take(15)
                                ->get();
                        @endphp
                        @forelse($treatments as $treatment)
                        <div class="timeline-item">
                            <div class="timeline-icon success"></div>
                            <div class="timeline-content">
                                <div class="d-flex justify-content-between align-items-start">
                                    <div>
                                        <h6 class="fw-bold mb-1">
                                            @if($treatment->service)
                                                {{ $treatment->service->name }}
                                            @else
                                                {{ $treatment->chief_complaint ?? 'การรักษา' }}
                                            @endif
                                        </h6>
                                        <small style="color: #64748b;">
                                            @if($treatment->started_at)
                                                @php $treatDate = \Carbon\Carbon::parse($treatment->started_at); @endphp
                                                <i class="bi bi-calendar3 me-1"></i>
                                                {{ $treatDate->locale('th')->isoFormat('D MMM') }} {{ $treatDate->year + 543 }}
                                                @if($treatDate->format('H:i') != '00:00')
                                                    {{ $treatDate->format('H:i') }} น.
                                                @endif
                                            @else
                                                @php $treatDate = \Carbon\Carbon::parse($treatment->created_at); @endphp
                                                <i class="bi bi-calendar3 me-1"></i>
                                                {{ $treatDate->locale('th')->isoFormat('D MMM') }} {{ $treatDate->year + 543 }}
                                            @endif
                                        </small>
                                        @if($treatment->pt)
                                        <br><small style="color: #64748b;">
                                            <i class="bi bi-person me-1"></i>PT: {{ $treatment->pt->name ?? $treatment->pt->username }}
                                        </small>
                                        @endif
                                        @if($treatment->duration_minutes)
                                        <br><small style="color: #64748b;">
                                            <i class="bi bi-clock me-1"></i>ระยะเวลา: {{ $treatment->duration_minutes }} นาที
                                        </small>
                                        @endif
                                    </div>
                                    <div class="text-end">
                                        <span class="badge mb-2" style="background: #dcfce7; color: #166534;">
                                            เสร็จสิ้น
                                        </span>
                                        @if($treatment->appointment_id)
                                        <br>
                                        <button class="btn btn-outline-primary btn-sm mt-1" onclick="viewTreatmentDetail('{{ $treatment->appointment_id }}')">
                                            <i class="bi bi-eye me-1"></i>ดูรายละเอียด
                                        </button>
                                        @endif
                                    </div>
                                </div>
                                @if($treatment->treatment_notes)
                                <div class="bg-light rounded p-2 mt-2">
                                    <small><i class="bi bi-chat-text me-1"></i>{!! nl2br(e(Str::limit($treatment->treatment_notes, 150))) !!}</small>
                                </div>
                                @endif
                            </div>
                        </div>
                        @empty
                        <div class="text-center py-4">
                            <i class="bi bi-calendar-x" style="font-size: 2rem; color: #cbd5e1;"></i>
                            <p class="text-muted mt-2">ยังไม่มีประวัติการรักษา</p>
                        </div>
                        @endforelse
                    </div>

                    @if($patient->treatments()->count() > 15)
                    <button class="btn btn-outline-secondary btn-sm w-100 mt-2">
                        <i class="bi bi-arrow-down me-1"></i>ดูเพิ่มเติม ({{ $patient->treatments()->count() }} รายการ)
                    </button>
                    @endif
                </div>

                <!-- Quick Stats -->
                <div class="col-12 col-lg-4 mt-3 mt-lg-0">
                    <div class="card">
                        <div class="card-header">นัดหมายถัดไป</div>
                        <div class="card-body">
                            @if($nextAppointment)
                            <div class="d-flex align-items-center">
                                <i class="bi bi-calendar-check me-3" style="font-size: 1.25rem; color: #0ea5e9;"></i>
                                <div>
                                    <div class="fw-bold" style="font-size: 0.875rem;">
                                        @php $nextApptDate = \Carbon\Carbon::parse($nextAppointment->appointment_date); @endphp
                                        {{ $nextApptDate->locale('th')->isoFormat('ddd D MMM') }} {{ $nextApptDate->year + 543 }}
                                    </div>
                                    <small style="color: #64748b;">
                                        {{ \Carbon\Carbon::parse($nextAppointment->appointment_time)->format('H:i') }} น.
                                        @if($nextAppointment->purpose)
                                        - {{ $nextAppointment->purpose }}
                                        @endif
                                    </small>
                                </div>
                            </div>
                            @else
                            <div class="text-center py-2">
                                <i class="bi bi-calendar-x" style="font-size: 1.5rem; color: #cbd5e1;"></i>
                                <p class="text-muted mb-0 mt-1 small">ไม่มีนัดหมาย</p>
                            </div>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Courses Tab -->
        <div class="tab-pane fade" id="courses">
            <div class="row">
                <!-- Active Courses -->
                <div class="col-12">
                    <div class="d-flex justify-content-between align-items-center mb-3">
                        <h5 class="mb-0 fw-bold">คอร์สที่ใช้งานอยู่ ({{ $patient->coursePurchases->where('status', 'active')->count() }} รายการ)</h5>
                        <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#purchaseCourseOnlineModal">
                            <i class="bi bi-plus-circle me-1"></i>ซื้อคอร์สใหม่
                        </button>
                    </div>

                    @forelse($patient->coursePurchases->where('status', 'active') as $course)
                    <div class="course-card">
                        <div class="row align-items-start">
                            <div class="col">
                                <h6 class="fw-bold mb-1">{{ $course->package->name ?? 'คอร์ส' }}</h6>
                                <p class="text-muted mb-2">{{ $course->total_sessions }} ครั้ง - ซื้อเมื่อ {{ $course->purchase_date ? $course->purchase_date->locale('th')->isoFormat('D MMM') . ' ' . ($course->purchase_date->year + 543) : '-' }}</p>

                                @php
                                    $usagePercent = $course->total_sessions > 0 ? ($course->used_sessions / $course->total_sessions * 100) : 0;
                                    $progressColor = $usagePercent > 80 ? 'linear-gradient(90deg, #f59e0b 0%, #d97706 100%)' : 'linear-gradient(90deg, #0ea5e9 0%, #0284c7 100%)';
                                @endphp

                                <div class="mb-2">
                                    <div class="d-flex justify-content-between mb-1">
                                        <small>ใช้ไปแล้ว {{ $course->used_sessions }}/{{ $course->total_sessions }} ครั้ง</small>
                                        <small class="{{ $usagePercent > 80 ? 'text-warning' : 'text-primary' }}">{{ number_format($usagePercent, 0) }}%</small>
                                    </div>
                                    <div class="progress-custom">
                                        <div class="progress-bar-custom" style="width: {{ $usagePercent }}%; background: {{ $progressColor }};"></div>
                                    </div>
                                </div>

                                <div class="d-flex gap-2 mt-2">
                                    <small class="text-muted"><i class="bi bi-clock"></i> หมดอายุ: {{ $course->expiry_date ? $course->expiry_date->locale('th')->isoFormat('D MMM') . ' ' . ($course->expiry_date->year + 543) : '-' }}</small>
                                </div>

                                @if($course->payment_type === 'installment')
                                <div class="mt-2">
                                    @if($course->installment_paid < $course->installment_total)
                                    <span class="badge bg-warning text-dark">
                                        <i class="bi bi-calendar2-week me-1"></i>
                                        ผ่อนงวด {{ $course->installment_paid }}/{{ $course->installment_total }}
                                        (เหลือ {{ $course->installment_total - $course->installment_paid }} งวด • ฿{{ number_format($course->installment_amount * ($course->installment_total - $course->installment_paid)) }})
                                    </span>
                                    @else
                                    <span class="badge bg-success">
                                        <i class="bi bi-check-circle me-1"></i>ผ่อนครบแล้ว
                                    </span>
                                    @endif
                                </div>
                                @endif

                                <!-- Course Sharing Settings -->
                                <div class="mt-3 p-3 bg-light rounded">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h6 class="mb-0 small fw-bold"><i class="bi bi-people me-2"></i>ผู้ใช้งานคอร์สร่วม</h6>
                                        <button class="btn btn-sm btn-outline-primary" data-bs-toggle="modal" data-bs-target="#addSharedUserModal" data-course-id="{{ $course->id }}">
                                            <i class="bi bi-person-plus me-1"></i>เพิ่มผู้ใช้ร่วม
                                        </button>
                                    </div>
                                    @if($course->sharedUsers->count() > 0)
                                    <div class="shared-users mt-2">
                                        @foreach($course->sharedUsers as $index => $sharedUser)
                                        <div class="d-flex align-items-center justify-content-between mb-2">
                                            <div class="d-flex align-items-center">
                                                <span class="badge bg-primary me-2">{{ $index + 1 }}</span>
                                                <small>{{ $sharedUser->sharedPatient->name ?? 'N/A' }} ({{ $sharedUser->relationship ?? 'ไม่ระบุ' }})</small>
                                            </div>
                                            <form action="{{ route('course-shared-users.destroy', $sharedUser->id) }}" method="POST" class="d-inline" onsubmit="return confirm('ต้องการยกเลิกการแชร์?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-sm btn-link text-danger p-0"><i class="bi bi-trash"></i></button>
                                            </form>
                                        </div>
                                        @endforeach
                                    </div>
                                    @else
                                    <div class="text-muted small">
                                        <i class="bi bi-info-circle me-1"></i>ยังไม่มีผู้ใช้ร่วม
                                    </div>
                                    @endif
                                </div>
                            </div>
                            <div class="col-auto">
                                <div class="d-flex flex-column gap-2">
                                    <button class="btn {{ $usagePercent > 80 ? 'btn-warning' : 'btn-primary' }}">
                                        <i class="bi bi-check2-circle me-1"></i>ใช้คอร์ส
                                    </button>
                                    <button class="btn btn-outline-info btn-sm" data-bs-toggle="modal" data-bs-target="#courseUsageHistoryModal" data-course-id="{{ $course->id }}" data-course-name="{{ $course->package->name ?? 'คอร์ส' }}" data-used-sessions="{{ $course->used_sessions }}" data-total-sessions="{{ $course->total_sessions }}">
                                        <i class="bi bi-clock-history me-1"></i>ประวัติการใช้
                                    </button>
                                    <button class="btn btn-outline-danger btn-sm" data-bs-toggle="modal" data-bs-target="#cancelCourseModal" data-course-id="{{ $course->id }}" data-course-name="{{ $course->package->name ?? 'คอร์ส' }}" data-course-price="{{ $course->invoice->total_amount ?? 0 }}" data-used-sessions="{{ $course->used_sessions }}">
                                        <i class="bi bi-x-circle me-1"></i>ยกเลิกคอร์ส
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                    @empty
                    <div class="text-center py-5">
                        <i class="bi bi-ticket-perforated" style="font-size: 3rem; color: #cbd5e1;"></i>
                        <p class="text-muted mt-3">ยังไม่มีคอร์สที่ใช้งานอยู่</p>
                    </div>
                    @endforelse

                    <!-- Completed Courses -->
                    <h5 class="mb-3 mt-4 fw-bold">คอร์สที่ใช้เสร็จแล้ว</h5>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>ชื่อคอร์ส</th>
                                    <th>จำนวน</th>
                                    <th>วันที่ซื้อ</th>
                                    <th>ใช้เสร็จ</th>
                                    <th>มูลค่า</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($patient->coursePurchases->where('status', 'completed') as $completedCourse)
                                <tr>
                                    <td>{{ $completedCourse->package->name ?? 'คอร์ส' }}</td>
                                    <td>{{ $completedCourse->total_sessions }} ครั้ง</td>
                                    <td>{{ $completedCourse->purchase_date ? $completedCourse->purchase_date->locale('th')->isoFormat('D MMM') . ' ' . ($completedCourse->purchase_date->year + 543) : '-' }}</td>
                                    <td>{{ $completedCourse->completed_date ? $completedCourse->completed_date->locale('th')->isoFormat('D MMM') . ' ' . ($completedCourse->completed_date->year + 543) : '-' }}</td>
                                    <td>฿{{ number_format($completedCourse->price ?? 0) }}</td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="5" class="text-center text-muted py-3">ยังไม่มีคอร์สที่ใช้เสร็จ</td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- Financial Tab -->
        <div class="tab-pane fade" id="financial">
            <!-- Summary Cards -->
            @php
                // Include all invoices (including soft deleted ones that have refunds)
                $totalPaid = \App\Models\Invoice::withTrashed()
                    ->where('patient_id', $patient->id)
                    ->where('status', 'paid')
                    ->sum('total_amount') ?? 0;
                $totalRefunded = $patient->refunds()->sum('refund_amount') ?? 0;
                // Net total = total paid - refunded
                $netTotal = $totalPaid - $totalRefunded;

                // Invoice pending
                $invoicePending = $patient->invoices()->whereIn('status', ['pending', 'partial'])->sum('total_amount') ?? 0;

                // Installment pending from courses
                $installmentPending = 0;
                foreach ($patient->coursePurchases->where('payment_type', 'installment') as $course) {
                    $remainingInstallments = $course->installment_total - $course->installment_paid;
                    if ($remainingInstallments > 0) {
                        $installmentPending += $remainingInstallments * $course->installment_amount;
                    }
                }

                $totalPending = $invoicePending + $installmentPending;
                $invoiceCount = $patient->invoices()->count();
            @endphp
            <div class="row mb-4">
                <div class="col-12 col-md-4 mb-3">
                    <div class="financial-card income">
                        <h6 class="text-muted mb-2">ยอดซื้อสุทธิ</h6>
                        <h3 class="text-success mb-0">฿{{ number_format($netTotal) }}</h3>
                        <small class="text-muted">{{ $invoiceCount }} รายการ @if($totalRefunded > 0)<span class="text-danger">(คืนเงิน ฿{{ number_format($totalRefunded) }})</span>@endif</small>
                    </div>
                </div>
                <div class="col-12 col-md-4 mb-3">
                    <div class="financial-card expense">
                        <h6 class="text-muted mb-2">ค้างชำระ</h6>
                        <h3 class="text-danger mb-0">฿{{ number_format($totalPending) }}</h3>
                        <small class="text-muted">
                            @if($totalPending == 0)
                                ไม่มีหนี้ค้าง
                            @else
                                @if($installmentPending > 0)
                                    <span class="text-warning">ผ่อนคอร์ส ฿{{ number_format($installmentPending) }}</span>
                                @endif
                            @endif
                        </small>
                    </div>
                </div>
            </div>

            <!-- Transaction History -->
            <div class="card">
                <div class="card-header bg-white">
                    <div class="d-flex justify-content-between align-items-center">
                        <h6 class="mb-0 fw-bold">ประวัติการทำรายการ</h6>
                        <button class="btn btn-sm btn-primary" data-bs-toggle="modal" data-bs-target="#addTransactionModal">
                            <i class="bi bi-plus-circle me-1"></i>เพิ่มรายการ
                        </button>
                    </div>
                </div>
                <div class="card-body p-0">
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>วันที่</th>
                                    <th>เลขที่บิล</th>
                                    <th>รายการ</th>
                                    <th>ประเภท</th>
                                    <th class="text-end">จำนวนเงิน</th>
                                    <th>สถานะ</th>
                                    <th width="80" class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @php
                                    // Get invoices
                                    $invoices = $patient->invoices()->with('items')->orderBy('created_at', 'desc')->take(10)->get();
                                    // Get refunds
                                    $refunds = \App\Models\Refund::where('patient_id', $patient->id)->orderBy('created_at', 'desc')->take(5)->get();
                                @endphp
                                @forelse($invoices as $invoice)
                                <tr>
                                    <td>{{ $invoice->created_at ? $invoice->created_at->locale('th')->isoFormat('D MMM') . ' ' . ($invoice->created_at->year + 543) : '-' }}</td>
                                    <td class="text-primary">{{ $invoice->invoice_number ?? 'INV-' . $invoice->id }}</td>
                                    <td>{{ $invoice->items->first()?->description ?? 'รายการ' }}</td>
                                    <td><span class="badge bg-{{ $invoice->invoice_type == 'course' ? 'info' : ($invoice->invoice_type == 'refund' ? 'danger' : 'primary') }}">{{ $invoice->invoice_type == 'course' ? 'คอร์ส' : ($invoice->invoice_type == 'refund' ? 'คืนเงิน' : 'บริการ') }}</span></td>
                                    <td class="text-end fw-medium">฿{{ number_format($invoice->total_amount ?? 0) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $invoice->status == 'paid' ? 'success' : ($invoice->status == 'pending' ? 'warning' : 'secondary') }}">
                                            {{ $invoice->status == 'paid' ? 'ชำระแล้ว' : ($invoice->status == 'pending' ? 'รอชำระ' : $invoice->status) }}
                                        </span>
                                    </td>
                                    <td class="text-center">
                                        <div class="d-flex gap-1">
                                            <button class="btn-icon btn-view" title="ดูรายละเอียด" onclick="viewInvoiceDetail('{{ $invoice->id }}')">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                @endforelse

                                @foreach($refunds as $refund)
                                <tr class="table-danger">
                                    <td>{{ $refund->created_at ? $refund->created_at->locale('th')->isoFormat('D MMM') . ' ' . ($refund->created_at->year + 543) : '-' }}</td>
                                    <td class="text-danger">{{ $refund->refund_number }}</td>
                                    <td>ยกเลิกคอร์ส - {{ $refund->reason }}</td>
                                    <td><span class="badge bg-danger">คืนเงิน</span></td>
                                    <td class="text-end fw-medium text-danger">-฿{{ number_format($refund->refund_amount ?? 0) }}</td>
                                    <td>
                                        <span class="badge bg-{{ $refund->status == 'approved' ? 'success' : 'warning' }}">
                                            {{ $refund->status == 'approved' ? 'คืนแล้ว' : 'รอดำเนินการ' }}
                                        </span>
                                    </td>
                                    <td class="text-center">-</td>
                                </tr>
                                @endforeach

                                @if($invoices->isEmpty() && $refunds->isEmpty())
                                <tr>
                                    <td colspan="7" class="text-center text-muted py-4">ยังไม่มีประวัติการทำรายการ</td>
                                </tr>
                                @endif
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>

        <!-- CRM History Tab -->
        <div class="tab-pane fade" id="crm">
            <div class="card">
                <div class="card-header bg-white">
                    <h6 class="mb-0 fw-bold"><i class="bi bi-telephone-outbound me-2"></i>ประวัติการโทร CRM</h6>
                </div>
                <div class="card-body p-0">
                    @php
                        $crmCalls = \App\Models\CrmCall::where('patient_id', $patient->id)
                            ->where('status', '!=', 'pending')
                            ->with(['appointment', 'treatment.service', 'caller', 'branch'])
                            ->orderBy('called_at', 'desc')
                            ->get();
                    @endphp

                    @if($crmCalls->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover mb-0">
                            <thead class="table-light">
                                <tr>
                                    <th>วันที่โทร</th>
                                    <th>ประเภท</th>
                                    <th>สถานะ</th>
                                    <th>ผู้โทร</th>
                                    <th>หมายเหตุ</th>
                                    <th>Feedback</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($crmCalls as $call)
                                <tr>
                                    <td>
                                        @if($call->called_at)
                                            {{ \Carbon\Carbon::parse($call->called_at)->locale('th')->isoFormat('D MMM') }} {{ \Carbon\Carbon::parse($call->called_at)->year + 543 }}
                                            <br><small class="text-muted">{{ \Carbon\Carbon::parse($call->called_at)->format('H:i') }} น.</small>
                                        @else
                                            -
                                        @endif
                                    </td>
                                    <td>
                                        @if($call->call_type == 'confirmation')
                                            <span class="badge bg-primary"><i class="bi bi-calendar-check me-1"></i>ยืนยันนัด</span>
                                            @if($call->appointment)
                                                <br><small class="text-muted">นัด {{ \Carbon\Carbon::parse($call->appointment->appointment_date)->locale('th')->isoFormat('D MMM') }}</small>
                                            @endif
                                        @else
                                            <span class="badge bg-info"><i class="bi bi-heart-pulse me-1"></i>ติดตามผล</span>
                                            @if($call->treatment && $call->treatment->service)
                                                <br><small class="text-muted">{{ $call->treatment->service->name ?? 'รักษา' }}</small>
                                            @endif
                                        @endif
                                    </td>
                                    <td>
                                        @php
                                            $statusColors = [
                                                'called' => 'secondary',
                                                'no_answer' => 'warning',
                                                'confirmed' => 'success',
                                                'cancelled' => 'danger',
                                                'rescheduled' => 'info'
                                            ];
                                            $statusLabels = [
                                                'called' => 'โทรแล้ว',
                                                'no_answer' => 'ไม่รับสาย',
                                                'confirmed' => 'ยืนยัน',
                                                'cancelled' => 'ยกเลิก',
                                                'rescheduled' => 'เลื่อนนัด'
                                            ];
                                        @endphp
                                        <span class="badge bg-{{ $statusColors[$call->status] ?? 'secondary' }}">
                                            {{ $statusLabels[$call->status] ?? $call->status }}
                                        </span>
                                    </td>
                                    <td>
                                        @if($call->caller)
                                            {{ $call->caller->name ?? $call->caller->username }}
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($call->notes)
                                            <small>{{ Str::limit($call->notes, 50) }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if($call->patient_feedback)
                                            <small class="text-info"><i class="bi bi-chat-quote me-1"></i>{{ Str::limit($call->patient_feedback, 50) }}</small>
                                        @else
                                            <span class="text-muted">-</span>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                    @else
                    <div class="text-center py-5">
                        <i class="bi bi-telephone-x" style="font-size: 3rem; color: #cbd5e1;"></i>
                        <p class="mt-3 mb-0 text-muted">ยังไม่มีประวัติการโทร CRM</p>
                        <small class="text-muted">ประวัติจะแสดงเมื่อมีการโทรยืนยันนัดหรือติดตามผลแล้ว</small>
                    </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- MODALS SECTION -->

<!-- Add Shared User Modal -->
<div class="modal fade" id="addSharedUserModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-person-plus me-2"></i>เพิ่มผู้ใช้งานคอร์สร่วม</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addSharedUserForm" action="{{ route('course-shared-users.store') }}" method="POST">
                @csrf
                <input type="hidden" name="course_purchase_id" id="sharedUserCoursePurchaseId">
                <input type="hidden" name="shared_patient_phone" id="sharedUserPatientPhone">

                <div class="modal-body">
                    <!-- Phone Lookup Section -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            <i class="bi bi-telephone me-1"></i>เบอร์โทรศัพท์ผู้ใช้ร่วม <span class="text-danger">*</span>
                        </label>
                        <div class="input-group">
                            <input
                                type="text"
                                class="form-control"
                                id="sharedUserPhoneInput"
                                placeholder="กรอกเบอร์โทรศัพท์ (เช่น 0812345678)"
                                maxlength="10"
                                required>
                            <button class="btn btn-outline-primary" type="button" id="searchSharedUserBtn">
                                <i class="bi bi-search me-1"></i>ค้นหา
                            </button>
                        </div>
                        <small class="text-muted">กรอกเบอร์โทรของผู้ที่ลงทะเบียนในระบบแล้ว</small>
                    </div>

                    <!-- Patient Info Display (shown after search) -->
                    <div id="sharedUserInfoSection" class="mb-3 d-none">
                        <div class="alert alert-success">
                            <h6 class="alert-heading">
                                <i class="bi bi-check-circle me-1"></i>พบข้อมูลผู้ป่วย
                            </h6>
                            <div id="sharedUserInfoDisplay"></div>
                        </div>
                    </div>

                    <!-- Error Display -->
                    <div id="sharedUserErrorSection" class="mb-3 d-none">
                        <div class="alert alert-danger">
                            <i class="bi bi-exclamation-triangle me-1"></i>
                            <span id="sharedUserErrorMessage"></span>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ความสัมพันธ์</label>
                        <select class="form-select" name="relationship">
                            <option value="">เลือกความสัมพันธ์...</option>
                            <option value="สามี/ภรรยา">สามี/ภรรยา</option>
                            <option value="บิดา/มารดา">บิดา/มารดา</option>
                            <option value="บุตร/ธิดา">บุตร/ธิดา</option>
                            <option value="ญาติ">ญาติ</option>
                            <option value="เพื่อน">เพื่อน</option>
                            <option value="อื่นๆ">อื่นๆ</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">หมายเหตุ (ถ้ามี)</label>
                        <textarea class="form-control" name="notes" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary" id="confirmAddSharedUserBtn" disabled>
                        <i class="bi bi-check-circle me-1"></i>เพิ่มผู้ใช้ร่วม
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Treatment Modal (with Audit Log) -->
<div class="modal fade" id="editTreatmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>แก้ไขประวัติการรักษา</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bi bi-info-circle me-2"></i>
                    <strong>คำเตือน:</strong> การแก้ไขประวัติการรักษาจะถูกบันทึกใน Audit Log
                </div>

                <form>
                    <div class="mb-3">
                        <label class="form-label text-danger">* เหตุผลในการแก้ไข (บังคับ)</label>
                        <textarea class="form-control border-danger" rows="3" placeholder="กรุณาระบุเหตุผลในการแก้ไขข้อมูล..." required></textarea>
                        <small class="text-muted">ข้อมูลนี้จะถูกบันทึกเพื่อการตรวจสอบ</small>
                    </div>

                    <hr>

                    <div class="mb-3">
                        <label class="form-label">รายละเอียดการรักษา</label>
                        <textarea class="form-control" rows="4">Ultrasound 10 นาที, TENS 15 นาที, Exercise therapy 20 นาที</textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-warning">
                    <i class="bi bi-pencil me-1"></i>บันทึกการแก้ไข
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Treatment Modal (with Audit Log) -->
<div class="modal fade" id="deleteTreatmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-octagon me-2"></i>ลบประวัติการรักษา</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <i class="bi bi-exclamation-triangle-fill me-2"></i>
                    <strong>คำเตือน:</strong> การลบข้อมูลนี้ไม่สามารถกู้คืนได้
                </div>

                <form>
                    <div class="mb-3">
                        <label class="form-label text-danger">* เหตุผลในการลบ (บังคับ)</label>
                        <textarea class="form-control border-danger" rows="3" placeholder="กรุณาระบุเหตุผลในการลบข้อมูล..." required></textarea>
                        <small class="text-muted">ข้อมูลนี้จะถูกบันทึกใน Audit Log เพื่อการตรวจสอบ</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">ผู้อนุมัติ (ถ้ามี)</label>
                        <input type="text" class="form-control" placeholder="ชื่อผู้อนุมัติการลบ...">
                    </div>

                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" id="confirmDelete">
                        <label class="form-check-label text-danger" for="confirmDelete">
                            ข้าพเจ้ายืนยันการลบข้อมูลและเข้าใจว่าไม่สามารถกู้คืนได้
                        </label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn" disabled>
                    <i class="bi bi-trash me-1"></i>ยืนยันการลบ
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Transaction Modal -->
<div class="modal fade" id="editTransactionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>แก้ไขรายการการเงิน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-warning">
                    <i class="bi bi-info-circle me-2"></i>
                    การแก้ไขรายการการเงินจะถูกบันทึกใน Audit Log
                </div>

                <form>
                    <div class="mb-3">
                        <label class="form-label text-danger">* เหตุผลในการแก้ไข</label>
                        <textarea class="form-control border-danger" rows="2" placeholder="ระบุเหตุผล..." required></textarea>
                    </div>

                    <hr>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">เลขที่บิล</label>
                            <input type="text" class="form-control" value="INV-2024-0125">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">จำนวนเงิน</label>
                            <input type="number" class="form-control" value="8500">
                        </div>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-warning">
                    <i class="bi bi-check-circle me-1"></i>บันทึกการแก้ไข
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Transaction Modal -->
<div class="modal fade" id="deleteTransactionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-trash me-2"></i>ลบรายการการเงิน</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-danger">
                    <strong>คำเตือน:</strong> การลบรายการการเงินจะส่งผลต่อยอดรวม
                </div>

                <form>
                    <div class="mb-3">
                        <label class="form-label text-danger">* เหตุผลในการลบ</label>
                        <textarea class="form-control border-danger" rows="3" placeholder="ระบุเหตุผล..." required></textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-danger">
                    <i class="bi bi-trash me-1"></i>ยืนยันการลบ
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Edit Note Modal -->
<div class="modal fade" id="editNoteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-pencil me-2"></i>แก้ไขบันทึก</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="mb-3">
                        <label class="form-label">ประเภทบันทึก</label>
                        <select class="form-select">
                            <option selected>บันทึกสำคัญ</option>
                            <option>พฤติกรรมดี</option>
                            <option>นัดหมาย</option>
                            <option>ทั่วไป</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">เนื้อหา</label>
                        <textarea class="form-control" rows="4">ลูกค้าแจ้งว่าแพ้ยา Penicillin ห้ามให้ยาในกลุ่มนี้เด็ดขาด รวมถึงยาปฏิชีวนะในกลุ่ม Beta-lactam</textarea>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>บันทึก
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Delete Note Modal -->
<div class="modal fade" id="deleteNoteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-trash me-2"></i>ลบบันทึก</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>คุณแน่ใจหรือไม่ที่จะลบบันทึกนี้?</p>
                <div class="alert alert-danger">
                    <small>บันทึกที่ถูกลบจะไม่สามารถกู้คืนได้</small>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-danger">
                    <i class="bi bi-trash me-1"></i>ยืนยันการลบ
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View Treatment Modal -->
<div class="modal fade" id="viewTreatmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: #0ea5e9; color: white;">
                <h5 class="modal-title"><i class="bi bi-clipboard2-pulse me-2"></i>รายละเอียดการรักษา</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div id="treatmentDetailContent">
                    <div class="text-center py-4">
                        <div class="spinner-border text-primary" role="status"></div>
                        <p class="mt-2">กำลังโหลด...</p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

<!-- View Transaction Modal -->
<div class="modal fade" id="viewTransactionModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="bi bi-eye me-2"></i>รายละเอียดการทำรายการ</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="row mb-3">
                    <div class="col-md-4">
                        <label class="form-label text-muted">วันที่</label>
                        <p class="fw-bold">18 ม.ค. 2567</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted">เลขที่บิล</label>
                        <p class="fw-bold text-primary">INV-2024-0125</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted">สถานะ</label>
                        <p><span class="badge bg-success">ชำระแล้ว</span></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-8">
                        <label class="form-label text-muted">รายการ</label>
                        <p class="fw-bold">คอร์สกายภาพบำบัดหลัง 10 ครั้ง</p>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label text-muted">ประเภท</label>
                        <p><span class="badge bg-info">คอร์ส</span></p>
                    </div>
                </div>
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label text-muted">จำนวนเงิน</label>
                        <h4 class="text-success">฿8,500</h4>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label text-muted">ช่องทางชำระเงิน</label>
                        <p class="fw-bold">เงินสด</p>
                    </div>
                </div>
                <hr>
                <small class="text-muted">บันทึกโดย: แคชเชียร์สมใจ | วันที่บันทึก: 18 ม.ค. 2567 10:30 น.</small>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i>พิมพ์ใบเสร็จ
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Add Transaction Modal -->
<div class="modal fade" id="addTransactionModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title"><i class="bi bi-plus-circle me-2"></i>เพิ่มรายการการเงิน</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">วันที่</label>
                            <input type="date" class="form-control" value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">เลขที่บิล</label>
                            <input type="text" class="form-control" placeholder="INV-2024-XXXX">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">รายการ</label>
                        <input type="text" class="form-control" placeholder="ระบุรายการ...">
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ประเภท</label>
                            <select class="form-select">
                                <option>บริการ</option>
                                <option>คอร์ส</option>
                                <option>สินค้า</option>
                                <option>คืนเงิน</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">จำนวนเงิน</label>
                            <input type="number" class="form-control" placeholder="0.00">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">สถานะ</label>
                        <select class="form-select">
                            <option>ชำระแล้ว</option>
                            <option>รอชำระ</option>
                            <option>ยกเลิก</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary">
                    <i class="bi bi-check-circle me-1"></i>เพิ่มรายการ
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Cancel Course Modal -->
<div class="modal fade" id="cancelCourseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-warning text-dark">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>ยกเลิกคอร์ส</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="cancelCourseId">

                <div class="alert alert-warning">
                    <i class="bi bi-info-circle-fill me-2"></i>
                    <strong>หมายเหตุ:</strong> การยกเลิกคอร์สจะทำการคืนเงินตามจำนวนครั้งที่เหลือ
                </div>

                <div class="mb-3">
                    <label class="form-label fw-bold">คอร์สที่จะยกเลิก:</label>
                    <p id="cancelCourseNameDisplay" class="text-danger fw-bold mb-1"></p>
                    <div class="d-flex gap-3 text-muted small">
                        <span><i class="bi bi-cash me-1"></i>ราคา: <strong id="cancelCoursePriceDisplay">0</strong> บาท</span>
                        <span><i class="bi bi-check2-circle me-1"></i>ใช้ไปแล้ว: <strong id="cancelCourseUsedDisplay">0</strong> ครั้ง</span>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label text-danger">* เหตุผลในการยกเลิก (บังคับ)</label>
                    <textarea
                        class="form-control border-warning"
                        id="cancelCourseReason"
                        rows="3"
                        placeholder="กรุณาระบุเหตุผล เช่น ลูกค้าต้องการยกเลิก, เปลี่ยนใจ, ฯลฯ"
                        required
                        minlength="5"></textarea>
                </div>

                <div class="form-check">
                    <input class="form-check-input" type="checkbox" id="confirmCancelCourse">
                    <label class="form-check-label text-danger" for="confirmCancelCourse">
                        ยืนยันการยกเลิกคอร์สและคืนเงินให้ลูกค้า
                    </label>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-warning" id="confirmCancelCourseBtn" onclick="submitCancelCourse()" disabled>
                    <i class="bi bi-x-circle me-1"></i>ยืนยันยกเลิกคอร์ส
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Course Usage History Modal -->
<div class="modal fade" id="courseUsageHistoryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-info text-white">
                <h5 class="modal-title"><i class="bi bi-clock-history me-2"></i>ประวัติการใช้คอร์ส</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <h6 class="fw-bold">คอร์ส: <span id="usageHistoryCourseNameDisplay" class="text-primary"></span></h6>
                </div>

                <div id="usageHistoryContent">
                    <!-- Usage history will be loaded here via JavaScript/AJAX -->
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="table-light">
                                <tr>
                                    <th width="12%">วันที่</th>
                                    <th width="10%">เวลา</th>
                                    <th width="10%">จำนวนครั้ง</th>
                                    <th width="18%">ผู้ใช้เซสชั่น</th>
                                    <th width="18%">PT/แพทย์ผู้รักษา</th>
                                    <th width="32%">หมายเหตุการรักษา</th>
                                </tr>
                            </thead>
                            <tbody id="usageHistoryTableBody">
                                <!-- Sample data - will be replaced with real data -->
                                <tr>
                                    <td>18 ม.ค. 2567</td>
                                    <td>14:30 น.</td>
                                    <td class="text-center"><span class="badge bg-success">1 ครั้ง</span></td>
                                    <td><span class="text-primary fw-bold">นายสมชาย ใจดี</span></td>
                                    <td>นพ.สมชาย ใจดี</td>
                                    <td>Ultrasound 10 นาที, TENS 15 นาที</td>
                                </tr>
                                <tr>
                                    <td>15 ม.ค. 2567</td>
                                    <td>10:00 น.</td>
                                    <td class="text-center"><span class="badge bg-success">1 ครั้ง</span></td>
                                    <td><span class="text-success fw-bold">นางสมหญิง รักดี (ผู้ใช้ร่วม)</span></td>
                                    <td>นพ.สมหญิง รักดี</td>
                                    <td>Exercise therapy 30 นาที</td>
                                </tr>
                                <tr>
                                    <td>12 ม.ค. 2567</td>
                                    <td>16:00 น.</td>
                                    <td class="text-center"><span class="badge bg-success">1 ครั้ง</span></td>
                                    <td><span class="text-primary fw-bold">นายสมชาย ใจดี</span></td>
                                    <td>นพ.สมชาย ใจดี</td>
                                    <td>Manual therapy, Hot pack</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <div class="alert alert-info mt-3">
                        <i class="bi bi-info-circle me-2"></i>
                        <strong>สรุปการใช้งาน:</strong> ใช้ไปแล้ว 3 ครั้ง จากทั้งหมด 10 ครั้ง (คงเหลือ 7 ครั้ง)
                    </div>
                </div>

                <!-- Empty State -->
                <div id="usageHistoryEmpty" class="text-center py-5" style="display: none;">
                    <i class="bi bi-inbox" style="font-size: 4rem; color: #9ca3af;"></i>
                    <h6 class="text-muted mt-3">ยังไม่มีประวัติการใช้คอร์ส</h6>
                    <p class="text-muted small">เมื่อมีการใช้คอร์สจะแสดงประวัติที่นี่</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-primary" onclick="window.print()">
                    <i class="bi bi-printer me-1"></i>พิมพ์ประวัติ
                </button>
            </div>
        </div>
    </div>
</div>

<!-- Quick Appointment Modal -->
<div class="modal fade" id="quickAppointmentModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <form method="POST" action="{{ route('appointments.quickStore') }}">
                @csrf
                <input type="hidden" name="patient_id" value="{{ $patient->id ?? '' }}">

                <div class="modal-header bg-primary text-white">
                    <h5 class="modal-title">
                        <i class="bi bi-calendar-plus me-2"></i>นัดหมายด่วน
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <!-- Patient Info (Read-only) -->
                    <div class="mb-3 p-3 bg-light rounded">
                        <div class="row">
                            <div class="col-6">
                                <small class="text-muted">ชื่อคนไข้</small>
                                <p class="mb-0 fw-bold">{{ $patient->name ?? 'N/A' }}</p>
                            </div>
                            <div class="col-6">
                                <small class="text-muted">เบอร์โทร</small>
                                <p class="mb-0 fw-bold">{{ $patient->phone ?? 'N/A' }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Date -->
                    <div class="mb-3">
                        <label for="quick_appointment_date" class="form-label fw-bold">
                            วันที่นัด <span class="text-danger">*</span>
                        </label>
                        <input type="date" class="form-control" id="quick_appointment_date"
                               name="appointment_date" required min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
                    </div>

                    <!-- Time -->
                    <div class="mb-3">
                        <label for="quick_appointment_time" class="form-label fw-bold">
                            เวลา <span class="text-danger">*</span>
                        </label>
                        <select class="form-select" id="quick_appointment_time" name="appointment_time" required>
                            <option value="">เลือกเวลา</option>
                            <optgroup label="ช่วงเช้า (09:00-12:00)">
                                <option value="09:00">09:00 น.</option>
                                <option value="09:30">09:30 น.</option>
                                <option value="10:00">10:00 น.</option>
                                <option value="10:30">10:30 น.</option>
                                <option value="11:00">11:00 น.</option>
                                <option value="11:30">11:30 น.</option>
                            </optgroup>
                            <optgroup label="ช่วงบ่าย (12:00-19:00)">
                                <option value="12:00">12:00 น.</option>
                                <option value="12:30">12:30 น.</option>
                                <option value="13:00">13:00 น.</option>
                                <option value="13:30">13:30 น.</option>
                                <option value="14:00">14:00 น.</option>
                                <option value="14:30">14:30 น.</option>
                                <option value="15:00">15:00 น.</option>
                                <option value="15:30">15:30 น.</option>
                                <option value="16:00">16:00 น.</option>
                                <option value="16:30">16:30 น.</option>
                                <option value="17:00">17:00 น.</option>
                                <option value="17:30">17:30 น.</option>
                                <option value="18:00">18:00 น.</option>
                                <option value="18:30">18:30 น.</option>
                                <option value="19:00">19:00 น.</option>
                            </optgroup>
                        </select>
                    </div>

                    <!-- Purpose -->
                    <div class="mb-3">
                        <label class="form-label fw-bold">
                            วัตถุประสงค์ <span class="text-danger">*</span>
                        </label>
                        <div class="d-flex flex-column gap-2">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="purpose"
                                       id="quick_purpose_follow_up" value="FOLLOW_UP" required>
                                <label class="form-check-label" for="quick_purpose_follow_up">
                                    <i class="bi bi-clipboard2-pulse me-1"></i>ติดตามอาการ
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="purpose"
                                       id="quick_purpose_pt" value="PHYSICAL_THERAPY">
                                <label class="form-check-label" for="quick_purpose_pt">
                                    <i class="bi bi-activity me-1"></i>นัดทำกายภาพบำบัด
                                </label>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i>บันทึกนัดหมาย
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- View Invoice Detail Modal -->
<div class="modal fade" id="viewInvoiceModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-receipt me-2"></i>รายละเอียดการชำระเงิน</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body" id="invoiceDetailContent">
                <div class="text-center py-4">
                    <div class="spinner-border text-primary" role="status"></div>
                    <p class="mt-2">กำลังโหลด...</p>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alert Modal -->
<div class="modal fade" id="alertModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div id="alertIcon" class="mb-3"></div>
                <h5 id="alertTitle" class="mb-2"></h5>
                <p id="alertMessage" class="text-muted mb-0"></p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0">
                <button type="button" class="btn btn-primary px-4" data-bs-dismiss="modal">ตกลง</button>
            </div>
        </div>
    </div>
</div>

<!-- Purchase Course Online Modal -->
<div class="modal fade" id="purchaseCourseOnlineModal" tabindex="-1">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-box-seam me-2"></i>ซื้อคอร์สออนไลน์</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="purchaseCourseOnlineForm">
                    <!-- Step 1: Select Package -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3"><span class="badge bg-primary me-2">1</span>เลือกคอร์ส</h6>
                        <select class="form-select" id="online_package_id" required>
                            <option value="">-- เลือกคอร์ส --</option>
                            @foreach($coursePackages ?? [] as $package)
                            <option value="{{ $package->id }}"
                                    data-price="{{ $package->price }}"
                                    data-sessions="{{ $package->total_sessions }}">
                                {{ $package->name }} - {{ $package->total_sessions }} ครั้ง (฿{{ number_format($package->price) }})
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Step 2: Payment Type -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3"><span class="badge bg-primary me-2">2</span>ประเภทการชำระ</h6>
                        <div class="row g-2">
                            <div class="col-6">
                                <div class="form-check card p-3">
                                    <input class="form-check-input" type="radio" name="online_payment_type" id="online_full" value="full" checked>
                                    <label class="form-check-label w-100" for="online_full">
                                        <strong>จ่ายเต็มจำนวน</strong>
                                        <div class="text-muted small" id="fullPayLabel">฿0</div>
                                    </label>
                                </div>
                            </div>
                            <div class="col-6">
                                <div class="form-check card p-3">
                                    <input class="form-check-input" type="radio" name="online_payment_type" id="online_installment" value="installment">
                                    <label class="form-check-label w-100" for="online_installment">
                                        <strong>ผ่อน 3 งวด</strong>
                                        <div class="text-muted small" id="installmentPayLabel">งวดละ ฿0</div>
                                    </label>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Step 3: Select Sellers -->
                    <div class="mb-4">
                        <h6 class="fw-bold mb-3"><span class="badge bg-primary me-2">3</span>เลือกคนขาย (เลือกได้หลายคน)</h6>
                        <div class="seller-list" style="max-height: 150px; overflow-y: auto;">
                            @foreach($salesStaff ?? [] as $staff)
                            <div class="form-check">
                                <input class="form-check-input online-seller-checkbox" type="checkbox" value="{{ $staff->id }}" id="online_seller_{{ $staff->id }}">
                                <label class="form-check-label" for="online_seller_{{ $staff->id }}">
                                    {{ $staff->name }}
                                </label>
                            </div>
                            @endforeach
                        </div>
                    </div>

                    <!-- Step 4: Payment Method -->
                    <div class="mb-3">
                        <h6 class="fw-bold mb-3"><span class="badge bg-primary me-2">4</span>วิธีชำระเงิน</h6>
                        <select class="form-select" id="online_payment_method" required>
                            <option value="transfer">โอนเงิน</option>
                            <option value="credit_card">บัตรเครดิต</option>
                            <option value="cash">เงินสด</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" onclick="submitOnlinePurchase()">
                    <i class="bi bi-cart-check me-1"></i>ยืนยันการซื้อ
                </button>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script>
// Show Alert Modal Function
function showAlert(type, title, message, callback = null) {
    const iconMap = {
        'success': '<i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>',
        'error': '<i class="bi bi-x-circle-fill text-danger" style="font-size: 3rem;"></i>',
        'warning': '<i class="bi bi-exclamation-circle-fill text-warning" style="font-size: 3rem;"></i>',
        'info': '<i class="bi bi-info-circle-fill text-info" style="font-size: 3rem;"></i>'
    };

    document.getElementById('alertIcon').innerHTML = iconMap[type] || iconMap['info'];
    document.getElementById('alertTitle').textContent = title;
    document.getElementById('alertMessage').innerHTML = message;

    const alertModal = new bootstrap.Modal(document.getElementById('alertModal'));
    alertModal.show();

    if (callback) {
        document.getElementById('alertModal').addEventListener('hidden.bs.modal', function handler() {
            callback();
            this.removeEventListener('hidden.bs.modal', handler);
        });
    }
}

// Online Course Purchase Functions
document.getElementById('online_package_id').addEventListener('change', function() {
    const selected = this.options[this.selectedIndex];
    const price = parseFloat(selected.dataset.price || 0);
    const installment = Math.ceil(price / 3);

    document.getElementById('fullPayLabel').textContent = '฿' + price.toLocaleString();
    document.getElementById('installmentPayLabel').textContent = 'งวดละ ฿' + installment.toLocaleString();
});

function submitOnlinePurchase() {
    const packageId = document.getElementById('online_package_id').value;
    const paymentType = document.querySelector('input[name="online_payment_type"]:checked').value;
    const paymentMethod = document.getElementById('online_payment_method').value;

    // Get selected sellers
    const sellerIds = [];
    document.querySelectorAll('.online-seller-checkbox:checked').forEach(cb => {
        sellerIds.push(cb.value);
    });

    if (!packageId) {
        showAlert('warning', 'กรุณาเลือกคอร์ส', 'โปรดเลือกคอร์สที่ต้องการซื้อ');
        return;
    }

    if (sellerIds.length === 0) {
        showAlert('warning', 'กรุณาเลือกคนขาย', 'โปรดเลือกคนขายอย่างน้อย 1 คน');
        return;
    }

    const data = {
        package_id: packageId,
        payment_type: paymentType,
        seller_ids: sellerIds,
        payment_method: paymentMethod
    };

    fetch('/patients/{{ $patient->id }}/purchase-course-online', {
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
            bootstrap.Modal.getInstance(document.getElementById('purchaseCourseOnlineModal')).hide();
            showAlert('success', 'ซื้อคอร์สเรียบร้อย!',
                'เลขที่คอร์ส: <strong>' + result.course_number + '</strong><br>' +
                'เลขที่ใบแจ้งหนี้: <strong>' + result.invoice_number + '</strong>',
                function() { location.reload(); }
            );
        } else {
            showAlert('error', 'เกิดข้อผิดพลาด', result.message);
        }
    })
    .catch(err => {
        showAlert('error', 'เกิดข้อผิดพลาด', err.message);
    });
}

// View Invoice Detail Function
function viewInvoiceDetail(invoiceId) {
    const modal = new bootstrap.Modal(document.getElementById('viewInvoiceModal'));
    const content = document.getElementById('invoiceDetailContent');

    content.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2">กำลังโหลด...</p>
        </div>
    `;

    modal.show();

    fetch('/api/invoice-detail/' + invoiceId)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const inv = data.invoice;
                let itemsHtml = '';
                if (inv.items && inv.items.length > 0) {
                    itemsHtml = `
                        <table class="table table-sm">
                            <thead>
                                <tr>
                                    <th>รายการ</th>
                                    <th class="text-center">จำนวน</th>
                                    <th class="text-end">ราคา/หน่วย</th>
                                    <th class="text-end">รวม</th>
                                </tr>
                            </thead>
                            <tbody>
                                ${inv.items.map(item => `
                                    <tr>
                                        <td>${item.description}</td>
                                        <td class="text-center">${item.quantity}</td>
                                        <td class="text-end">${Number(item.unit_price).toLocaleString()}</td>
                                        <td class="text-end">${Number(item.total_amount).toLocaleString()}</td>
                                    </tr>
                                `).join('')}
                            </tbody>
                        </table>
                    `;
                }

                content.innerHTML = `
                    <!-- Header Section -->
                    <div style="background: linear-gradient(135deg, #0284c7, #0ea5e9); color: white; padding: 1.25rem; border-radius: 12px; margin-bottom: 1rem;">
                        <div class="d-flex justify-content-between align-items-start">
                            <div>
                                <h5 class="mb-1 fw-bold">${inv.invoice_number}</h5>
                                <small><i class="bi bi-calendar3 me-1"></i>${inv.date_display} ${inv.time_display}</small>
                            </div>
                            <span class="badge bg-${inv.status == 'paid' ? 'success' : 'warning'}" style="font-size: 0.85rem;">
                                ${inv.status == 'paid' ? '<i class="bi bi-check-circle me-1"></i>ชำระแล้ว' : '<i class="bi bi-clock me-1"></i>รอชำระ'}
                            </span>
                        </div>
                    </div>

                    <!-- Patient & Staff Info -->
                    <div class="row g-3 mb-3">
                        <div class="col-md-6">
                            <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; height: 100%;">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-person-circle text-primary me-2" style="font-size: 1.25rem;"></i>
                                    <span class="text-muted small">ลูกค้า</span>
                                </div>
                                <p class="fw-bold mb-0">${inv.patient_name}</p>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div style="background: #f8fafc; padding: 1rem; border-radius: 8px; height: 100%;">
                                <div class="d-flex align-items-center mb-2">
                                    <i class="bi bi-person-badge text-success me-2" style="font-size: 1.25rem;"></i>
                                    <span class="text-muted small">ผู้บันทึก</span>
                                </div>
                                <p class="fw-bold mb-0">${inv.created_by_name}</p>
                            </div>
                        </div>
                    </div>

                    ${inv.pt_name ? `
                    <!-- Treatment Info -->
                    <div style="background: linear-gradient(135deg, #f0fdf4, #dcfce7); padding: 1rem; border-radius: 8px; margin-bottom: 1rem; border-left: 4px solid #22c55e;">
                        <h6 class="fw-bold mb-3" style="color: #166534;"><i class="bi bi-activity me-2"></i>ข้อมูลการรักษา</h6>
                        <div class="row g-2">
                            <div class="col-md-6">
                                <small class="text-muted d-block">นักกายภาพบำบัด</small>
                                <span class="fw-bold">${inv.pt_name}</span>
                            </div>
                            <div class="col-md-6">
                                <small class="text-muted d-block">หัตถการ/บริการ</small>
                                <span class="fw-bold">${inv.service_name || 'ไม่ระบุ'}</span>
                            </div>
                            ${inv.duration_minutes ? `
                            <div class="col-md-6 mt-2">
                                <small class="text-muted d-block">ระยะเวลา</small>
                                <span class="fw-bold"><i class="bi bi-stopwatch me-1"></i>${inv.duration_minutes} นาที</span>
                            </div>
                            ` : ''}
                        </div>
                        ${inv.treatment_notes ? `
                        <div class="mt-3 pt-3" style="border-top: 1px solid #bbf7d0;">
                            <small class="text-muted d-block mb-1">บันทึกการรักษา</small>
                            <p class="mb-0" style="color: #166534;">${inv.treatment_notes}</p>
                        </div>
                        ` : ''}
                    </div>
                    ` : ''}

                    <!-- Items List -->
                    <div class="mb-3">
                        <h6 class="fw-bold mb-2"><i class="bi bi-list-check me-2"></i>รายการ</h6>
                        ${itemsHtml || '<p class="text-muted small">ไม่มีรายการ</p>'}
                    </div>

                    <!-- Total Section -->
                    <div style="background: #f8fafc; padding: 1rem; border-radius: 8px;">
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">ยอดรวม</span>
                            <span class="fw-medium">${Number(inv.subtotal).toLocaleString()} บาท</span>
                        </div>
                        ${inv.discount_amount > 0 ? `
                        <div class="d-flex justify-content-between mb-2">
                            <span class="text-muted">ส่วนลด</span>
                            <span class="fw-medium text-danger">-${Number(inv.discount_amount).toLocaleString()} บาท</span>
                        </div>
                        ` : ''}
                        <hr class="my-2">
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="fw-bold">ยอดชำระสุทธิ</span>
                            <span class="fw-bold text-success" style="font-size: 1.5rem;">${Number(inv.total_amount).toLocaleString()} บาท</span>
                        </div>
                    </div>
                `;
            } else {
                content.innerHTML = '<div class="text-center text-danger py-4"><i class="bi bi-exclamation-circle me-2"></i>' + (data.message || 'ไม่พบข้อมูล') + '</div>';
            }
        })
        .catch(err => {
            content.innerHTML = '<div class="text-center text-danger py-4"><i class="bi bi-exclamation-circle me-2"></i>เกิดข้อผิดพลาด</div>';
        });
}

// View Treatment Detail Function
function viewTreatmentDetail(appointmentId) {
    const modal = new bootstrap.Modal(document.getElementById('viewTreatmentModal'));
    const content = document.getElementById('treatmentDetailContent');

    // Show loading
    content.innerHTML = `
        <div class="text-center py-4">
            <div class="spinner-border text-primary" role="status"></div>
            <p class="mt-2">กำลังโหลด...</p>
        </div>
    `;

    modal.show();

    // Fetch treatment detail
    fetch('/api/treatment-detail/' + appointmentId)
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                const t = data.treatment;
                const a = data.appointment;
                const inv = data.invoice;

                let invoiceHtml = '';
                if (inv) {
                    invoiceHtml = `
                        <div class="mt-3 pt-3" style="border-top: 1px solid #e2e8f0;">
                            <h6 class="fw-bold mb-3"><i class="bi bi-receipt me-2"></i>ข้อมูลการชำระเงิน</h6>
                            <div class="row">
                                <div class="col-md-6">
                                    <label class="form-label text-muted small">เลขที่ใบเสร็จ</label>
                                    <p class="fw-bold text-primary">${inv.invoice_number}</p>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label text-muted small">ยอดชำระ</label>
                                    <p class="fw-bold text-success">${Number(inv.total_amount).toLocaleString()} บาท</p>
                                </div>
                            </div>
                            ${inv.items && inv.items.length > 0 ? `
                                <label class="form-label text-muted small">รายการ</label>
                                <ul class="list-unstyled mb-0">
                                    ${inv.items.map(item => `
                                        <li class="py-1"><i class="bi bi-check2 me-2 text-success"></i>${item.description} - ${Number(item.total_amount).toLocaleString()} บาท</li>
                                    `).join('')}
                                </ul>
                            ` : ''}
                        </div>
                    `;
                }

                content.innerHTML = `
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">วันที่รักษา</label>
                            <p class="fw-bold">${a.date_display} ${a.time_display}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">สถานะ</label>
                            <p><span class="badge bg-success">เสร็จสิ้น</span></p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">หัตถการ/บริการ</label>
                            <p class="fw-bold">${t.service_name || 'ไม่ระบุ'}</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">นักกายภาพบำบัด</label>
                            <p class="fw-bold">${t.pt_name || 'ไม่ระบุ'}</p>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-md-6">
                            <label class="form-label text-muted small">ระยะเวลารักษา</label>
                            <p class="fw-bold">${t.duration_minutes || 0} นาที</p>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label text-muted small">สถานะการเงิน</label>
                            <p>
                                ${t.billing_status === 'paid' ? '<span class="badge bg-success">ชำระแล้ว</span>' : '<span class="badge bg-warning">รอชำระ</span>'}
                            </p>
                        </div>
                    </div>
                    ${t.treatment_notes ? `
                        <div class="mb-3">
                            <label class="form-label text-muted small">บันทึกการรักษา</label>
                            <div class="bg-light p-3 rounded">
                                <p class="mb-0">${t.treatment_notes}</p>
                            </div>
                        </div>
                    ` : ''}
                    ${invoiceHtml}
                    <hr>
                    <small class="text-muted">
                        <i class="bi bi-clock me-1"></i>เวลาเริ่ม: ${t.started_at || '-'} | เวลาเสร็จ: ${t.completed_at || '-'}
                    </small>
                `;
            } else {
                content.innerHTML = `
                    <div class="text-center py-4">
                        <i class="bi bi-exclamation-circle text-warning" style="font-size: 3rem;"></i>
                        <p class="mt-2">${data.message || 'ไม่พบข้อมูลการรักษา'}</p>
                    </div>
                `;
            }
        })
        .catch(err => {
            content.innerHTML = `
                <div class="text-center py-4">
                    <i class="bi bi-x-circle text-danger" style="font-size: 3rem;"></i>
                    <p class="mt-2">เกิดข้อผิดพลาด: ${err.message}</p>
                </div>
            `;
        });
}

document.addEventListener('DOMContentLoaded', function() {
    // Tab persistence
    const tabLinks = document.querySelectorAll('#patientTabs .nav-link');
    const lastActiveTab = localStorage.getItem('activePatientTab');

    if (lastActiveTab) {
        const tab = document.querySelector(`[href="${lastActiveTab}"]`);
        if (tab) {
            new bootstrap.Tab(tab).show();
        }
    }

    tabLinks.forEach(tab => {
        tab.addEventListener('shown.bs.tab', function (e) {
            localStorage.setItem('activePatientTab', e.target.getAttribute('href'));
        });
    });

    // Enable/disable delete button based on checkbox
    const confirmDeleteCheckbox = document.getElementById('confirmDelete');
    const confirmDeleteBtn = document.getElementById('confirmDeleteBtn');

    if (confirmDeleteCheckbox) {
        confirmDeleteCheckbox.addEventListener('change', function() {
            confirmDeleteBtn.disabled = !this.checked;
        });
    }

    // Reset modals on close
    document.querySelectorAll('.modal').forEach(modal => {
        modal.addEventListener('hidden.bs.modal', function () {
            // Reset forms
            const forms = this.querySelectorAll('form');
            forms.forEach(form => form.reset());

            // Reset delete confirmation
            if (confirmDeleteCheckbox) {
                confirmDeleteCheckbox.checked = false;
                confirmDeleteBtn.disabled = true;
            }
        });
    });

    // Delete Course Modal Logic
    // Cancel Course Modal Logic
    const cancelCourseModal = document.getElementById('cancelCourseModal');
    const confirmCancelCourseCheckbox = document.getElementById('confirmCancelCourse');
    const confirmCancelCourseBtn = document.getElementById('confirmCancelCourseBtn');
    const cancelCourseReason = document.getElementById('cancelCourseReason');

    if (cancelCourseModal) {
        cancelCourseModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const courseId = button.getAttribute('data-course-id');
            const courseName = button.getAttribute('data-course-name');
            const coursePrice = button.getAttribute('data-course-price');
            const usedSessions = button.getAttribute('data-used-sessions');

            // Update modal content
            document.getElementById('cancelCourseId').value = courseId;
            document.getElementById('cancelCourseNameDisplay').textContent = courseName;
            document.getElementById('cancelCoursePriceDisplay').textContent = Number(coursePrice).toLocaleString();
            document.getElementById('cancelCourseUsedDisplay').textContent = usedSessions;
        });

        // Enable/disable cancel button based on checkbox and reason length
        function checkCancelCourseValidity() {
            const isChecked = confirmCancelCourseCheckbox.checked;
            const reasonLength = cancelCourseReason.value.trim().length;
            const isValid = isChecked && reasonLength >= 5;

            confirmCancelCourseBtn.disabled = !isValid;
        }

        if (confirmCancelCourseCheckbox) {
            confirmCancelCourseCheckbox.addEventListener('change', checkCancelCourseValidity);
        }

        if (cancelCourseReason) {
            cancelCourseReason.addEventListener('input', checkCancelCourseValidity);
        }

        // Reset on modal close
        cancelCourseModal.addEventListener('hidden.bs.modal', function () {
            confirmCancelCourseCheckbox.checked = false;
            cancelCourseReason.value = '';
            confirmCancelCourseBtn.disabled = true;
        });
    }

    // Submit cancel course via AJAX
    window.submitCancelCourse = function() {
        const courseId = document.getElementById('cancelCourseId').value;
        const reason = document.getElementById('cancelCourseReason').value;

        if (!courseId || !reason || reason.length < 5) {
            alert('กรุณากรอกข้อมูลให้ครบ');
            return;
        }

        confirmCancelCourseBtn.disabled = true;
        confirmCancelCourseBtn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>กำลังดำเนินการ...';

        fetch('/billing/cancel-course', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                course_purchase_id: courseId,
                reason: reason
            })
        })
        .then(res => res.json())
        .then(result => {
            if (result.success) {
                bootstrap.Modal.getInstance(cancelCourseModal).hide();
                alert('ยกเลิกคอร์สสำเร็จ\\n\\nเลขที่คืนเงิน: ' + result.refund_number + '\\nยอดคืน: ' + Number(result.refund_amount).toLocaleString() + ' บาท');
                location.reload();
            } else {
                alert('เกิดข้อผิดพลาด: ' + (result.message || 'Unknown error'));
                confirmCancelCourseBtn.disabled = false;
                confirmCancelCourseBtn.innerHTML = '<i class="bi bi-x-circle me-1"></i>ยืนยันยกเลิกคอร์ส';
            }
        })
        .catch(err => {
            alert('เกิดข้อผิดพลาด: ' + err.message);
            confirmCancelCourseBtn.disabled = false;
            confirmCancelCourseBtn.innerHTML = '<i class="bi bi-x-circle me-1"></i>ยืนยันยกเลิกคอร์ส';
        });
    };

    // Course Usage History Modal Logic
    const usageHistoryModal = document.getElementById('courseUsageHistoryModal');

    if (usageHistoryModal) {
        usageHistoryModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            const courseId = button.getAttribute('data-course-id');
            const courseName = button.getAttribute('data-course-name');
            const usedSessions = button.getAttribute('data-used-sessions');
            const totalSessions = button.getAttribute('data-total-sessions');

            // Update modal content
            document.getElementById('usageHistoryCourseNameDisplay').textContent = courseName;

            // Show loading state
            const contentDiv = document.getElementById('usageHistoryContent');
            const emptyDiv = document.getElementById('usageHistoryEmpty');
            const tbody = document.getElementById('usageHistoryTableBody');

            tbody.innerHTML = '<tr><td colspan="6" class="text-center"><i class="bi bi-hourglass-split me-2"></i>กำลังโหลดข้อมูล...</td></tr>';
            contentDiv.style.display = 'block';
            emptyDiv.style.display = 'none';

            // Load actual usage history via AJAX
            fetch(`{{ url('/api/course-usage-logs') }}/${courseId}`)
                .then(response => response.json())
                .then(data => {
                    tbody.innerHTML = ''; // Clear loading message

                    if (data.usage_history.length === 0) {
                        contentDiv.style.display = 'none';
                        emptyDiv.style.display = 'block';
                    } else {
                        contentDiv.style.display = 'block';
                        emptyDiv.style.display = 'none';

                        data.usage_history.forEach(usage => {
                            const row = `<tr>
                                <td>${usage.date}</td>
                                <td>${usage.time}</td>
                                <td class="text-center"><span class="badge bg-success">${usage.sessions} ครั้ง</span></td>
                                <td><span class="text-primary fw-bold">${usage.used_by_patient_name}</span></td>
                                <td>${usage.pt_name}</td>
                                <td>${usage.notes}</td>
                            </tr>`;
                            tbody.innerHTML += row;
                        });

                        // Update summary
                        const summaryDiv = contentDiv.querySelector('.alert-info');
                        if (summaryDiv) {
                            summaryDiv.innerHTML = `
                                <i class="bi bi-info-circle me-2"></i>
                                <strong>สรุปการใช้งาน:</strong> ใช้ไปแล้ว ${data.used_sessions} ครั้ง จากทั้งหมด ${data.total_sessions} ครั้ง (คงเหลือ ${data.remaining_sessions} ครั้ง)
                            `;
                        }
                    }
                })
                .catch(error => {
                    console.error('Error loading usage history:', error);
                    tbody.innerHTML = '<tr><td colspan="6" class="text-center text-danger"><i class="bi bi-exclamation-triangle me-2"></i>เกิดข้อผิดพลาดในการโหลดข้อมูล</td></tr>';
                });
        });
    }

    // ===============================================
    // ADD SHARED USER MODAL - Phone Lookup Logic
    // ===============================================
    const addSharedUserModal = document.getElementById('addSharedUserModal');
    const sharedUserPhoneInput = document.getElementById('sharedUserPhoneInput');
    const searchSharedUserBtn = document.getElementById('searchSharedUserBtn');
    const sharedUserInfoSection = document.getElementById('sharedUserInfoSection');
    const sharedUserInfoDisplay = document.getElementById('sharedUserInfoDisplay');
    const sharedUserErrorSection = document.getElementById('sharedUserErrorSection');
    const sharedUserErrorMessage = document.getElementById('sharedUserErrorMessage');
    const confirmAddSharedUserBtn = document.getElementById('confirmAddSharedUserBtn');
    const sharedUserPatientPhone = document.getElementById('sharedUserPatientPhone');
    const sharedUserCoursePurchaseId = document.getElementById('sharedUserCoursePurchaseId');

    let foundPatient = null;

    // Search patient by phone
    function searchPatientByPhone() {
        const phone = sharedUserPhoneInput.value.trim();

        // Reset state
        sharedUserInfoSection.classList.add('d-none');
        sharedUserErrorSection.classList.add('d-none');
        confirmAddSharedUserBtn.disabled = true;
        foundPatient = null;

        if (phone.length < 9) {
            sharedUserErrorSection.classList.remove('d-none');
            sharedUserErrorMessage.textContent = 'กรุณากรอกเบอร์โทรศัพท์ให้ครบ (อย่างน้อย 9 หลัก)';
            return;
        }

        // Show loading state
        searchSharedUserBtn.disabled = true;
        searchSharedUserBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>กำลังค้นหา...';

        // Call API to search patient
        fetch(`/api/course-shared-users/search-patient?phone=${encodeURIComponent(phone)}`)
            .then(response => response.json())
            .then(data => {
                if (data.patient) {
                    // Patient found!
                    foundPatient = data.patient;
                    sharedUserInfoSection.classList.remove('d-none');
                    sharedUserInfoDisplay.innerHTML = `
                        <p class="mb-1"><strong>ชื่อ:</strong> ${data.patient.name}</p>
                        <p class="mb-1"><strong>HN:</strong> ${data.patient.hn || 'ไม่มี'}</p>
                        <p class="mb-0"><strong>เบอร์โทร:</strong> ${data.patient.phone}</p>
                    `;
                    sharedUserPatientPhone.value = data.patient.phone;
                    confirmAddSharedUserBtn.disabled = false;
                } else {
                    // Patient not found
                    sharedUserErrorSection.classList.remove('d-none');
                    sharedUserErrorMessage.textContent = `ไม่พบคนไข้ที่มีเบอร์โทร "${phone}" ในระบบ กรุณาตรวจสอบเบอร์โทรอีกครั้ง`;
                }
            })
            .catch(error => {
                console.error('Error searching patient:', error);
                sharedUserErrorSection.classList.remove('d-none');
                sharedUserErrorMessage.textContent = 'เกิดข้อผิดพลาดในการค้นหา กรุณาลองใหม่อีกครั้ง';
            })
            .finally(() => {
                // Reset button state
                searchSharedUserBtn.disabled = false;
                searchSharedUserBtn.innerHTML = '<i class="bi bi-search me-1"></i>ค้นหา';
            });
    }

    if (searchSharedUserBtn) {
        searchSharedUserBtn.addEventListener('click', searchPatientByPhone);
    }

    // Allow search on Enter key
    if (sharedUserPhoneInput) {
        sharedUserPhoneInput.addEventListener('keypress', function(e) {
            if (e.key === 'Enter') {
                e.preventDefault();
                searchPatientByPhone();
            }
        });
    }

    // Set course_purchase_id when modal opens
    if (addSharedUserModal) {
        addSharedUserModal.addEventListener('show.bs.modal', function (event) {
            const button = event.relatedTarget;
            if (button) {
                const courseId = button.getAttribute('data-course-id');
                if (courseId) {
                    sharedUserCoursePurchaseId.value = courseId;
                }
            }
        });

        // Reset modal on close
        addSharedUserModal.addEventListener('hidden.bs.modal', function () {
            sharedUserPhoneInput.value = '';
            sharedUserInfoSection.classList.add('d-none');
            sharedUserErrorSection.classList.add('d-none');
            confirmAddSharedUserBtn.disabled = true;
            foundPatient = null;
        });
    }
});
</script>
@endpush
