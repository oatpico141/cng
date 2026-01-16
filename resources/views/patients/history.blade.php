@extends('layouts.app')

@section('title', 'ประวัติการรักษา - ' . ($patient->name ?? $patient->first_name) . ' - GCMS')

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #0284c7, #0ea5e9);
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        color: white;
        margin-bottom: 1.5rem;
    }
    .timeline-item {
        position: relative;
        padding-left: 30px;
        padding-bottom: 1.5rem;
        border-left: 2px solid #e2e8f0;
        margin-left: 10px;
    }
    .timeline-item:last-child {
        border-left: 2px solid transparent;
    }
    .timeline-item::before {
        content: '';
        position: absolute;
        left: -8px;
        top: 0;
        width: 14px;
        height: 14px;
        border-radius: 50%;
        background: #0ea5e9;
        border: 2px solid #fff;
        box-shadow: 0 0 0 2px #0ea5e9;
    }
    .timeline-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 1rem;
    }
    .timeline-date {
        font-size: 0.75rem;
        color: #64748b;
        margin-bottom: 0.5rem;
    }
    .treatment-badge {
        background: #dbeafe;
        color: #1e40af;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.75rem;
        font-weight: 500;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1"><i class="bi bi-clock-history me-2"></i>ประวัติการรักษา</h4>
            <p class="mb-0 opacity-75">{{ $patient->name ?? $patient->first_name . ' ' . $patient->last_name }} | {{ $patient->phone ?? '-' }}</p>
        </div>
        <a href="{{ route('patients.show', $patient->id) }}" class="btn btn-light">
            <i class="bi bi-arrow-left me-1"></i>กลับ
        </a>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div class="h3 text-primary mb-1">{{ $patient->treatments->count() ?? 0 }}</div>
                <small class="text-muted">ครั้งที่รักษา</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div class="h3 text-success mb-1">{{ $patient->appointments->where('status', 'completed')->count() ?? 0 }}</div>
                <small class="text-muted">นัดหมายสำเร็จ</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div class="h3 text-info mb-1">{{ $patient->coursePurchases->count() ?? 0 }}</div>
                <small class="text-muted">คอร์สที่ซื้อ</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div class="h3 text-warning mb-1">{{ number_format($patient->invoices->where('status', 'paid')->sum('total_amount') ?? 0) }}</div>
                <small class="text-muted">ยอดใช้จ่าย (บาท)</small>
            </div>
        </div>
    </div>

    <!-- Timeline -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-list-ul me-2"></i>ประวัติการรักษาทั้งหมด
        </div>
        <div class="card-body">
            @forelse($patient->treatments()->with(['service', 'pt', 'appointment'])->orderBy('created_at', 'desc')->get() as $treatment)
            <div class="timeline-item">
                <div class="timeline-card">
                    <div class="timeline-date">
                        <i class="bi bi-calendar3 me-1"></i>{{ $treatment->created_at->locale('th')->translatedFormat('j F Y H:i น.') }}
                    </div>
                    <div class="d-flex justify-content-between align-items-start">
                        <div>
                            <h6 class="mb-1">{{ $treatment->service->name ?? 'ไม่ระบุบริการ' }}</h6>
                            <p class="text-muted small mb-2">
                                <i class="bi bi-person me-1"></i>PT: {{ $treatment->pt->name ?? '-' }}
                                @if($treatment->notes)
                                <br><i class="bi bi-chat-left-text me-1"></i>{{ $treatment->notes }}
                                @endif
                            </p>
                            @if($treatment->coursePurchase)
                            <span class="treatment-badge">
                                <i class="bi bi-box me-1"></i>ใช้คอร์ส: {{ $treatment->coursePurchase->package->name ?? '-' }}
                            </span>
                            @endif
                        </div>
                        <div class="text-end">
                            @if($treatment->total_price)
                            <span class="fw-bold text-primary">{{ number_format($treatment->total_price) }} ฿</span>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center text-muted py-4">
                <i class="bi bi-inbox display-4"></i>
                <p class="mt-2">ยังไม่มีประวัติการรักษา</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection
