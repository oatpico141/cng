@extends('layouts.app')

@section('title', 'คอร์สของลูกค้า - ' . ($patient->name ?? $patient->first_name) . ' - GCMS')

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #7c3aed, #8b5cf6);
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        color: white;
        margin-bottom: 1.5rem;
    }
    .course-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        transition: all 0.2s;
    }
    .course-card:hover {
        border-color: #8b5cf6;
        box-shadow: 0 4px 12px rgba(139, 92, 246, 0.15);
    }
    .course-card.expired {
        opacity: 0.6;
        background: #f8fafc;
    }
    .course-card.active {
        border-left: 4px solid #10b981;
    }
    .progress-sessions {
        height: 8px;
        border-radius: 4px;
        background: #e2e8f0;
        overflow: hidden;
    }
    .progress-sessions .bar {
        height: 100%;
        background: linear-gradient(90deg, #10b981, #34d399);
        border-radius: 4px;
        transition: width 0.3s;
    }
    .session-info {
        display: flex;
        justify-content: space-between;
        font-size: 0.8rem;
        color: #64748b;
        margin-top: 0.5rem;
    }
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .status-active { background: #d1fae5; color: #065f46; }
    .status-expired { background: #fee2e2; color: #991b1b; }
    .status-cancelled { background: #f3f4f6; color: #6b7280; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1"><i class="bi bi-box-seam me-2"></i>คอร์สของลูกค้า</h4>
            <p class="mb-0 opacity-75">{{ $patient->name ?? $patient->first_name . ' ' . $patient->last_name }} | {{ $patient->phone ?? '-' }}</p>
        </div>
        <div>
            <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-light me-2">
                <i class="bi bi-arrow-left me-1"></i>กลับ
            </a>
            <a href="{{ route('billing.index') }}?patient_id={{ $patient->id }}" class="btn btn-warning">
                <i class="bi bi-cart-plus me-1"></i>ซื้อคอร์สใหม่
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div class="h3 text-primary mb-1">{{ $patient->coursePurchases->count() ?? 0 }}</div>
                <small class="text-muted">คอร์สทั้งหมด</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div class="h3 text-success mb-1">{{ $patient->coursePurchases->where('status', 'active')->count() ?? 0 }}</div>
                <small class="text-muted">คอร์สใช้งานได้</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div class="h3 text-info mb-1">{{ $patient->coursePurchases->sum('total_sessions') - $patient->coursePurchases->sum('used_sessions') }}</div>
                <small class="text-muted">ครั้งคงเหลือรวม</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div class="h3 text-warning mb-1">{{ $patient->coursePurchases->sum('used_sessions') }}</div>
                <small class="text-muted">ครั้งที่ใช้ไปแล้ว</small>
            </div>
        </div>
    </div>

    <!-- Course List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-list-ul me-2"></i>รายการคอร์สทั้งหมด</span>
        </div>
        <div class="card-body">
            @forelse($patient->coursePurchases()->with('package')->orderBy('created_at', 'desc')->get() as $course)
            @php
                $remaining = $course->total_sessions - $course->used_sessions;
                $progress = $course->total_sessions > 0 ? ($course->used_sessions / $course->total_sessions) * 100 : 0;
                $isExpired = $course->expiry_date && $course->expiry_date->isPast();
            @endphp
            <div class="course-card {{ $course->status === 'active' && !$isExpired ? 'active' : '' }} {{ $isExpired ? 'expired' : '' }}">
                <div class="d-flex justify-content-between align-items-start mb-2">
                    <div>
                        <h5 class="mb-1">{{ $course->package->name ?? 'ไม่ระบุ' }}</h5>
                        <small class="text-muted">{{ $course->course_number }}</small>
                    </div>
                    <span class="status-badge status-{{ $course->status }}">
                        @if($isExpired)
                            หมดอายุ
                        @elseif($course->status === 'active')
                            ใช้งานได้
                        @elseif($course->status === 'completed')
                            ใช้ครบแล้ว
                        @elseif($course->status === 'cancelled')
                            ยกเลิก
                        @else
                            {{ $course->status }}
                        @endif
                    </span>
                </div>

                <div class="progress-sessions mb-1">
                    <div class="bar" style="width: {{ $progress }}%"></div>
                </div>
                <div class="session-info">
                    <span>ใช้ไป {{ $course->used_sessions }}/{{ $course->total_sessions }} ครั้ง</span>
                    <span class="fw-bold {{ $remaining > 0 ? 'text-success' : 'text-danger' }}">คงเหลือ {{ $remaining }} ครั้ง</span>
                </div>

                <div class="row mt-3 text-muted small">
                    <div class="col-6 col-md-3">
                        <i class="bi bi-calendar-plus me-1"></i>ซื้อ: {{ $course->purchase_date?->format('d/m/Y') ?? '-' }}
                    </div>
                    <div class="col-6 col-md-3">
                        <i class="bi bi-calendar-x me-1"></i>หมดอายุ: {{ $course->expiry_date?->format('d/m/Y') ?? '-' }}
                    </div>
                    <div class="col-6 col-md-3">
                        <i class="bi bi-tag me-1"></i>{{ number_format($course->package->price ?? 0) }} บาท
                    </div>
                    <div class="col-6 col-md-3">
                        <i class="bi bi-bag me-1"></i>{{ $course->purchase_pattern === 'buy_and_use' ? 'ซื้อ+ใช้เลย' : ($course->purchase_pattern === 'buy_for_later' ? 'ซื้อเก็บไว้' : 'ย้อนหลัง') }}
                    </div>
                </div>

                @if($course->status === 'active' && !$isExpired && $remaining > 0)
                <div class="mt-3 pt-3 border-top">
                    <a href="{{ route('course-usage-logs.show', $course->id) }}" class="btn btn-sm btn-outline-primary">
                        <i class="bi bi-list-ul me-1"></i>ดูประวัติใช้งาน
                    </a>
                </div>
                @endif
            </div>
            @empty
            <div class="text-center text-muted py-4">
                <i class="bi bi-box display-4"></i>
                <p class="mt-2">ยังไม่มีคอร์ส</p>
                <a href="{{ route('billing.index') }}?patient_id={{ $patient->id }}" class="btn btn-primary">
                    <i class="bi bi-cart-plus me-1"></i>ซื้อคอร์สแรก
                </a>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
