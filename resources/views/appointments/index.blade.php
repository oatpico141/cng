@extends('layouts.app')

@section('title', 'ระบบนัดหมาย - GCMS')

@push('styles')
<link href='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.css' rel='stylesheet' />
<style>
    /* ==================== MODERN APPOINTMENTS PAGE 2024 ==================== */

    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 50%, #6366f1 100%);
        border-radius: 20px;
        padding: 2rem 2.5rem;
        color: white;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .page-header-content {
        position: relative;
        z-index: 1;
    }

    .page-header h2 {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .page-header h2 i {
        font-size: 1.5rem;
    }

    .page-header p {
        font-size: 0.95rem;
        opacity: 0.95;
        margin: 0;
    }

    .btn-create-apt {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.3);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
    }

    .btn-create-apt:hover {
        background: white;
        color: #0ea5e9;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    /* Stats Bar */
    .stats-bar {
        display: flex;
        align-items: center;
        justify-content: space-between;
        margin-bottom: 1rem;
        padding: 0.75rem 1rem;
        background: white;
        border-radius: 12px;
        box-shadow: 0 2px 12px rgba(0,0,0,0.04);
        border: 1px solid #f1f5f9;
    }

    .stats-date {
        font-size: 0.85rem;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .stats-date strong {
        color: #1e293b;
    }

    .btn-refresh {
        background: #f1f5f9;
        border: none;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.8rem;
        color: #64748b;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-refresh:hover {
        background: #e2e8f0;
        color: #475569;
    }

    /* Stat Cards Grid */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(6, 1fr);
        gap: 0.75rem;
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: white;
        border-radius: 14px;
        padding: 1.25rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        border: 1px solid #f1f5f9;
        border-left: 4px solid;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    }

    .stat-card.blue { border-left-color: #0ea5e9; }
    .stat-card.purple { border-left-color: #8b5cf6; }
    .stat-card.cyan { border-left-color: #06b6d4; }
    .stat-card.green { border-left-color: #10b981; }
    .stat-card.orange { border-left-color: #f59e0b; }
    .stat-card.red { border-left-color: #ef4444; }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .stat-card.blue .stat-value { color: #0369a1; }
    .stat-card.purple .stat-value { color: #7c3aed; }
    .stat-card.cyan .stat-value { color: #0891b2; }
    .stat-card.green .stat-value { color: #166534; }
    .stat-card.orange .stat-value { color: #92400e; }
    .stat-card.red .stat-value { color: #dc2626; }

    .stat-label {
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 500;
    }

    /* Section Card */
    .section-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }

    .section-header {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        font-size: 0.95rem;
        font-weight: 700;
        color: #1e293b;
        background: linear-gradient(135deg, #f8fafc, #fff);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .section-header i {
        color: #0ea5e9;
    }

    /* Calendar Styles */
    #calendar {
        padding: 1rem;
    }

    .fc {
        font-size: 0.85rem;
    }

    .fc-toolbar-title {
        font-size: 1.1rem !important;
        font-weight: 700;
        color: #1e293b;
    }

    .fc-button-primary {
        background: linear-gradient(135deg, #0ea5e9, #3b82f6) !important;
        border: none !important;
        border-radius: 10px !important;
        padding: 8px 16px !important;
        font-size: 0.8rem !important;
        font-weight: 600 !important;
        transition: all 0.3s ease !important;
    }

    .fc-button-primary:hover {
        transform: translateY(-2px) !important;
        box-shadow: 0 4px 15px rgba(14, 165, 233, 0.4) !important;
    }

    .fc-button-primary.fc-button-active {
        background: linear-gradient(135deg, #0369a1, #1d4ed8) !important;
    }

    .fc-event {
        border: none !important;
        border-radius: 8px !important;
        padding: 4px 8px !important;
        font-size: 0.75rem !important;
        font-weight: 500 !important;
        cursor: pointer !important;
        pointer-events: auto !important;
        transition: all 0.2s ease !important;
    }

    .fc-event:hover {
        opacity: 0.9;
        transform: scale(1.02);
        box-shadow: 0 4px 12px rgba(0,0,0,0.15);
    }

    .fc-day-today {
        background: linear-gradient(135deg, #f0f9ff, #e0f2fe) !important;
    }

    .fc-theme-standard td, .fc-theme-standard th {
        border-color: #f1f5f9;
    }

    .fc-col-header-cell {
        padding: 12px 0 !important;
        background: #f8fafc;
    }

    .fc-col-header-cell-cushion {
        font-weight: 600 !important;
        color: #64748b !important;
    }

    .fc-daygrid-day-number {
        font-weight: 600;
        color: #334155;
        padding: 8px !important;
    }

    /* Today's Appointments List */
    .appointment-item {
        padding: 1rem 1.25rem;
        border-bottom: 1px solid #f1f5f9;
        transition: all 0.2s ease;
    }

    .appointment-item:last-child {
        border-bottom: none;
    }

    .appointment-item:hover {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    }

    .appointment-time {
        font-size: 0.9rem;
        font-weight: 700;
        color: #0369a1;
        width: 55px;
        flex-shrink: 0;
    }

    .appointment-name {
        font-size: 0.9rem;
        font-weight: 600;
        color: #1e293b;
    }

    .appointment-service {
        font-size: 0.75rem;
        color: #64748b;
        margin-top: 2px;
    }

    /* Status Badge */
    .status-badge {
        font-size: 0.7rem;
        padding: 4px 10px;
        border-radius: 20px;
        font-weight: 600;
    }

    .status-confirmed { background: #dcfce7; color: #166534; }
    .status-pending { background: #fef3c7; color: #92400e; }
    .status-cancelled { background: #fee2e2; color: #991b1b; }
    .status-completed { background: #dbeafe; color: #1e40af; }

    /* Status Select */
    .status-select {
        font-size: 0.75rem;
        padding: 6px 10px;
        border-radius: 8px;
        border: 2px solid #e2e8f0;
        background: white;
        color: #475569;
        cursor: pointer;
        min-width: 80px;
        font-weight: 500;
        transition: all 0.2s ease;
    }

    .status-select:focus {
        outline: none;
        border-color: #0ea5e9;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
    }

    /* Time Group */
    .time-group {
        font-size: 0.75rem;
        color: #64748b;
        padding: 0.75rem 1.25rem;
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .time-group i {
        color: #0ea5e9;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 2rem;
        color: #94a3b8;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
        color: #cbd5e1;
    }

    .empty-state p {
        font-size: 0.9rem;
        font-weight: 500;
    }

    /* Modal Styling */
    .modal-header {
        background: linear-gradient(135deg, #f8fafc, #fff);
        border-bottom: 1px solid #f1f5f9;
        padding: 1.25rem 1.5rem;
    }

    .modal-header.gradient-header {
        background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 100%);
        color: white;
    }

    .modal-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .modal-header.gradient-header .modal-title {
        color: white;
    }

    .form-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-label i {
        color: #0ea5e9;
    }

    .form-control, .form-select {
        font-size: 0.9rem;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #0ea5e9;
        box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
    }

    /* Service Selection */
    .service-btn {
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        padding: 1rem 0.75rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.3s ease;
        background: white;
    }

    .service-btn:hover {
        border-color: #0ea5e9;
        background: #f0f9ff;
        transform: translateY(-2px);
    }

    .btn-check:checked + .service-btn {
        border-color: #0ea5e9;
        background: linear-gradient(135deg, #e0f2fe, #bae6fd);
        color: #0369a1;
        box-shadow: 0 4px 15px rgba(14, 165, 233, 0.2);
    }

    .service-btn i {
        font-size: 1.5rem;
        display: block;
        margin-bottom: 0.5rem;
        color: #0ea5e9;
    }

    .service-btn small {
        font-size: 0.8rem;
        font-weight: 600;
    }

    /* Nav Tabs */
    .nav-tabs {
        border-bottom: 2px solid #f1f5f9;
    }

    .nav-tabs .nav-link {
        border: none;
        color: #64748b;
        font-weight: 600;
        padding: 0.75rem 1.5rem;
        border-radius: 10px 10px 0 0;
        transition: all 0.2s ease;
    }

    .nav-tabs .nav-link:hover {
        color: #0ea5e9;
        background: #f0f9ff;
    }

    .nav-tabs .nav-link.active {
        color: #0ea5e9;
        background: white;
        border-bottom: 3px solid #0ea5e9;
    }

    /* FAB Button */
    .fab-btn {
        position: fixed;
        bottom: 90px;
        right: 24px;
        width: 60px;
        height: 60px;
        border-radius: 50%;
        background: linear-gradient(135deg, #0ea5e9, #3b82f6);
        border: none;
        color: white;
        font-size: 1.5rem;
        box-shadow: 0 8px 25px rgba(14, 165, 233, 0.4);
        z-index: 9999;
        display: none;
        transition: all 0.3s ease;
    }

    .fab-btn:hover {
        transform: scale(1.1) translateY(-2px);
        box-shadow: 0 12px 35px rgba(14, 165, 233, 0.5);
    }

    /* Dropdown Menu */
    .dropdown-menu {
        border: none;
        border-radius: 12px;
        box-shadow: 0 10px 40px rgba(0,0,0,0.12);
        padding: 0.5rem;
    }

    .dropdown-item {
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }

    .dropdown-item:hover {
        background: #f0f9ff;
    }

    .dropdown-item.text-danger:hover {
        background: #fee2e2;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .stat-grid {
            grid-template-columns: repeat(3, 1fr);
        }
    }

    @media (max-width: 768px) {
        .fab-btn {
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .page-header {
            padding: 1.5rem;
            border-radius: 16px;
        }

        .page-header h2 {
            font-size: 1.35rem;
        }

        .calendar-section {
            display: none;
        }

        .stat-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.5rem;
        }

        .stat-card {
            padding: 1rem;
        }

        .stat-value {
            font-size: 1.35rem;
        }
    }

    @media (max-width: 480px) {
        .stat-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
                <div>
                    <h2><i class="bi bi-calendar-check-fill"></i>ระบบนัดหมาย</h2>
                    <p>จัดการนัดหมายและตารางการรักษา</p>
                </div>
                <div class="d-none d-md-block">
                    <button type="button" class="btn-create-apt" data-bs-toggle="modal" data-bs-target="#createAppointmentModal">
                        <i class="bi bi-plus-circle-fill"></i>สร้างนัดหมาย
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- FAB for Mobile -->
    <button class="fab-btn d-md-none" data-bs-toggle="modal" data-bs-target="#createAppointmentModal">
        <i class="bi bi-plus"></i>
    </button>

    <!-- Stats Bar -->
    <div class="stats-bar">
        <div class="stats-date">
            <i class="bi bi-calendar3"></i>
            สถิติวันที่: <strong id="statsDateLabel">วันนี้</strong>
        </div>
        <button class="btn-refresh" onclick="loadStatsForToday()">
            <i class="bi bi-arrow-clockwise"></i>รีเฟรช
        </button>
    </div>

    <!-- Stats Grid -->
    <div class="stat-grid">
        <div class="stat-card blue">
            <div class="stat-value" id="statTotal">{{ $todayAppointments }}</div>
            <div class="stat-label">ทั้งหมด</div>
        </div>
        <div class="stat-card purple">
            <div class="stat-value" id="statNewPatients">0</div>
            <div class="stat-label">ลูกค้าใหม่</div>
        </div>
        <div class="stat-card cyan">
            <div class="stat-value" id="statCoursePatients">0</div>
            <div class="stat-label">ลูกค้าคอร์ส</div>
        </div>
        <div class="stat-card green">
            <div class="stat-value" id="statCompleted">0</div>
            <div class="stat-label">มาตามนัด</div>
        </div>
        <div class="stat-card orange">
            <div class="stat-value" id="statRescheduled">0</div>
            <div class="stat-label">เลื่อนนัด</div>
        </div>
        <div class="stat-card red">
            <div class="stat-value" id="statCancelled">0</div>
            <div class="stat-label">ยกเลิก/ไม่มา</div>
        </div>
    </div>

    <!-- Calendar & List -->
    <div class="row g-2">
        <!-- Calendar -->
        <div class="col-lg-8 calendar-section">
            <div class="section-card">
                <div class="section-header">
                    <i class="bi bi-calendar3 me-2"></i>ปฏิทินนัดหมาย
                </div>
                <div id="calendar"></div>
            </div>
        </div>

        <!-- Today's List -->
        <div class="col-lg-4">
            <div class="section-card">
                <div class="section-header d-flex justify-content-between">
                    <span><i class="bi bi-clock me-2"></i>วันนี้</span>
                    <small style="color: #64748b;">{{ now()->locale('th')->isoFormat('D MMM Y') }}</small>
                </div>

                @if($todayAppointmentsList->count() > 0)
                    @php
                        $morning = $todayAppointmentsList->filter(fn($a) => substr($a->appointment_time, 0, 2) < 12);
                        $afternoon = $todayAppointmentsList->filter(fn($a) => substr($a->appointment_time, 0, 2) >= 12);
                    @endphp

                    @if($morning->count() > 0)
                        <div class="time-group"><i class="bi bi-sunrise me-1"></i>ช่วงเช้า</div>
                        @foreach($morning as $appointment)
                            <div class="appointment-item">
                                <div class="d-flex align-items-start">
                                    <div class="appointment-time">{{ substr($appointment->appointment_time, 0, 5) }}</div>
                                    <div class="flex-grow-1">
                                        <div class="appointment-name">{{ $appointment->patient->name ?? 'ไม่ระบุ' }}</div>
                                        <div class="appointment-service">
                                            {{ $appointment->purpose == 'PHYSICAL_THERAPY' ? 'กายภาพบำบัด' : 'ติดตามผล' }}
                                            @if($appointment->pt)
                                                - {{ $appointment->pt->name }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="dropdown me-2">
                                        <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" style="padding: 2px 6px;">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="#" onclick="viewAppointmentById('{{ $appointment->id }}')"><i class="bi bi-eye me-2"></i>ดูรายละเอียด</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="editAppointmentById('{{ $appointment->id }}')"><i class="bi bi-pencil me-2"></i>แก้ไข</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteAppointmentById('{{ $appointment->id }}')"><i class="bi bi-trash me-2"></i>ลบ</a></li>
                                        </ul>
                                    </div>
                                    <select class="status-select" data-id="{{ $appointment->id }}" onchange="updateStatus(this)">
                                        <option value="pending" {{ $appointment->status == 'pending' ? 'selected' : '' }}>รอคิว</option>
                                        <option value="confirmed" {{ $appointment->status == 'confirmed' ? 'selected' : '' }}>ยืนยัน</option>
                                        <option value="rescheduled" {{ $appointment->status == 'rescheduled' ? 'selected' : '' }}>เลื่อน</option>
                                        <option value="cancelled" {{ $appointment->status == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                                        <option value="no_show" {{ $appointment->status == 'no_show' ? 'selected' : '' }}>ไม่มา</option>
                                        <option value="completed" {{ $appointment->status == 'completed' ? 'selected' : '' }}>เสร็จ</option>
                                    </select>
                                </div>
                            </div>
                        @endforeach
                    @endif

                    @if($afternoon->count() > 0)
                        <div class="time-group"><i class="bi bi-sun me-1"></i>ช่วงบ่าย</div>
                        @foreach($afternoon as $appointment)
                            <div class="appointment-item">
                                <div class="d-flex align-items-start">
                                    <div class="appointment-time">{{ substr($appointment->appointment_time, 0, 5) }}</div>
                                    <div class="flex-grow-1">
                                        <div class="appointment-name">{{ $appointment->patient->name ?? 'ไม่ระบุ' }}</div>
                                        <div class="appointment-service">
                                            {{ $appointment->purpose == 'PHYSICAL_THERAPY' ? 'กายภาพบำบัด' : 'ติดตามผล' }}
                                            @if($appointment->pt)
                                                - {{ $appointment->pt->name }}
                                            @endif
                                        </div>
                                    </div>
                                    <div class="dropdown me-2">
                                        <button class="btn btn-sm btn-light" type="button" data-bs-toggle="dropdown" style="padding: 2px 6px;">
                                            <i class="bi bi-three-dots-vertical"></i>
                                        </button>
                                        <ul class="dropdown-menu dropdown-menu-end">
                                            <li><a class="dropdown-item" href="#" onclick="viewAppointmentById('{{ $appointment->id }}')"><i class="bi bi-eye me-2"></i>ดูรายละเอียด</a></li>
                                            <li><a class="dropdown-item" href="#" onclick="editAppointmentById('{{ $appointment->id }}')"><i class="bi bi-pencil me-2"></i>แก้ไข</a></li>
                                            <li><hr class="dropdown-divider"></li>
                                            <li><a class="dropdown-item text-danger" href="#" onclick="deleteAppointmentById('{{ $appointment->id }}')"><i class="bi bi-trash me-2"></i>ลบ</a></li>
                                        </ul>
                                    </div>
                                    <select class="status-select" data-id="{{ $appointment->id }}" onchange="updateStatus(this)">
                                        <option value="pending" {{ $appointment->status == 'pending' ? 'selected' : '' }}>รอคิว</option>
                                        <option value="confirmed" {{ $appointment->status == 'confirmed' ? 'selected' : '' }}>ยืนยัน</option>
                                        <option value="rescheduled" {{ $appointment->status == 'rescheduled' ? 'selected' : '' }}>เลื่อน</option>
                                        <option value="cancelled" {{ $appointment->status == 'cancelled' ? 'selected' : '' }}>ยกเลิก</option>
                                        <option value="no_show" {{ $appointment->status == 'no_show' ? 'selected' : '' }}>ไม่มา</option>
                                        <option value="completed" {{ $appointment->status == 'completed' ? 'selected' : '' }}>เสร็จ</option>
                                    </select>
                                </div>
                            </div>
                        @endforeach
                    @endif
                @else
                    <div class="empty-state">
                        <i class="bi bi-calendar-x d-block"></i>
                        <div>ไม่มีนัดหมายวันนี้</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Create Appointment Modal -->
<div class="modal fade" id="createAppointmentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="appointmentForm" method="POST" action="{{ route('appointments.store') }}" autocomplete="off">
                @csrf
                <input type="hidden" id="customer_type" name="customer_type" value="existing">
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-calendar-plus me-2"></i>สร้างนัดหมายใหม่</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <!-- Customer Type Tabs -->
                    <ul class="nav nav-tabs mb-3" id="customerTypeTabs" role="tablist">
                        <li class="nav-item" role="presentation">
                            <button class="nav-link active" id="existing-tab" data-bs-toggle="tab" data-bs-target="#existing-customer" type="button" role="tab">
                                <i class="bi bi-person-check me-1"></i>ลูกค้าเก่า
                            </button>
                        </li>
                        <li class="nav-item" role="presentation">
                            <button class="nav-link" id="new-tab" data-bs-toggle="tab" data-bs-target="#new-customer" type="button" role="tab">
                                <i class="bi bi-person-plus me-1"></i>ลูกค้าใหม่
                            </button>
                        </li>
                    </ul>

                    <div class="tab-content" id="customerTypeTabContent">
                        <!-- Existing Customer Tab -->
                        <div class="tab-pane fade show active" id="existing-customer" role="tabpanel">
                            <div class="mb-3">
                                <label class="form-label"><i class="bi bi-search me-1"></i>ค้นหาคนไข้</label>
                                <div class="position-relative">
                                    <input type="text" class="form-control" id="patient_search" placeholder="พิมพ์ชื่อ, เบอร์โทร หรือ HN..." autocomplete="off">
                                    <div id="patient_search_results" class="position-absolute w-100 bg-white border rounded-bottom shadow-sm" style="display:none; z-index:1000; max-height:250px; overflow-y:auto;"></div>
                                </div>
                                <div id="patient_info" class="mt-2"></div>
                                <input type="hidden" id="patient_id" name="patient_id">
                            </div>
                        </div>

                        <!-- New Customer Tab -->
                        <div class="tab-pane fade" id="new-customer" role="tabpanel">
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label"><i class="bi bi-person me-1"></i>ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                                    <input type="text" class="form-control" id="new_patient_name" name="new_patient_name" placeholder="กรอกชื่อ-นามสกุล">
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="bi bi-telephone me-1"></i>เบอร์โทรศัพท์ <span class="text-danger">*</span></label>
                                    <input type="tel" class="form-control" id="new_patient_phone" name="new_patient_phone" placeholder="0812345678" maxlength="10">
                                    <small class="text-muted" id="phoneHelperModal">กรุณาใส่เบอร์โทร 10 หลัก</small>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="bi bi-megaphone me-1"></i>ช่องทางที่ติดต่อมา</label>
                                    <select class="form-select" id="new_lead_source" name="new_lead_source">
                                        <option value="walk_in">Walk-in</option>
                                        <option value="phone">โทรศัพท์</option>
                                        <option value="line">LINE</option>
                                        <option value="facebook">Facebook</option>
                                        <option value="ads">Ads (โฆษณา)</option>
                                        <option value="referral">แนะนำจากคนรู้จัก</option>
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label"><i class="bi bi-bandaid me-1"></i>อาการ</label>
                                    <select class="form-select" id="new_symptoms" name="new_symptoms">
                                        <option value="">ไม่ระบุ</option>
                                        <option value="ปวดคอ บ่า ไหล่">ปวดคอ บ่า ไหล่</option>
                                        <option value="ปวดหลัง">ปวดหลัง</option>
                                        <option value="ปวดเข่า">ปวดเข่า</option>
                                        <option value="สลักเพรชจม">สลักเพรชจม</option>
                                        <option value="ปวดขา">ปวดขา</option>
                                        <option value="ปวดแขน">ปวดแขน</option>
                                        <option value="รองช้ำ">รองช้ำ</option>
                                        <option value="อื่นๆ">อื่นๆ</option>
                                    </select>
                                </div>
                                <div class="col-12" id="customSymptomsModalGroup" style="display:none;">
                                    <label class="form-label">ระบุอาการเพิ่มเติม</label>
                                    <input type="text" class="form-control" id="new_custom_symptoms" name="new_custom_symptoms" placeholder="อธิบายอาการ...">
                                </div>
                            </div>
                        </div>
                    </div>

                    <hr class="my-3">

                    <!-- Service -->
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-heart-pulse me-1"></i>บริการ</label>
                        <div class="row g-2">
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="purpose" id="purpose_pt" value="PHYSICAL_THERAPY" required>
                                <label class="service-btn w-100" for="purpose_pt">
                                    <i class="bi bi-activity"></i>
                                    <small>กายภาพบำบัด</small>
                                </label>
                            </div>
                            <div class="col-6">
                                <input type="radio" class="btn-check" name="purpose" id="purpose_follow" value="FOLLOW_UP">
                                <label class="service-btn w-100" for="purpose_follow">
                                    <i class="bi bi-clipboard-pulse"></i>
                                    <small>ติดตามผล</small>
                                </label>
                            </div>
                        </div>
                    </div>

                    <!-- Date & Time -->
                    <div class="row g-2 mb-3">
                        <div class="col-6">
                            <label class="form-label"><i class="bi bi-calendar me-1"></i>วันที่</label>
                            <input type="date" class="form-control" id="appointment_date" name="appointment_date" required value="{{ date('Y-m-d') }}">
                        </div>
                        <div class="col-6">
                            <label class="form-label"><i class="bi bi-clock me-1"></i>เวลา</label>
                            <select class="form-select" id="appointment_time" name="appointment_time" required>
                                <option value="">เลือกเวลา</option>
                                <optgroup label="เช้า (09:00-12:00)">
                                    <option value="09:00">09:00</option>
                                    <option value="09:30">09:30</option>
                                    <option value="10:00">10:00</option>
                                    <option value="10:30">10:30</option>
                                    <option value="11:00">11:00</option>
                                    <option value="11:30">11:30</option>
                                </optgroup>
                                <optgroup label="บ่าย (12:00-19:00)">
                                    <option value="12:00">12:00</option>
                                    <option value="12:30">12:30</option>
                                    <option value="13:00">13:00</option>
                                    <option value="13:30">13:30</option>
                                    <option value="14:00">14:00</option>
                                    <option value="14:30">14:30</option>
                                    <option value="15:00">15:00</option>
                                    <option value="15:30">15:30</option>
                                    <option value="16:00">16:00</option>
                                    <option value="16:30">16:30</option>
                                    <option value="17:00">17:00</option>
                                    <option value="17:30">17:30</option>
                                    <option value="18:00">18:00</option>
                                    <option value="18:30">18:30</option>
                                    <option value="19:00">19:00</option>
                                </optgroup>
                            </select>
                        </div>
                    </div>

                    <!-- Branch -->
                    @php
                        $selectedBranch = null;
                        if (session('selected_branch_id')) {
                            $selectedBranch = \App\Models\Branch::find(session('selected_branch_id'));
                        }
                        if (!$selectedBranch) {
                            $selectedBranch = auth()->user()->branch;
                        }
                    @endphp
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-building me-1"></i>สาขา</label>
                        <input type="text" class="form-control" value="{{ $selectedBranch->name ?? 'ไม่ระบุสาขา' }}" disabled>
                        <input type="hidden" id="branch_id" name="branch_id" value="{{ $selectedBranch->id ?? '' }}" required>
                    </div>

                    <!-- PT -->
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-person-badge me-1"></i>นักกายภาพ</label>
                        <select class="form-select" id="pt_id" name="pt_id">
                            <option value="">จัดคิวอัตโนมัติ</option>
                            @foreach($staff as $pt)
                                <option value="{{ $pt->id }}">{{ $pt->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <!-- Notes -->
                    <div class="mb-3">
                        <label class="form-label"><i class="bi bi-chat-text me-1"></i>หมายเหตุ</label>
                        <textarea class="form-control" name="notes" rows="2" placeholder="อาการ หรือข้อมูลเพิ่มเติม..."></textarea>
                    </div>
                </div>
                <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0;">
                    <button type="button" class="btn btn-light btn-sm" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" id="submitAppointmentBtn" class="btn btn-primary btn-sm" style="background: #0ea5e9; border-color: #0ea5e9;">
                        <i class="bi bi-check2 me-1"></i>บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Daily Summary Modal -->
<div class="modal fade" id="dateSummaryModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #0284c7, #0ea5e9); color: white;">
                <h5 class="modal-title"><i class="bi bi-calendar-check me-2"></i>สรุปยอดประจำวัน - <span id="summaryDate"></span></h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Summary Cards Row 1 -->
                <div class="row g-2 mb-2">
                    <div class="col-4">
                        <div class="stat-card blue text-center">
                            <div class="stat-value" id="summaryTotal">0</div>
                            <div class="stat-label">ทั้งหมด</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stat-card text-center" style="border-left-color: #8b5cf6;">
                            <div class="stat-value" id="summaryNewPatients" style="color: #7c3aed;">0</div>
                            <div class="stat-label">ลูกค้าใหม่</div>
                        </div>
                    </div>
                    <div class="col-4">
                        <div class="stat-card text-center" style="border-left-color: #06b6d4;">
                            <div class="stat-value" id="summaryCoursePatients" style="color: #0891b2;">0</div>
                            <div class="stat-label">ลูกค้าคอร์ส</div>
                        </div>
                    </div>
                </div>
                <!-- Summary Cards Row 2 -->
                <div class="row g-2 mb-3">
                    <div class="col-3">
                        <div class="stat-card green text-center">
                            <div class="stat-value" id="summaryCompleted">0</div>
                            <div class="stat-label">มาตามนัด</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="stat-card orange text-center">
                            <div class="stat-value" id="summaryRescheduled">0</div>
                            <div class="stat-label">เลื่อนนัด</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="stat-card red text-center">
                            <div class="stat-value" id="summaryCancelled">0</div>
                            <div class="stat-label">ยกเลิก</div>
                        </div>
                    </div>
                    <div class="col-3">
                        <div class="stat-card text-center" style="border-left-color: #6b7280;">
                            <div class="stat-value" id="summaryNoShow" style="color: #4b5563;">0</div>
                            <div class="stat-label">ไม่มา</div>
                        </div>
                    </div>
                </div>

                <!-- Appointment Detail Table -->
                <div class="section-card">
                    <div class="section-header d-flex justify-content-between align-items-center">
                        <span><i class="bi bi-list-ul me-1"></i>รายละเอียดนัดหมาย</span>
                        <span class="badge bg-secondary" id="appointmentCount">0 รายการ</span>
                    </div>
                    <div style="max-height: 300px; overflow-y: auto;">
                        <table class="table table-sm table-hover mb-0">
                            <thead class="table-light" style="position: sticky; top: 0;">
                                <tr>
                                    <th style="width: 60px;">เวลา</th>
                                    <th>ชื่อคนไข้</th>
                                    <th style="width: 80px;">ประเภท</th>
                                    <th style="width: 80px;">สถานะ</th>
                                </tr>
                            </thead>
                            <tbody id="appointmentTableBody">
                                <!-- Data will be loaded here -->
                            </tbody>
                        </table>
                    </div>
                    <div id="noAppointmentMessage" class="text-center py-4 text-muted" style="display: none;">
                        <i class="bi bi-calendar-x d-block fs-3 mb-2"></i>
                        <div>ไม่มีนัดหมายในวันนี้</div>
                    </div>
                </div>
            </div>
            <div class="modal-footer" style="background: #f8fafc; border-top: 1px solid #e2e8f0;">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ปิด</button>
                <button type="button" class="btn btn-primary btn-sm" id="addAppointmentFromSummary" style="background: #0ea5e9; border-color: #0ea5e9;">
                    <i class="bi bi-plus me-1"></i>เพิ่มนัดหมายในวันนี้
                </button>
            </div>
        </div>
    </div>
</div>

<!-- View/Edit Appointment Modal -->
<div class="modal fade" id="viewAppointmentModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #0284c7, #0ea5e9); color: white;">
                <h5 class="modal-title"><i class="bi bi-calendar-event me-2"></i>รายละเอียดนัดหมาย</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="view_appointment_id">

                <!-- View Mode -->
                <div id="viewModeContent">
                    <div class="mb-3">
                        <label class="form-label text-muted small">ชื่อคนไข้</label>
                        <div class="fw-bold" id="view_patient_name">-</div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label text-muted small">เบอร์โทรศัพท์</label>
                            <div id="view_phone">-</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted small">ประเภทบริการ</label>
                            <div id="view_service">-</div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label text-muted small">วันที่</label>
                            <div id="view_date">-</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted small">เวลา</label>
                            <div id="view_time">-</div>
                        </div>
                    </div>
                    <div class="row mb-3">
                        <div class="col-6">
                            <label class="form-label text-muted small">นักกายภาพ</label>
                            <div id="view_pt_name">-</div>
                        </div>
                        <div class="col-6">
                            <label class="form-label text-muted small">สถานะ</label>
                            <div id="view_status_badge">-</div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label text-muted small">หมายเหตุ</label>
                        <div id="view_notes" class="text-muted">-</div>
                    </div>
                </div>

                <!-- Edit Mode -->
                <div id="editModeContent" style="display: none;">
                    <form id="editAppointmentForm">
                        <div class="mb-3">
                            <label class="form-label">วันที่นัดหมาย</label>
                            <input type="date" class="form-control" id="edit_date" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">เวลา</label>
                            <select class="form-select" id="edit_time" required>
                                <option value="">เลือกเวลา</option>
                                <optgroup label="เช้า (09:00-12:00)">
                                    <option value="09:00">09:00</option>
                                    <option value="09:30">09:30</option>
                                    <option value="10:00">10:00</option>
                                    <option value="10:30">10:30</option>
                                    <option value="11:00">11:00</option>
                                    <option value="11:30">11:30</option>
                                </optgroup>
                                <optgroup label="บ่าย (12:00-19:00)">
                                    <option value="12:00">12:00</option>
                                    <option value="12:30">12:30</option>
                                    <option value="13:00">13:00</option>
                                    <option value="13:30">13:30</option>
                                    <option value="14:00">14:00</option>
                                    <option value="14:30">14:30</option>
                                    <option value="15:00">15:00</option>
                                    <option value="15:30">15:30</option>
                                    <option value="16:00">16:00</option>
                                    <option value="16:30">16:30</option>
                                    <option value="17:00">17:00</option>
                                    <option value="17:30">17:30</option>
                                    <option value="18:00">18:00</option>
                                    <option value="18:30">18:30</option>
                                    <option value="19:00">19:00</option>
                                </optgroup>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">นักกายภาพ</label>
                            <select class="form-select" id="edit_pt_id">
                                <option value="">-- ไม่ระบุ --</option>
                                @foreach($staff ?? [] as $s)
                                    <option value="{{ $s->id }}">{{ $s->name ?? $s->first_name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">ประเภทบริการ</label>
                            <select class="form-select" id="edit_purpose" required>
                                <option value="PHYSICAL_THERAPY">กายภาพบำบัด</option>
                                <option value="FOLLOW_UP">ติดตามผล</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">สถานะ</label>
                            <select class="form-select" id="edit_status" required>
                                <option value="pending">รอยืนยัน</option>
                                <option value="confirmed">ยืนยันแล้ว</option>
                                <option value="completed">เสร็จสิ้น</option>
                                <option value="cancelled">ยกเลิก</option>
                                <option value="no_show">ไม่มา</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label">หมายเหตุ</label>
                            <textarea class="form-control" id="edit_notes" rows="2"></textarea>
                        </div>
                    </form>
                </div>
            </div>
            <div class="modal-footer">
                <!-- View Mode Buttons -->
                <div id="viewModeButtons">
                    <button type="button" class="btn btn-danger btn-sm" onclick="deleteAppointment()">
                        <i class="bi bi-trash me-1"></i>ลบ
                    </button>
                    <button type="button" class="btn btn-warning btn-sm" onclick="toggleEditMode(true)">
                        <i class="bi bi-pencil me-1"></i>แก้ไข
                    </button>
                    <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ปิด</button>
                </div>
                <!-- Edit Mode Buttons -->
                <div id="editModeButtons" style="display: none;">
                    <button type="button" class="btn btn-secondary btn-sm" onclick="toggleEditMode(false)">ยกเลิก</button>
                    <button type="button" class="btn btn-primary btn-sm" onclick="saveAppointmentEdit()">
                        <i class="bi bi-check me-1"></i>บันทึก
                    </button>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Alert Modal -->
<div class="modal fade" id="alertModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <div id="alertIcon" class="mb-3"></div>
                <h5 id="alertTitle" class="mb-2"></h5>
                <p id="alertMessage" class="text-muted mb-0"></p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0">
                <button type="button" class="btn btn-primary btn-sm px-4" data-bs-dismiss="modal" id="alertOkBtn">ตกลง</button>
            </div>
        </div>
    </div>
</div>

<!-- Confirm Modal -->
<div class="modal fade" id="confirmModal" tabindex="-1">
    <div class="modal-dialog modal-sm modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-body text-center py-4">
                <i class="bi bi-question-circle-fill text-warning" style="font-size: 3rem;"></i>
                <h5 class="mt-3 mb-2">ยืนยันการดำเนินการ</h5>
                <p id="confirmMessage" class="text-muted mb-0"></p>
            </div>
            <div class="modal-footer justify-content-center border-0 pt-0">
                <button type="button" class="btn btn-secondary btn-sm px-3" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-danger btn-sm px-3" id="confirmOkBtn">ยืนยัน</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script src='https://cdn.jsdelivr.net/npm/fullcalendar@6.1.10/index.global.min.js'></script>
<script>
// Base URL for API calls
const BASE_URL = '{{ url('/') }}';

// Alert Modal function
var alertCallback = null;
function showAlert(type, title, message, callback = null) {
    var iconMap = {
        'success': '<i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>',
        'error': '<i class="bi bi-x-circle-fill text-danger" style="font-size: 3rem;"></i>',
        'warning': '<i class="bi bi-exclamation-triangle-fill text-warning" style="font-size: 3rem;"></i>',
        'info': '<i class="bi bi-info-circle-fill text-primary" style="font-size: 3rem;"></i>'
    };

    document.getElementById('alertIcon').innerHTML = iconMap[type] || iconMap['info'];
    document.getElementById('alertTitle').textContent = title;
    document.getElementById('alertMessage').textContent = message;

    alertCallback = callback;
    var modal = new bootstrap.Modal(document.getElementById('alertModal'));
    modal.show();
}

document.getElementById('alertModal').addEventListener('hidden.bs.modal', function() {
    if (alertCallback) {
        alertCallback();
        alertCallback = null;
    }
});

// Confirm Modal function
var confirmCallback = null;
function showConfirm(message, callback) {
    document.getElementById('confirmMessage').textContent = message;
    confirmCallback = callback;
    var modal = new bootstrap.Modal(document.getElementById('confirmModal'));
    modal.show();
}

document.getElementById('confirmOkBtn').addEventListener('click', function() {
    var modal = bootstrap.Modal.getInstance(document.getElementById('confirmModal'));
    modal.hide();
    if (confirmCallback) {
        confirmCallback();
        confirmCallback = null;
    }
});

document.addEventListener('DOMContentLoaded', function() {
    var calendarEl = document.getElementById('calendar');

    // Calendar events from database
    var events = [
        @foreach($allAppointments as $apt)
        {
            id: '{{ $apt->id }}',
            title: '{{ $apt->patient->name ?? "ไม่ระบุ" }}',
            start: '{{ $apt->appointment_date }}T{{ $apt->appointment_time }}',
            color: @switch($apt->status)
                @case('confirmed') '#10b981' @break
                @case('pending') '#f59e0b' @break
                @case('cancelled') '#ef4444' @break
                @case('completed') '#3b82f6' @break
                @default '#6b7280'
            @endswitch,
            extendedProps: {
                patient_name: '{{ $apt->patient->name ?? "ไม่ระบุ" }}',
                phone: '{{ $apt->patient->phone ?? "N/A" }}',
                service: '{{ $apt->purpose == "PHYSICAL_THERAPY" ? "กายภาพบำบัด" : "ติดตามผล" }}',
                pt_name: '{{ $apt->pt ? $apt->pt->name : "ไม่ระบุ" }}',
                pt_id: '{{ $apt->pt_id ?? "" }}',
                status: '{{ $apt->status }}',
                notes: @json($apt->notes ?? ''),
                purpose: '{{ $apt->purpose ?? "PHYSICAL_THERAPY" }}'
            }
        },
        @endforeach
    ];

    var calendar = new FullCalendar.Calendar(calendarEl, {
        initialView: 'dayGridMonth',
        locale: 'th',
        height: 500,
        headerToolbar: {
            left: 'prev,next today',
            center: 'title',
            right: 'dayGridMonth,timeGridWeek'
        },
        buttonText: {
            today: 'วันนี้',
            month: 'เดือน',
            week: 'สัปดาห์'
        },
        events: events,
        dateClick: function(info) {
            var selectedDate = new Date(info.dateStr);
            var today = new Date();
            today.setHours(0, 0, 0, 0);

            // Always update stat cards when any date is clicked
            loadStatsForDate(info.dateStr);

            // For past dates, show summary modal
            if (selectedDate < today) {
                loadDailySummary(info.dateStr);
            } else {
                // For today and future dates, show create appointment modal
                document.getElementById('appointment_date').value = info.dateStr;
                var submitBtn = document.getElementById('submitAppointmentBtn');
                submitBtn.style.display = 'inline-block';
                var modal = new bootstrap.Modal(document.getElementById('createAppointmentModal'));
                modal.show();
            }
        },
        eventClick: function(info) {
            showAppointmentDetail(info.event);
        }
    });

    calendar.render();

    // Patient search with dropdown
    let searchTimeout;
    const searchInput = document.getElementById('patient_search');
    const searchResults = document.getElementById('patient_search_results');

    searchInput.addEventListener('input', function() {
        clearTimeout(searchTimeout);
        const query = this.value.trim();

        if (query.length >= 2) {
            searchTimeout = setTimeout(() => {
                fetch(`${BASE_URL}/api/patients/search?query=` + encodeURIComponent(query))
                    .then(res => res.json())
                    .then(data => {
                        if (data.length > 0) {
                            let html = '';
                            data.forEach(patient => {
                                const hn = patient.hn ? '<span class="text-primary">[' + patient.hn + ']</span> ' : '';
                                const phone = patient.phone || 'ไม่มีเบอร์';
                                html += '<div class="patient-search-item px-3 py-2 border-bottom" data-id="' + patient.id + '" data-name="' + patient.name + '" data-phone="' + phone + '" data-hn="' + (patient.hn || '') + '" style="cursor:pointer;">' +
                                    '<div class="fw-medium">' + hn + patient.name + '</div>' +
                                    '<small class="text-muted"><i class="bi bi-telephone me-1"></i>' + phone + '</small>' +
                                    '</div>';
                            });
                            searchResults.innerHTML = html;
                            searchResults.style.display = 'block';

                            // Add click events to results
                            document.querySelectorAll('.patient-search-item').forEach(item => {
                                item.addEventListener('click', function() {
                                    selectPatient(this.dataset.id, this.dataset.name, this.dataset.phone, this.dataset.hn);
                                });
                                item.addEventListener('mouseenter', function() {
                                    this.style.background = '#f0f9ff';
                                });
                                item.addEventListener('mouseleave', function() {
                                    this.style.background = '';
                                });
                            });
                        } else {
                            searchResults.innerHTML = '<div class="px-3 py-2 text-muted text-center"><i class="bi bi-exclamation-circle me-1"></i>ไม่พบข้อมูลคนไข้</div>';
                            searchResults.style.display = 'block';
                            document.getElementById('patient_id').value = '';
                            document.getElementById('patient_info').innerHTML = '';
                        }
                    })
                    .catch(() => {
                        searchResults.style.display = 'none';
                        document.getElementById('patient_info').innerHTML = '';
                    });
            }, 300);
        } else {
            searchResults.style.display = 'none';
            document.getElementById('patient_info').innerHTML = '';
        }
    });

    // Select patient from dropdown
    function selectPatient(id, name, phone, hn) {
        document.getElementById('patient_id').value = id;
        document.getElementById('patient_search').value = name;
        searchResults.style.display = 'none';

        const hnDisplay = hn ? '<span class="badge bg-primary me-1">' + hn + '</span>' : '';
        document.getElementById('patient_info').innerHTML =
            '<div class="alert alert-success py-2 mb-0">' +
            '<i class="bi bi-check-circle-fill me-2"></i>' +
            hnDisplay + '<strong>' + name + '</strong>' +
            '<br><small class="text-muted"><i class="bi bi-telephone me-1"></i>' + phone + '</small>' +
            '</div>';
    }

    // Hide dropdown when clicking outside
    document.addEventListener('click', function(e) {
        if (!searchInput.contains(e.target) && !searchResults.contains(e.target)) {
            searchResults.style.display = 'none';
        }
    });

    // Show dropdown again when focusing on search input
    searchInput.addEventListener('focus', function() {
        if (this.value.length >= 2 && searchResults.innerHTML !== '') {
            searchResults.style.display = 'block';
        }
    });
});

// Update appointment status
function updateStatus(select) {
    const id = select.dataset.id;
    const status = select.value;

    fetch(`${BASE_URL}/appointments/${id}/status`, {
        method: 'PATCH',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ status: status })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            // Change select background based on status
            select.style.background = getStatusColor(status);
        } else {
            showAlert('error', 'ผิดพลาด', 'ไม่สามารถอัพเดทสถานะได้');
        }
    })
    .catch(() => {
        showAlert('error', 'ผิดพลาด', 'เกิดข้อผิดพลาดในการเชื่อมต่อ');
    });
}

function getStatusColor(status) {
    switch(status) {
        case 'pending': return '#fef3c7';
        case 'confirmed': return '#dcfce7';
        case 'rescheduled': return '#dbeafe';
        case 'cancelled': return '#fee2e2';
        case 'no_show': return '#fee2e2';
        case 'completed': return '#d1fae5';
        default: return '#fff';
    }
}

// Store selected date for "Add Appointment" button
var selectedSummaryDate = null;

// Server's current date (Asia/Bangkok timezone)
var serverToday = '{{ date('Y-m-d') }}';

// Load stats for today on page load
loadStatsForDate(serverToday);

// Load stats for today (refresh button)
function loadStatsForToday() {
    loadStatsForDate(serverToday);
}

// Load stats for a specific date and update cards
function loadStatsForDate(dateStr) {
    fetch(`${BASE_URL}/api/appointments/summary?date=${dateStr}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update stat cards
                document.getElementById('statTotal').textContent = data.counts.total;
                document.getElementById('statNewPatients').textContent = data.counts.new_patients;
                document.getElementById('statCoursePatients').textContent = data.counts.course_patients;
                document.getElementById('statCompleted').textContent = data.counts.completed;
                document.getElementById('statRescheduled').textContent = data.counts.rescheduled;
                document.getElementById('statCancelled').textContent = data.counts.cancelled + data.counts.no_show;

                // Update date label
                var date = new Date(dateStr);
                var today = new Date();
                today.setHours(0, 0, 0, 0);
                var selectedDate = new Date(dateStr);
                selectedDate.setHours(0, 0, 0, 0);

                var thaiYear = date.getFullYear() + 543;
                var months = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];

                if (selectedDate.getTime() === today.getTime()) {
                    document.getElementById('statsDateLabel').textContent = 'วันนี้ (' + date.getDate() + ' ' + months[date.getMonth()] + ' ' + thaiYear + ')';
                } else {
                    document.getElementById('statsDateLabel').textContent = date.getDate() + ' ' + months[date.getMonth()] + ' ' + thaiYear;
                }
            }
        })
        .catch(error => {
            console.error('Error loading stats:', error);
        });
}

// Load daily summary from API
function loadDailySummary(dateStr) {
    selectedSummaryDate = dateStr;

    // Format date for display (Thai Buddhist Era)
    var date = new Date(dateStr);
    var thaiYear = date.getFullYear() + 543;
    var months = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
    var formattedDate = date.getDate() + ' ' + months[date.getMonth()] + ' ' + thaiYear;
    document.getElementById('summaryDate').textContent = formattedDate;

    // Fetch data from API
    fetch(`${BASE_URL}/api/appointments/summary?date=${dateStr}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Update summary cards
                document.getElementById('summaryTotal').textContent = data.counts.total;
                document.getElementById('summaryNewPatients').textContent = data.counts.new_patients;
                document.getElementById('summaryCoursePatients').textContent = data.counts.course_patients;
                document.getElementById('summaryCompleted').textContent = data.counts.completed;
                document.getElementById('summaryRescheduled').textContent = data.counts.rescheduled;
                document.getElementById('summaryCancelled').textContent = data.counts.cancelled;
                document.getElementById('summaryNoShow').textContent = data.counts.no_show;
                document.getElementById('appointmentCount').textContent = data.counts.total + ' รายการ';

                // Update appointment table
                var tableBody = document.getElementById('appointmentTableBody');
                var noMessage = document.getElementById('noAppointmentMessage');

                if (data.appointments.length > 0) {
                    tableBody.innerHTML = '';
                    noMessage.style.display = 'none';
                    tableBody.parentElement.parentElement.style.display = 'block';

                    data.appointments.forEach(function(apt) {
                        var patientLink = apt.patient_id
                            ? '<a href="/patients/' + apt.patient_id + '" class="text-decoration-none">' + apt.patient_name + '</a>'
                            : apt.patient_name;

                        var patientType = apt.is_new_patient
                            ? '<span class="badge bg-success">ใหม่</span>'
                            : '<span class="badge bg-secondary">เก่า</span>';

                        var statusBadge = '<span class="badge bg-' + apt.status_color + '">' + apt.status_text + '</span>';

                        tableBody.innerHTML += '<tr>' +
                            '<td><strong>' + apt.time + '</strong></td>' +
                            '<td>' + patientLink + '<br><small class="text-muted">' + apt.patient_phone + '</small></td>' +
                            '<td>' + patientType + '</td>' +
                            '<td>' + statusBadge + '</td>' +
                            '</tr>';
                    });
                } else {
                    tableBody.innerHTML = '';
                    tableBody.parentElement.parentElement.style.display = 'none';
                    noMessage.style.display = 'block';
                }

                // Hide "Add Appointment" button for past dates
                var addBtn = document.getElementById('addAppointmentFromSummary');
                var selectedDate = new Date(dateStr);
                var today = new Date();
                today.setHours(0, 0, 0, 0);

                if (selectedDate < today) {
                    addBtn.style.display = 'none';
                } else {
                    addBtn.style.display = 'inline-block';
                }

                // Show modal
                var modal = new bootstrap.Modal(document.getElementById('dateSummaryModal'));
                modal.show();
            } else {
                showAlert('error', 'ผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'ผิดพลาด', 'เกิดข้อผิดพลาดในการโหลดข้อมูล');
        });
}

// Handle "Add Appointment" button from summary modal
document.getElementById('addAppointmentFromSummary').addEventListener('click', function() {
    // Close summary modal
    var summaryModal = bootstrap.Modal.getInstance(document.getElementById('dateSummaryModal'));
    summaryModal.hide();

    // Set date and show create appointment modal
    document.getElementById('appointment_date').value = selectedSummaryDate;
    document.getElementById('submitAppointmentBtn').style.display = 'inline-block';

    setTimeout(function() {
        var createModal = new bootstrap.Modal(document.getElementById('createAppointmentModal'));
        createModal.show();
    }, 300);
});

// ========================================
// Customer Type Tabs Logic
// ========================================

// Track which tab is active
document.getElementById('existing-tab').addEventListener('click', function() {
    document.getElementById('customer_type').value = 'existing';
});

document.getElementById('new-tab').addEventListener('click', function() {
    document.getElementById('customer_type').value = 'new';
});

// Phone validation for new customer
var newPhoneInput = document.getElementById('new_patient_phone');
var phoneHelperModal = document.getElementById('phoneHelperModal');
if (newPhoneInput) {
    newPhoneInput.addEventListener('input', function(e) {
        let v = e.target.value.replace(/\D/g, '');
        if (v.length > 10) v = v.slice(0, 10);
        e.target.value = v;

        if (!v.length) {
            phoneHelperModal.textContent = 'กรุณาใส่เบอร์โทร 10 หลัก';
            phoneHelperModal.className = 'text-muted';
        } else if (v.length < 10) {
            phoneHelperModal.textContent = 'ใส่เบอร์อีก ' + (10 - v.length) + ' หลัก';
            phoneHelperModal.className = 'text-danger';
        } else {
            phoneHelperModal.textContent = '✓ เบอร์โทรถูกต้อง';
            phoneHelperModal.className = 'text-success';
        }
    });
}

// Toggle custom symptoms field
var newSymptomsSelect = document.getElementById('new_symptoms');
if (newSymptomsSelect) {
    newSymptomsSelect.addEventListener('change', function() {
        var customGroup = document.getElementById('customSymptomsModalGroup');
        if (this.value === 'อื่นๆ') {
            customGroup.style.display = 'block';
        } else {
            customGroup.style.display = 'none';
            document.getElementById('new_custom_symptoms').value = '';
        }
    });
}

// Form validation and AJAX submit
document.getElementById('appointmentForm').addEventListener('submit', function(e) {
    e.preventDefault();

    var customerType = document.getElementById('customer_type').value;

    if (customerType === 'existing') {
        // Validate existing customer - must have patient_id
        var patientId = document.getElementById('patient_id').value;
        if (!patientId) {
            showAlert('warning', 'แจ้งเตือน', 'กรุณาเลือกคนไข้จากรายการค้นหา');
            return false;
        }
    } else if (customerType === 'new') {
        // Validate new customer
        var name = document.getElementById('new_patient_name').value.trim();
        var phone = document.getElementById('new_patient_phone').value.trim();

        if (!name) {
            showAlert('warning', 'แจ้งเตือน', 'กรุณากรอกชื่อ-นามสกุลของลูกค้าใหม่');
            return false;
        }

        if (!/^\d{10}$/.test(phone)) {
            showAlert('warning', 'แจ้งเตือน', 'กรุณากรอกเบอร์โทรศัพท์ 10 หลัก');
            return false;
        }
    }

    // Submit via AJAX
    var form = this;
    var formData = new FormData(form);
    var submitBtn = document.getElementById('submitAppointmentBtn');

    submitBtn.disabled = true;
    submitBtn.innerHTML = '<i class="bi bi-hourglass-split me-1"></i>กำลังบันทึก...';

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => {
        if (!response.ok) {
            return response.text().then(text => {
                try {
                    return JSON.parse(text);
                } catch (e) {
                    throw new Error('Server error: ' + response.status);
                }
            });
        }
        return response.json();
    })
    .then(data => {
        if (data.success) {
            // Close modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('createAppointmentModal'));
            modal.hide();

            // Show success message and reload page
            showAlert('success', 'สำเร็จ', 'บันทึกนัดหมายสำเร็จ', function() {
                location.reload();
            });
        } else {
            showAlert('error', 'ผิดพลาด', data.message || 'ไม่สามารถบันทึกได้');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'ผิดพลาด', error.message);
    })
    .finally(() => {
        submitBtn.disabled = false;
        submitBtn.innerHTML = '<i class="bi bi-check2 me-1"></i>บันทึก';
    });
});

// Reset modal when closed
document.getElementById('createAppointmentModal').addEventListener('hidden.bs.modal', function() {
    // Reset to existing customer tab
    var existingTab = new bootstrap.Tab(document.getElementById('existing-tab'));
    existingTab.show();
    document.getElementById('customer_type').value = 'existing';

    // Clear new customer fields
    document.getElementById('new_patient_name').value = '';
    document.getElementById('new_patient_phone').value = '';
    document.getElementById('new_symptoms').value = '';
    document.getElementById('new_custom_symptoms').value = '';
    document.getElementById('customSymptomsModalGroup').style.display = 'none';

    // Reset phone helper
    if (phoneHelperModal) {
        phoneHelperModal.textContent = 'กรุณาใส่เบอร์โทร 10 หลัก';
        phoneHelperModal.className = 'text-muted';
    }
});

// Store current appointment data for editing
var currentAppointmentData = null;

// Show appointment detail modal
function showAppointmentDetail(event) {
    currentAppointmentData = {
        id: event.id,
        patient_name: event.extendedProps.patient_name,
        phone: event.extendedProps.phone,
        service: event.extendedProps.service,
        pt_name: event.extendedProps.pt_name,
        pt_id: event.extendedProps.pt_id || '',
        status: event.extendedProps.status,
        notes: event.extendedProps.notes || '',
        purpose: event.extendedProps.purpose || 'PHYSICAL_THERAPY',
        date: event.startStr.split('T')[0],
        time: event.startStr.split('T')[1] ? event.startStr.split('T')[1].substring(0, 5) : ''
    };

    // Populate view mode
    document.getElementById('view_appointment_id').value = currentAppointmentData.id;
    document.getElementById('view_patient_name').textContent = currentAppointmentData.patient_name;
    document.getElementById('view_phone').textContent = currentAppointmentData.phone || '-';
    document.getElementById('view_service').textContent = currentAppointmentData.service;
    document.getElementById('view_pt_name').textContent = currentAppointmentData.pt_name || 'ไม่ระบุ';
    document.getElementById('view_notes').textContent = currentAppointmentData.notes || '-';

    // Format date for display
    var dateObj = new Date(currentAppointmentData.date);
    var thaiMonths = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
    var thaiYear = dateObj.getFullYear() + 543;
    document.getElementById('view_date').textContent = dateObj.getDate() + ' ' + thaiMonths[dateObj.getMonth()] + ' ' + thaiYear;
    document.getElementById('view_time').textContent = currentAppointmentData.time || '-';

    // Status badge
    var statusMap = {
        'pending': { label: 'รอยืนยัน', class: 'bg-warning text-dark' },
        'confirmed': { label: 'ยืนยันแล้ว', class: 'bg-success' },
        'completed': { label: 'เสร็จสิ้น', class: 'bg-primary' },
        'cancelled': { label: 'ยกเลิก', class: 'bg-danger' },
        'no_show': { label: 'ไม่มา', class: 'bg-secondary' }
    };
    var statusInfo = statusMap[currentAppointmentData.status] || { label: currentAppointmentData.status, class: 'bg-secondary' };
    document.getElementById('view_status_badge').innerHTML = '<span class="badge ' + statusInfo.class + '">' + statusInfo.label + '</span>';

    // Reset to view mode
    toggleEditMode(false);

    // Show modal
    var modal = new bootstrap.Modal(document.getElementById('viewAppointmentModal'));
    modal.show();
}

// Toggle between view and edit mode
function toggleEditMode(isEdit) {
    if (isEdit && currentAppointmentData) {
        // Populate edit form with current data
        console.log('Setting edit form with data:', currentAppointmentData);

        // Set date (format: YYYY-MM-DD)
        if (currentAppointmentData.date) {
            document.getElementById('edit_date').value = currentAppointmentData.date;
        }

        // Set time (format: HH:MM for select dropdown)
        if (currentAppointmentData.time) {
            // Ensure time is in HH:MM format
            var timeParts = currentAppointmentData.time.split(':');
            var formattedTime = timeParts[0].padStart(2, '0') + ':' + (timeParts[1] || '00').substring(0, 2);
            document.getElementById('edit_time').value = formattedTime;
            console.log('Setting time to:', formattedTime);
        }

        document.getElementById('edit_pt_id').value = currentAppointmentData.pt_id || '';
        document.getElementById('edit_purpose').value = currentAppointmentData.purpose || 'PHYSICAL_THERAPY';
        document.getElementById('edit_status').value = currentAppointmentData.status || 'pending';
        document.getElementById('edit_notes').value = currentAppointmentData.notes || '';

        // Show edit mode
        document.getElementById('viewModeContent').style.display = 'none';
        document.getElementById('editModeContent').style.display = 'block';
        document.getElementById('viewModeButtons').style.display = 'none';
        document.getElementById('editModeButtons').style.display = 'block';
    } else {
        // Show view mode
        document.getElementById('viewModeContent').style.display = 'block';
        document.getElementById('editModeContent').style.display = 'none';
        document.getElementById('viewModeButtons').style.display = 'block';
        document.getElementById('editModeButtons').style.display = 'none';
    }
}

// Save appointment edit
function saveAppointmentEdit() {
    var appointmentId = document.getElementById('view_appointment_id').value;

    var dateValue = document.getElementById('edit_date').value;
    var timeValue = document.getElementById('edit_time').value;

    // Validate required fields
    if (!dateValue) {
        showAlert('warning', 'แจ้งเตือน', 'กรุณาเลือกวันที่นัดหมาย');
        return;
    }
    if (!timeValue) {
        showAlert('warning', 'แจ้งเตือน', 'กรุณาเลือกเวลานัดหมาย');
        return;
    }

    var formData = {
        appointment_date: dateValue,
        appointment_time: timeValue,
        pt_id: document.getElementById('edit_pt_id').value || null,
        purpose: document.getElementById('edit_purpose').value,
        status: document.getElementById('edit_status').value,
        notes: document.getElementById('edit_notes').value
    };

    fetch(`${BASE_URL}/appointments/${appointmentId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(formData)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Close modal
            var modal = bootstrap.Modal.getInstance(document.getElementById('viewAppointmentModal'));
            modal.hide();
            showAlert('success', 'สำเร็จ', 'บันทึกการแก้ไขสำเร็จ', function() {
                location.reload();
            });
        } else {
            showAlert('error', 'ผิดพลาด', data.message || 'ไม่สามารถบันทึกได้');
        }
    })
    .catch(error => {
        console.error('Error:', error);
        showAlert('error', 'ผิดพลาด', 'เกิดข้อผิดพลาดในการเชื่อมต่อ');
    });
}

// Delete appointment
function deleteAppointment() {
    showConfirm('คุณต้องการลบนัดหมายนี้ใช่หรือไม่?', function() {
        var appointmentId = document.getElementById('view_appointment_id').value;

        fetch(`${BASE_URL}/appointments/${appointmentId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                // Close modal
                var modal = bootstrap.Modal.getInstance(document.getElementById('viewAppointmentModal'));
                modal.hide();
                showAlert('success', 'สำเร็จ', 'ลบนัดหมายสำเร็จ', function() {
                    location.reload();
                });
            } else {
                showAlert('error', 'ผิดพลาด', data.message || 'ไม่สามารถลบได้');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'ผิดพลาด', 'เกิดข้อผิดพลาดในการเชื่อมต่อ');
        });
    });
}

// Reset view appointment modal when closed
document.getElementById('viewAppointmentModal').addEventListener('hidden.bs.modal', function() {
    toggleEditMode(false);
    currentAppointmentData = null;
});

// View appointment by ID (fetch from server)
function viewAppointmentById(id) {
    fetch(`${BASE_URL}/appointments/${id}`)
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                var apt = data.appointment;
                currentAppointmentData = {
                    id: apt.id,
                    patient_name: apt.patient ? apt.patient.name : 'ไม่ระบุ',
                    phone: apt.patient ? apt.patient.phone : '-',
                    service: apt.purpose == 'PHYSICAL_THERAPY' ? 'กายภาพบำบัด' : 'ติดตามผล',
                    pt_name: apt.pt ? (apt.pt.name || apt.pt.first_name) : 'ไม่ระบุ',
                    pt_id: apt.pt_id || '',
                    status: apt.status,
                    notes: apt.notes || '',
                    purpose: apt.purpose || 'PHYSICAL_THERAPY',
                    date: apt.appointment_date,
                    time: apt.appointment_time ? apt.appointment_time.substring(0, 5) : ''
                };

                // Populate view mode
                document.getElementById('view_appointment_id').value = currentAppointmentData.id;
                document.getElementById('view_patient_name').textContent = currentAppointmentData.patient_name;
                document.getElementById('view_phone').textContent = currentAppointmentData.phone || '-';
                document.getElementById('view_service').textContent = currentAppointmentData.service;
                document.getElementById('view_pt_name').textContent = currentAppointmentData.pt_name || 'ไม่ระบุ';
                document.getElementById('view_notes').textContent = currentAppointmentData.notes || '-';

                // Format date
                var dateObj = new Date(currentAppointmentData.date);
                var thaiMonths = ['ม.ค.', 'ก.พ.', 'มี.ค.', 'เม.ย.', 'พ.ค.', 'มิ.ย.', 'ก.ค.', 'ส.ค.', 'ก.ย.', 'ต.ค.', 'พ.ย.', 'ธ.ค.'];
                var thaiYear = dateObj.getFullYear() + 543;
                document.getElementById('view_date').textContent = dateObj.getDate() + ' ' + thaiMonths[dateObj.getMonth()] + ' ' + thaiYear;
                document.getElementById('view_time').textContent = currentAppointmentData.time || '-';

                // Status badge
                var statusMap = {
                    'pending': { label: 'รอยืนยัน', class: 'bg-warning text-dark' },
                    'confirmed': { label: 'ยืนยันแล้ว', class: 'bg-success' },
                    'completed': { label: 'เสร็จสิ้น', class: 'bg-primary' },
                    'cancelled': { label: 'ยกเลิก', class: 'bg-danger' },
                    'no_show': { label: 'ไม่มา', class: 'bg-secondary' },
                    'rescheduled': { label: 'เลื่อนนัด', class: 'bg-info' }
                };
                var statusInfo = statusMap[currentAppointmentData.status] || { label: currentAppointmentData.status, class: 'bg-secondary' };
                document.getElementById('view_status_badge').innerHTML = '<span class="badge ' + statusInfo.class + '">' + statusInfo.label + '</span>';

                toggleEditMode(false);
                var modal = new bootstrap.Modal(document.getElementById('viewAppointmentModal'));
                modal.show();
            } else {
                showAlert('error', 'ผิดพลาด', 'ไม่สามารถโหลดข้อมูลได้');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'ผิดพลาด', 'เกิดข้อผิดพลาดในการโหลดข้อมูล');
        });
}

// Edit appointment by ID
function editAppointmentById(id) {
    viewAppointmentById(id);
    setTimeout(() => {
        toggleEditMode(true);
    }, 500);
}

// Delete appointment by ID
function deleteAppointmentById(id) {
    showConfirm('คุณต้องการลบนัดหมายนี้ใช่หรือไม่?', function() {
        fetch(`${BASE_URL}/appointments/${id}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': '{{ csrf_token() }}',
                'Accept': 'application/json'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                showAlert('success', 'สำเร็จ', 'ลบนัดหมายสำเร็จ', function() {
                    location.reload();
                });
            } else {
                showAlert('error', 'ผิดพลาด', data.message || 'ไม่สามารถลบได้');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('error', 'ผิดพลาด', 'เกิดข้อผิดพลาดในการเชื่อมต่อ');
        });
    });
}
</script>
@endpush
@endsection
