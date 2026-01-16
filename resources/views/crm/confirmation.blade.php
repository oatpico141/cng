@extends('layouts.app')

@section('title', 'ยืนยันนัดหมาย - GCMS')

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #0ea5e9, #06b6d4);
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        color: white;
        margin-bottom: 1.5rem;
    }
    .confirmation-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        transition: all 0.2s;
    }
    .confirmation-card:hover {
        border-color: #0ea5e9;
        box-shadow: 0 2px 8px rgba(14, 165, 233, 0.15);
    }
    .confirmation-card.pending {
        border-left: 4px solid #f59e0b;
    }
    .confirmation-card.confirmed {
        border-left: 4px solid #10b981;
        background: #f0fdf4;
    }
    .confirmation-card.cancelled {
        border-left: 4px solid #ef4444;
        background: #fef2f2;
        opacity: 0.7;
    }
    .status-badge {
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }
    .status-pending { background: #fef3c7; color: #92400e; }
    .status-confirmed { background: #d1fae5; color: #065f46; }
    .status-cancelled { background: #fee2e2; color: #991b1b; }
    .status-no_answer { background: #e2e8f0; color: #475569; }
    .action-buttons .btn {
        padding: 0.3rem 0.6rem;
        font-size: 0.8rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1"><i class="bi bi-calendar-check me-2"></i>ยืนยันนัดหมาย</h4>
            <p class="mb-0 opacity-75">โทรยืนยันการมาตามนัดกับลูกค้า</p>
        </div>
        <div>
            <a href="{{ route('crm.index') }}" class="btn btn-light">
                <i class="bi bi-arrow-left me-1"></i>กลับ CRM
            </a>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 border-warning">
                <div class="h3 text-warning mb-1">{{ $confirmations->where('status', 'pending')->count() ?? 0 }}</div>
                <small class="text-muted">รอยืนยัน</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 border-success">
                <div class="h3 text-success mb-1">{{ $confirmations->where('status', 'confirmed')->count() ?? 0 }}</div>
                <small class="text-muted">ยืนยันแล้ว</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 border-secondary">
                <div class="h3 text-secondary mb-1">{{ $confirmations->where('status', 'no_answer')->count() ?? 0 }}</div>
                <small class="text-muted">ไม่รับสาย</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 border-danger">
                <div class="h3 text-danger mb-1">{{ $confirmations->where('status', 'cancelled')->count() ?? 0 }}</div>
                <small class="text-muted">ยกเลิก</small>
            </div>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="card mb-4">
        <div class="card-body py-2">
            <form class="row g-2 align-items-center">
                <div class="col-auto">
                    <label class="form-label mb-0 small">วันที่นัด:</label>
                </div>
                <div class="col-auto">
                    <input type="date" name="date" class="form-control form-control-sm" value="{{ request('date', today()->format('Y-m-d')) }}">
                </div>
                <div class="col-auto">
                    <select name="branch_id" class="form-select form-select-sm">
                        <option value="">ทุกสาขา</option>
                        @foreach($branches ?? [] as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-search me-1"></i>ค้นหา
                    </button>
                </div>
            </form>
        </div>
    </div>

    <!-- Confirmation List -->
    <div class="card">
        <div class="card-header d-flex justify-content-between align-items-center">
            <span><i class="bi bi-list-ul me-2"></i>รายการนัดหมายวันที่ {{ request('date', today()->format('d/m/Y')) }}</span>
            <button class="btn btn-sm btn-outline-primary" onclick="refreshList()">
                <i class="bi bi-arrow-clockwise me-1"></i>รีเฟรช
            </button>
        </div>
        <div class="card-body">
            @forelse($confirmations ?? [] as $confirmation)
            <div class="confirmation-card {{ $confirmation->status }}" id="card-{{ $confirmation->id }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center mb-1">
                            <h6 class="mb-0 me-2">{{ $confirmation->patient->name ?? $confirmation->patient->first_name ?? 'ไม่ระบุ' }}</h6>
                            <span class="status-badge status-{{ $confirmation->status }}">
                                @switch($confirmation->status)
                                    @case('pending') รอยืนยัน @break
                                    @case('confirmed') ยืนยันแล้ว @break
                                    @case('cancelled') ยกเลิก @break
                                    @case('no_answer') ไม่รับสาย @break
                                    @default {{ $confirmation->status }}
                                @endswitch
                            </span>
                        </div>
                        <p class="text-muted small mb-2">
                            <i class="bi bi-telephone me-1"></i>{{ $confirmation->patient->phone ?? '-' }}
                            <span class="ms-2"><i class="bi bi-clock me-1"></i>{{ $confirmation->appointment->appointment_time ?? '-' }}</span>
                            @if($confirmation->appointment->purpose)
                            <span class="ms-2"><i class="bi bi-clipboard me-1"></i>{{ $confirmation->appointment->purpose }}</span>
                            @endif
                        </p>
                        @if($confirmation->notes)
                        <p class="small text-muted mb-0"><i class="bi bi-chat-left-text me-1"></i>{{ $confirmation->notes }}</p>
                        @endif
                    </div>
                    <div class="action-buttons">
                        @if($confirmation->status === 'pending')
                        <button class="btn btn-success" onclick="confirmAppointment('{{ $confirmation->id }}')" title="ยืนยัน">
                            <i class="bi bi-check-lg"></i>
                        </button>
                        <button class="btn btn-secondary" onclick="markNoAnswer('{{ $confirmation->id }}')" title="ไม่รับสาย">
                            <i class="bi bi-telephone-x"></i>
                        </button>
                        <button class="btn btn-danger" onclick="cancelAppointment('{{ $confirmation->id }}')" title="ยกเลิก">
                            <i class="bi bi-x-lg"></i>
                        </button>
                        @endif
                        <a href="tel:{{ $confirmation->patient->phone ?? '' }}" class="btn btn-primary" title="โทร">
                            <i class="bi bi-telephone-fill"></i>
                        </a>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center text-muted py-4">
                <i class="bi bi-calendar-x display-4"></i>
                <p class="mt-2">ไม่มีนัดหมายในวันนี้</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function confirmAppointment(id) {
        if (confirm('ยืนยันว่าลูกค้าจะมาตามนัด?')) {
            updateStatus(id, 'confirmed');
        }
    }

    function markNoAnswer(id) {
        updateStatus(id, 'no_answer');
    }

    function cancelAppointment(id) {
        const reason = prompt('ระบุเหตุผลในการยกเลิก:');
        if (reason !== null) {
            updateStatus(id, 'cancelled', reason);
        }
    }

    function updateStatus(id, status, notes = '') {
        fetch(`/confirmation-lists/${id}/confirm`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({ status: status, notes: notes })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                location.reload();
            } else {
                alert('เกิดข้อผิดพลาด: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            location.reload();
        });
    }

    function refreshList() {
        location.reload();
    }
</script>
@endpush
