@extends('layouts.app')

@section('title', 'ประวัติการใช้คอร์ส - ' . $coursePurchase->package->name)

@push('styles')
<style>
    .course-header {
        background: linear-gradient(135deg, #6366f1, #8b5cf6);
        border-radius: 12px;
        padding: 1.5rem;
        color: white;
        margin-bottom: 1.5rem;
    }

    .usage-table {
        font-size: 0.9rem;
    }

    .usage-table th {
        background: #f8fafc;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .usage-table td {
        vertical-align: middle;
    }

    .badge-sessions {
        background: #dbeafe;
        color: #1e40af;
        padding: 0.25rem 0.5rem;
        border-radius: 6px;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-3">
    <!-- Course Header -->
    <div class="course-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h4 class="mb-1">{{ $coursePurchase->package->name }}</h4>
                <p class="mb-0 opacity-90">
                    <i class="bi bi-person me-1"></i>{{ $coursePurchase->patient->name }}
                    <span class="mx-2">|</span>
                    <i class="bi bi-ticket-perforated me-1"></i>{{ $coursePurchase->course_number }}
                </p>
            </div>
            <div class="text-end">
                <div class="fs-3 fw-bold">{{ $coursePurchase->used_sessions }}/{{ $coursePurchase->total_sessions }}</div>
                <small class="opacity-90">ครั้งที่ใช้ไป</small>
            </div>
        </div>
    </div>

    <!-- Usage History Table -->
    <div class="card">
        <div class="card-header bg-white">
            <h5 class="mb-0">
                <i class="bi bi-clock-history me-2"></i>ประวัติการใช้คอร์ส
            </h5>
        </div>
        <div class="card-body p-0">
            @if($usageLogs->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover usage-table mb-0">
                    <thead>
                        <tr>
                            <th>วันที่</th>
                            <th>เวลา</th>
                            <th class="text-center">จำนวนครั้ง</th>
                            <th>ผู้ใช้เซสชั่น</th>
                            <th>PT/แพทย์ผู้รักษา</th>
                            <th>สาขา</th>
                            <th>หมายเหตุการรักษา</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($usageLogs as $log)
                        <tr>
                            <td>{{ \Carbon\Carbon::parse($log->usage_date)->locale('th')->isoFormat('D MMM YYYY') }}</td>
                            <td>{{ $log->created_at->format('H:i') }} น.</td>
                            <td class="text-center">
                                <span class="badge-sessions">{{ $log->sessions_used }} ครั้ง</span>
                            </td>
                            <td>{{ $log->patient->name }}</td>
                            <td>{{ $log->pt->name ?? 'N/A' }}</td>
                            <td>
                                {{ $log->branch->name ?? 'N/A' }}
                                @if($log->is_cross_branch)
                                    <span class="badge bg-warning text-dark" style="font-size: 0.65rem;">ข้ามสาขา</span>
                                @endif
                            </td>
                            <td>
                                @if($log->treatment && $log->treatment->treatment_notes)
                                    {{ $log->treatment->treatment_notes }}
                                @else
                                    <span class="text-muted">-</span>
                                @endif
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                    <tfoot>
                        <tr class="table-light">
                            <td colspan="2" class="text-end fw-bold">รวม</td>
                            <td class="text-center fw-bold">{{ $usageLogs->sum('sessions_used') }} ครั้ง</td>
                            <td colspan="4"></td>
                        </tr>
                    </tfoot>
                </table>
            </div>
            @else
            <div class="text-center py-5 text-muted">
                <i class="bi bi-inbox" style="font-size: 3rem;"></i>
                <p class="mt-3">ยังไม่มีประวัติการใช้คอร์ส</p>
            </div>
            @endif
        </div>
    </div>

    <!-- Back Button -->
    <div class="mt-3">
        <button onclick="history.back()" class="btn btn-secondary">
            <i class="bi bi-arrow-left me-1"></i>กลับ
        </button>
    </div>
</div>
@endsection
