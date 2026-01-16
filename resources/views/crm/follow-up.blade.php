@extends('layouts.app')

@section('title', 'Follow-up ลูกค้า - GCMS')

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #f59e0b, #fbbf24);
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        color: white;
        margin-bottom: 1.5rem;
    }
    .followup-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        transition: all 0.2s;
    }
    .followup-card:hover {
        border-color: #f59e0b;
        box-shadow: 0 2px 8px rgba(245, 158, 11, 0.15);
    }
    .followup-card.overdue {
        border-left: 4px solid #ef4444;
        background: #fef2f2;
    }
    .followup-card.today {
        border-left: 4px solid #f59e0b;
        background: #fffbeb;
    }
    .followup-card.upcoming {
        border-left: 4px solid #10b981;
    }
    .priority-badge {
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 600;
    }
    .priority-high { background: #fee2e2; color: #991b1b; }
    .priority-medium { background: #fef3c7; color: #92400e; }
    .priority-low { background: #d1fae5; color: #065f46; }
    .filter-tabs .btn {
        border-radius: 20px;
        padding: 0.4rem 1rem;
        font-size: 0.85rem;
    }
    .filter-tabs .btn.active {
        background: #f59e0b;
        border-color: #f59e0b;
        color: #fff;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1"><i class="bi bi-telephone-forward me-2"></i>Follow-up ลูกค้า</h4>
            <p class="mb-0 opacity-75">ติดตามลูกค้าที่ต้องโทรติดต่อ</p>
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
            <div class="card text-center p-3 border-danger">
                <div class="h3 text-danger mb-1">{{ $followUps->where('follow_up_date', '<', today())->count() ?? 0 }}</div>
                <small class="text-muted">เลยกำหนด</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 border-warning">
                <div class="h3 text-warning mb-1">{{ $followUps->where('follow_up_date', today())->count() ?? 0 }}</div>
                <small class="text-muted">วันนี้</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3 border-success">
                <div class="h3 text-success mb-1">{{ $followUps->where('follow_up_date', '>', today())->count() ?? 0 }}</div>
                <small class="text-muted">รอติดตาม</small>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="card text-center p-3">
                <div class="h3 text-primary mb-1">{{ $followUps->count() ?? 0 }}</div>
                <small class="text-muted">ทั้งหมด</small>
            </div>
        </div>
    </div>

    <!-- Filter Tabs -->
    <div class="filter-tabs mb-3">
        <button class="btn btn-outline-secondary active" data-filter="all">ทั้งหมด</button>
        <button class="btn btn-outline-danger" data-filter="overdue">เลยกำหนด</button>
        <button class="btn btn-outline-warning" data-filter="today">วันนี้</button>
        <button class="btn btn-outline-success" data-filter="upcoming">รอติดตาม</button>
    </div>

    <!-- Follow-up List -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-list-ul me-2"></i>รายการ Follow-up
        </div>
        <div class="card-body">
            @forelse($followUps ?? [] as $followUp)
            @php
                $isOverdue = $followUp->follow_up_date && $followUp->follow_up_date < today();
                $isToday = $followUp->follow_up_date && $followUp->follow_up_date->isToday();
            @endphp
            <div class="followup-card {{ $isOverdue ? 'overdue' : ($isToday ? 'today' : 'upcoming') }}" data-status="{{ $isOverdue ? 'overdue' : ($isToday ? 'today' : 'upcoming') }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div>
                        <h6 class="mb-1">{{ $followUp->patient->name ?? $followUp->patient->first_name ?? 'ไม่ระบุ' }}</h6>
                        <p class="text-muted small mb-2">
                            <i class="bi bi-telephone me-1"></i>{{ $followUp->patient->phone ?? '-' }}
                            <span class="ms-2"><i class="bi bi-calendar3 me-1"></i>{{ $followUp->follow_up_date?->format('d/m/Y') ?? '-' }}</span>
                        </p>
                        @if($followUp->notes)
                        <p class="small mb-0">{{ $followUp->notes }}</p>
                        @endif
                    </div>
                    <div class="text-end">
                        <span class="priority-badge priority-{{ $followUp->priority ?? 'medium' }}">
                            {{ $followUp->priority === 'high' ? 'ด่วน' : ($followUp->priority === 'low' ? 'ปกติ' : 'ปานกลาง') }}
                        </span>
                        <div class="mt-2">
                            <button class="btn btn-sm btn-success" onclick="markCompleted('{{ $followUp->id }}')">
                                <i class="bi bi-check"></i>
                            </button>
                            <a href="tel:{{ $followUp->patient->phone ?? '' }}" class="btn btn-sm btn-primary">
                                <i class="bi bi-telephone"></i>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
            @empty
            <div class="text-center text-muted py-4">
                <i class="bi bi-check-circle display-4"></i>
                <p class="mt-2">ไม่มีรายการ Follow-up</p>
            </div>
            @endforelse
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Filter functionality
    document.querySelectorAll('.filter-tabs .btn').forEach(btn => {
        btn.addEventListener('click', function() {
            document.querySelectorAll('.filter-tabs .btn').forEach(b => b.classList.remove('active'));
            this.classList.add('active');

            const filter = this.dataset.filter;
            document.querySelectorAll('.followup-card').forEach(card => {
                if (filter === 'all' || card.dataset.status === filter) {
                    card.style.display = 'block';
                } else {
                    card.style.display = 'none';
                }
            });
        });
    });

    function markCompleted(id) {
        if (confirm('ยืนยันว่าติดต่อลูกค้าสำเร็จแล้ว?')) {
            // TODO: Implement mark completed
            alert('บันทึกเรียบร้อย');
            location.reload();
        }
    }
</script>
@endpush
