@extends('layouts.app')

@section('title', 'Dashboard - ' . $user->name)

@push('styles')
<style>
    /* PT Dashboard - Physical Therapy CI Design */
    :root {
        --pt-primary: #0ea5e9;      /* Sky Blue - Fresh, Health */
        --pt-secondary: #10b981;     /* Green - Recovery, Wellness */
        --pt-accent: #6366f1;        /* Indigo - Professional */
        --pt-warning: #f59e0b;       /* Amber */
        --pt-success: #22c55e;       /* Light Green */
    }

    .pt-header {
        background: linear-gradient(135deg, #0284c7 0%, #0ea5e9 50%, #38bdf8 100%);
        border-radius: 16px;
        padding: 2rem;
        color: white;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .pt-header::before {
        content: '';
        position: absolute;
        top: 0;
        right: 0;
        width: 200px;
        height: 200px;
        background: url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><circle cx="50" cy="50" r="40" fill="rgba(255,255,255,0.1)"/></svg>');
        background-size: contain;
        opacity: 0.3;
    }

    .pt-header h3 {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 1;
    }

    .pt-header .subtitle {
        font-size: 0.95rem;
        opacity: 0.95;
        display: flex;
        align-items: center;
        gap: 1rem;
        position: relative;
        z-index: 1;
    }

    .pt-header .subtitle i {
        font-size: 1.1rem;
    }

    /* Stat Cards - PT Theme */
    .pt-stat-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        border: 2px solid #e0f2fe;
        box-shadow: 0 2px 8px rgba(14, 165, 233, 0.08);
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        position: relative;
        overflow: hidden;
        height: 100%;
    }

    .pt-stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
        background: linear-gradient(180deg, var(--pt-primary), var(--pt-secondary));
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .pt-stat-card:hover {
        border-color: #bae6fd;
        box-shadow: 0 8px 24px rgba(14, 165, 233, 0.15);
        transform: translateY(-4px);
    }

    .pt-stat-card:hover::before {
        opacity: 1;
    }

    .pt-stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        margin-bottom: 1rem;
        background: linear-gradient(135deg, #e0f2fe 0%, #f0f9ff 100%);
        color: var(--pt-primary);
    }

    .pt-stat-icon.success {
        background: linear-gradient(135deg, #d1fae5 0%, #ecfdf5 100%);
        color: var(--pt-secondary);
    }

    .pt-stat-icon.warning {
        background: linear-gradient(135deg, #fef3c7 0%, #fefce8 100%);
        color: var(--pt-warning);
    }

    .pt-stat-value {
        font-size: 2.25rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
        margin-bottom: 0.5rem;
    }

    .pt-stat-label {
        color: #64748b;
        font-size: 0.875rem;
        font-weight: 600;
        letter-spacing: 0.025em;
    }

    .pt-stat-change {
        font-size: 0.8rem;
        font-weight: 700;
        padding: 4px 8px;
        border-radius: 6px;
        display: inline-flex;
        align-items: center;
        gap: 4px;
        margin-top: 0.75rem;
    }

    .pt-stat-change.positive {
        background: #d1fae5;
        color: #047857;
    }

    .pt-stat-change.negative {
        background: #fee2e2;
        color: #dc2626;
    }

    /* Income Card - Gradient */
    .pt-income-card {
        background: linear-gradient(135deg, #10b981 0%, #059669 50%, #047857 100%);
        border-radius: 16px;
        padding: 2rem;
        color: white;
        box-shadow: 0 8px 24px rgba(16, 185, 129, 0.25);
        position: relative;
        overflow: hidden;
    }

    .pt-income-card::before {
        content: '฿';
        position: absolute;
        top: -20px;
        right: -20px;
        font-size: 180px;
        font-weight: 900;
        opacity: 0.08;
        line-height: 1;
    }

    .pt-income-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1.5rem;
        position: relative;
        z-index: 1;
    }

    .pt-income-title h5 {
        font-size: 1.1rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .pt-income-title p {
        font-size: 0.875rem;
        opacity: 0.9;
        margin: 0;
    }

    .pt-income-amount {
        font-size: 3rem;
        font-weight: 900;
        margin-bottom: 1.5rem;
        position: relative;
        z-index: 1;
        text-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
    }

    .pt-income-breakdown {
        display: grid;
        grid-template-columns: 1fr 1fr;
        gap: 1.5rem;
        position: relative;
        z-index: 1;
    }

    .pt-income-item {
        background: rgba(255, 255, 255, 0.15);
        backdrop-filter: blur(10px);
        border-radius: 12px;
        padding: 1rem;
    }

    .pt-income-item-value {
        font-size: 1.5rem;
        font-weight: 700;
        margin-bottom: 0.25rem;
    }

    .pt-income-item-label {
        font-size: 0.85rem;
        opacity: 0.95;
        font-weight: 500;
    }

    /* Quick Actions - Modern */
    .pt-action-card {
        background: white;
        border: 2px solid #e0f2fe;
        border-radius: 12px;
        padding: 1.5rem 1rem;
        text-align: center;
        text-decoration: none;
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
        color: inherit;
    }

    .pt-action-card:hover {
        border-color: var(--pt-primary);
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        transform: translateY(-4px);
        box-shadow: 0 8px 24px rgba(14, 165, 233, 0.15);
        text-decoration: none;
    }

    .pt-action-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        background: linear-gradient(135deg, var(--pt-primary), #38bdf8);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.25);
    }

    .pt-action-card:hover .pt-action-icon {
        transform: scale(1.1) rotate(5deg);
    }

    .pt-action-card.success .pt-action-icon {
        background: linear-gradient(135deg, var(--pt-secondary), #34d399);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.25);
    }

    .pt-action-card.info .pt-action-icon {
        background: linear-gradient(135deg, var(--pt-accent), #818cf8);
        box-shadow: 0 4px 12px rgba(99, 102, 241, 0.25);
    }

    .pt-action-card.warning .pt-action-icon {
        background: linear-gradient(135deg, var(--pt-warning), #fbbf24);
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.25);
    }

    .pt-action-label {
        font-size: 0.95rem;
        font-weight: 600;
        color: #334155;
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .pt-header {
            padding: 1.5rem;
        }

        .pt-header h3 {
            font-size: 1.5rem;
        }

        .pt-stat-value {
            font-size: 1.75rem;
        }

        .pt-income-amount {
            font-size: 2.25rem;
        }

        .pt-income-breakdown {
            grid-template-columns: 1fr;
            gap: 1rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- PT Header -->
    <div class="pt-header">
        <h3>สวัสดี, {{ $user->name }} 👋</h3>
        <div class="subtitle">
            <span><i class="bi bi-heart-pulse-fill"></i> {{ $user->role->name ?? 'นักกายภาพบำบัด' }}</span>
            <span><i class="bi bi-building-fill"></i> {{ $user->branch->name ?? 'สาขาหลัก' }}</span>
            <span><i class="bi bi-calendar3"></i> {{ now()->locale('th')->isoFormat('D MMMM YYYY') }}</span>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <!-- Today's Appointments -->
        <div class="col-md-4">
            <div class="pt-stat-card">
                <div class="pt-stat-icon">
                    <i class="bi bi-calendar-heart"></i>
                </div>
                <div class="pt-stat-value">{{ $todayAppointments }}</div>
                <div class="pt-stat-label">นัดหมายวันนี้</div>
                @if($appointmentsChange != 0)
                <div class="pt-stat-change {{ $appointmentsChange > 0 ? 'positive' : 'negative' }}">
                    <i class="bi bi-{{ $appointmentsChange > 0 ? 'arrow-up' : 'arrow-down' }}"></i>
                    {{ abs(round($appointmentsChange, 1)) }}% จากเมื่อวาน
                </div>
                @endif
            </div>
        </div>

        <!-- Waiting Queue -->
        <div class="col-md-4">
            <div class="pt-stat-card">
                <div class="pt-stat-icon warning">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                <div class="pt-stat-value">{{ $waitingQueue }}</div>
                <div class="pt-stat-label">คิวรอ/กำลังทำ</div>
                <div class="text-muted small mt-2">
                    <i class="bi bi-info-circle me-1"></i>คิวที่ต้องดำเนินการ
                </div>
            </div>
        </div>

        <!-- Completed Today -->
        <div class="col-md-4">
            <div class="pt-stat-card">
                <div class="pt-stat-icon success">
                    <i class="bi bi-check-circle-fill"></i>
                </div>
                <div class="pt-stat-value">{{ $completedToday }}</div>
                <div class="pt-stat-label">เสร็จแล้ววันนี้</div>
                <div class="text-muted small mt-2">
                    <i class="bi bi-trophy me-1"></i>งานที่ทำสำเร็จ
                </div>
            </div>
        </div>
    </div>

    <!-- Monthly Income Card -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="pt-income-card">
                <div class="pt-income-header">
                    <div class="pt-income-title">
                        <h5><i class="bi bi-wallet2 me-2"></i>รายได้เดือนนี้</h5>
                        <p>{{ now()->locale('th')->isoFormat('MMMM YYYY') }}</p>
                    </div>
                    <a href="{{ url('/commission-rates/'.auth()->id().'/detail') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-eye me-1"></i>ดูรายละเอียด
                    </a>
                </div>

                <div class="pt-income-amount">
                    ฿{{ number_format($monthlyDF + $monthlyCommission) }}
                </div>

                <div class="pt-income-breakdown">
                    <div class="pt-income-item">
                        <div class="pt-income-item-value">฿{{ number_format($monthlyDF) }}</div>
                        <div class="pt-income-item-label"><i class="bi bi-hand-thumbs-up me-1"></i>ค่ามือ (DF)</div>
                    </div>
                    <div class="pt-income-item">
                        <div class="pt-income-item-value">฿{{ number_format($monthlyCommission) }}</div>
                        <div class="pt-income-item-label"><i class="bi bi-graph-up-arrow me-1"></i>ค่าคอมมิชชั่น</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="row g-3">
        <div class="col-6 col-md-3">
            <a href="{{ route('queue.index') }}" class="pt-action-card">
                <div class="pt-action-icon">
                    <i class="bi bi-list-check"></i>
                </div>
                <div class="pt-action-label">ดูคิว</div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('appointments.index') }}" class="pt-action-card success">
                <div class="pt-action-icon">
                    <i class="bi bi-calendar-check"></i>
                </div>
                <div class="pt-action-label">นัดหมาย</div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ route('patients.index') }}" class="pt-action-card info">
                <div class="pt-action-icon">
                    <i class="bi bi-people"></i>
                </div>
                <div class="pt-action-label">ค้นหาคนไข้</div>
            </a>
        </div>
        <div class="col-6 col-md-3">
            <a href="{{ url('/commission-rates/'.auth()->id().'/detail') }}" class="pt-action-card warning">
                <div class="pt-action-icon">
                    <i class="bi bi-cash-coin"></i>
                </div>
                <div class="pt-action-label">รายได้ของฉัน</div>
            </a>
        </div>
    </div>
</div>
@endsection
