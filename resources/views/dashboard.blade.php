@extends('layouts.app')

@section('title', 'แดชบอร์ด - GCMS')

@push('styles')
<style>
    /* ==================== MODERN DASHBOARD 2024 ==================== */

    /* Welcome Header */
    .welcome-header {
        background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 50%, #6366f1 100%);
        border-radius: 20px;
        padding: 2rem 2.5rem;
        color: white;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .welcome-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .welcome-header::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: 10%;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(255,255,255,0.08) 0%, transparent 70%);
        border-radius: 50%;
    }

    .welcome-content {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .welcome-text h2 {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .welcome-text p {
        font-size: 0.95rem;
        opacity: 0.95;
        margin: 0;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .welcome-text p i {
        font-size: 1rem;
    }

    .branch-badge {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        padding: 0.75rem 1.25rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        border: 1px solid rgba(255,255,255,0.3);
    }

    .branch-badge i {
        font-size: 1.25rem;
    }

    .branch-badge .branch-info {
        text-align: left;
    }

    .branch-badge .branch-label {
        font-size: 0.7rem;
        opacity: 0.9;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .branch-badge .branch-name {
        font-size: 0.95rem;
        font-weight: 600;
    }

    .btn-change-branch {
        background: rgba(255,255,255,0.2);
        border: 1px solid rgba(255,255,255,0.3);
        color: white;
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 500;
        transition: all 0.3s ease;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
    }

    .btn-change-branch:hover {
        background: rgba(255,255,255,0.3);
        color: white;
        transform: translateY(-2px);
    }

    /* KPI Cards Grid */
    .kpi-grid {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .kpi-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        border: 1px solid #f1f5f9;
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .kpi-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.08);
    }

    .kpi-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        border-radius: 16px 16px 0 0;
    }

    .kpi-card.revenue::before { background: linear-gradient(90deg, #0ea5e9, #06b6d4); }
    .kpi-card.patients::before { background: linear-gradient(90deg, #10b981, #34d399); }
    .kpi-card.queue::before { background: linear-gradient(90deg, #6366f1, #8b5cf6); }
    .kpi-card.new-patients::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }

    .kpi-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .kpi-card.revenue .kpi-icon { background: linear-gradient(135deg, #e0f2fe, #cffafe); color: #0369a1; }
    .kpi-card.patients .kpi-icon { background: linear-gradient(135deg, #dcfce7, #d1fae5); color: #166534; }
    .kpi-card.queue .kpi-icon { background: linear-gradient(135deg, #e0e7ff, #ede9fe); color: #4338ca; }
    .kpi-card.new-patients .kpi-icon { background: linear-gradient(135deg, #fef3c7, #fef9c3); color: #92400e; }

    .kpi-change {
        font-size: 0.7rem;
        font-weight: 600;
        padding: 4px 8px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 2px;
    }

    .kpi-change.positive {
        background: #dcfce7;
        color: #166534;
    }

    .kpi-change.negative {
        background: #fee2e2;
        color: #dc2626;
    }

    .kpi-value {
        font-size: 2rem;
        font-weight: 800;
        color: #0f172a;
        line-height: 1;
        margin-bottom: 0.25rem;
        letter-spacing: -0.5px;
    }

    .kpi-label {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 500;
    }

    /* Section Cards */
    .section-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1.5rem;
    }

    .section-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        border: 1px solid #f1f5f9;
    }

    .section-header {
        display: flex;
        justify-content: space-between;
        align-items: center;
        margin-bottom: 1.25rem;
        padding-bottom: 0.75rem;
        border-bottom: 1px solid #f1f5f9;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0;
    }

    .section-title i {
        font-size: 1.1rem;
        color: #0ea5e9;
    }

    /* Customer Type Items */
    .customer-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .customer-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
        background: #f8fafc;
        border-radius: 12px;
        border-left: 4px solid;
        transition: all 0.2s ease;
    }

    .customer-item:hover {
        background: #f1f5f9;
        transform: translateX(4px);
    }

    .customer-item.new-customer { border-left-color: #10b981; }
    .customer-item.course-customer { border-left-color: #f59e0b; }
    .customer-item.old-customer { border-left-color: #0ea5e9; }

    .customer-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .customer-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    .customer-item.new-customer .customer-icon {
        background: #dcfce7;
        color: #166534;
    }

    .customer-item.course-customer .customer-icon {
        background: #fef3c7;
        color: #92400e;
    }

    .customer-item.old-customer .customer-icon {
        background: #e0f2fe;
        color: #0369a1;
    }

    .customer-details h6 {
        font-size: 0.9rem;
        font-weight: 600;
        color: #334155;
        margin: 0 0 0.25rem 0;
    }

    .customer-details small {
        font-size: 0.8rem;
        color: #64748b;
    }

    .customer-badge {
        font-size: 0.7rem;
        font-weight: 600;
        padding: 4px 10px;
        border-radius: 20px;
        display: inline-flex;
        align-items: center;
        gap: 3px;
    }

    /* Queue Status Items */
    .queue-list {
        display: flex;
        flex-direction: column;
        gap: 0.75rem;
    }

    .queue-item {
        display: flex;
        align-items: center;
        justify-content: space-between;
        padding: 1rem 1.25rem;
        background: #f8fafc;
        border-radius: 12px;
        border-left: 4px solid;
        transition: all 0.2s ease;
    }

    .queue-item:hover {
        background: #f1f5f9;
        transform: translateX(4px);
    }

    .queue-item.waiting { border-left-color: #f59e0b; }
    .queue-item.in-progress { border-left-color: #0ea5e9; }
    .queue-item.completed { border-left-color: #10b981; }

    .queue-info {
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .queue-icon {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
    }

    .queue-item.waiting .queue-icon {
        background: #fef3c7;
        color: #92400e;
    }

    .queue-item.in-progress .queue-icon {
        background: #e0f2fe;
        color: #0369a1;
    }

    .queue-item.completed .queue-icon {
        background: #dcfce7;
        color: #166534;
    }

    .queue-details h6 {
        font-size: 0.9rem;
        font-weight: 600;
        color: #334155;
        margin: 0 0 0.25rem 0;
    }

    .queue-details small {
        font-size: 0.8rem;
        color: #64748b;
    }

    .queue-badge {
        font-size: 0.75rem;
        font-weight: 600;
        padding: 6px 12px;
        border-radius: 8px;
    }

    .queue-item.waiting .queue-badge {
        background: #fef3c7;
        color: #92400e;
    }

    .queue-item.in-progress .queue-badge {
        background: #e0f2fe;
        color: #0369a1;
    }

    .queue-item.completed .queue-badge {
        background: #dcfce7;
        color: #166534;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .kpi-grid {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 992px) {
        .section-grid {
            grid-template-columns: 1fr;
        }
    }

    @media (max-width: 768px) {
        .welcome-header {
            padding: 1.5rem;
            border-radius: 16px;
        }

        .welcome-text h2 {
            font-size: 1.35rem;
        }

        .welcome-content {
            flex-direction: column;
            align-items: flex-start;
        }

        .branch-badge {
            width: 100%;
            justify-content: center;
        }

        .kpi-grid {
            grid-template-columns: repeat(2, 1fr);
            gap: 0.75rem;
        }

        .kpi-card {
            padding: 1rem;
            border-radius: 12px;
        }

        .kpi-icon {
            width: 40px;
            height: 40px;
            font-size: 1rem;
        }

        .kpi-value {
            font-size: 1.5rem;
        }

        .kpi-label {
            font-size: 0.75rem;
        }

        .section-card {
            padding: 1rem;
            border-radius: 12px;
        }

        .customer-item,
        .queue-item {
            padding: 0.75rem 1rem;
        }

        .customer-icon,
        .queue-icon {
            width: 36px;
            height: 36px;
            font-size: 0.9rem;
        }
    }

    @media (max-width: 480px) {
        .kpi-grid {
            grid-template-columns: 1fr 1fr;
        }

        .kpi-header {
            flex-direction: column;
            gap: 0.5rem;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    {{-- Branch Info --}}
    @php
        $currentBranch = null;
        if (session('selected_branch_id')) {
            $currentBranch = \App\Models\Branch::find(session('selected_branch_id'));
        }
        if (!$currentBranch) {
            $currentBranch = auth()->user()->branch;
        }
    @endphp

    <!-- Welcome Header -->
    <div class="welcome-header">
        <div class="welcome-content">
            <div class="welcome-text">
                <h2>สวัสดี, {{ Auth::user()->name ?? 'Admin' }} 👋</h2>
                <p>
                    <i class="bi bi-calendar3"></i>
                    {{ now()->locale('th')->isoFormat('วันdddd ที่ D MMMM YYYY') }}
                </p>
            </div>
            <div class="d-flex align-items-center gap-2 flex-wrap">
                <div class="branch-badge">
                    <i class="bi bi-building"></i>
                    <div class="branch-info">
                        <div class="branch-label">สาขาปัจจุบัน</div>
                        <div class="branch-name">{{ $currentBranch->name ?? 'ไม่ระบุสาขา' }}</div>
                    </div>
                </div>
                @if(auth()->user()->needsBranchSelection())
                <a href="{{ route('branch.selector') }}" class="btn-change-branch">
                    <i class="bi bi-arrow-repeat"></i>
                    เปลี่ยนสาขา
                </a>
                @endif
            </div>
        </div>
    </div>

    <!-- KPI Cards -->
    <div class="kpi-grid">
        <div class="kpi-card revenue">
            <div class="kpi-header">
                <div class="kpi-icon">
                    <i class="bi bi-wallet2"></i>
                </div>
                @if($revenueChange >= 0)
                <span class="kpi-change positive"><i class="bi bi-arrow-up"></i> {{ number_format(abs($revenueChange), 0) }}%</span>
                @else
                <span class="kpi-change negative"><i class="bi bi-arrow-down"></i> {{ number_format(abs($revenueChange), 0) }}%</span>
                @endif
            </div>
            <div class="kpi-value">฿{{ number_format($todayRevenue, 0) }}</div>
            <div class="kpi-label">รายได้วันนี้</div>
        </div>

        <div class="kpi-card patients">
            <div class="kpi-header">
                <div class="kpi-icon">
                    <i class="bi bi-calendar-check"></i>
                </div>
                @if($patientsChange >= 0)
                <span class="kpi-change positive"><i class="bi bi-arrow-up"></i> {{ number_format(abs($patientsChange), 0) }}%</span>
                @else
                <span class="kpi-change negative"><i class="bi bi-arrow-down"></i> {{ number_format(abs($patientsChange), 0) }}%</span>
                @endif
            </div>
            <div class="kpi-value">{{ $todayPatients }}</div>
            <div class="kpi-label">นัดหมายวันนี้</div>
        </div>

        <div class="kpi-card queue">
            <div class="kpi-header">
                <div class="kpi-icon">
                    <i class="bi bi-hourglass-split"></i>
                </div>
                @if($queueChange >= 0)
                <span class="kpi-change positive"><i class="bi bi-arrow-up"></i> {{ number_format(abs($queueChange), 0) }}%</span>
                @else
                <span class="kpi-change negative"><i class="bi bi-arrow-down"></i> {{ number_format(abs($queueChange), 0) }}%</span>
                @endif
            </div>
            <div class="kpi-value">{{ $waitingQueue }}</div>
            <div class="kpi-label">คิวรอ</div>
        </div>

        <div class="kpi-card new-patients">
            <div class="kpi-header">
                <div class="kpi-icon">
                    <i class="bi bi-person-plus"></i>
                </div>
                @if($newPatientsChange >= 0)
                <span class="kpi-change positive"><i class="bi bi-arrow-up"></i> {{ number_format(abs($newPatientsChange), 0) }}%</span>
                @else
                <span class="kpi-change negative"><i class="bi bi-arrow-down"></i> {{ number_format(abs($newPatientsChange), 0) }}%</span>
                @endif
            </div>
            <div class="kpi-value">{{ $todayNewPatients }}</div>
            <div class="kpi-label">ลูกค้าใหม่วันนี้</div>
        </div>
    </div>

    <!-- Patient Classification & Queue Status -->
    <div class="section-grid">
        <!-- Patient Classification -->
        <div class="section-card">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="bi bi-people-fill"></i>
                    จำแนกประเภทลูกค้า
                </h3>
            </div>
            <div class="customer-list">
                <div class="customer-item new-customer">
                    <div class="customer-info">
                        <div class="customer-icon">
                            <i class="bi bi-person-plus-fill"></i>
                        </div>
                        <div class="customer-details">
                            <h6>ลูกค้าใหม่</h6>
                            <small>{{ $todayNewPatients }} คน</small>
                        </div>
                    </div>
                    <span class="customer-badge" style="background: #dcfce7; color: #166534;">
                        @if($newPatientsChange >= 0)
                            <i class="bi bi-arrow-up"></i> {{ number_format(abs($newPatientsChange), 0) }}%
                        @else
                            <i class="bi bi-arrow-down"></i> {{ number_format(abs($newPatientsChange), 0) }}%
                        @endif
                    </span>
                </div>

                <div class="customer-item course-customer">
                    <div class="customer-info">
                        <div class="customer-icon">
                            <i class="bi bi-card-checklist"></i>
                        </div>
                        <div class="customer-details">
                            <h6>ลูกค้าคอร์ส</h6>
                            <small>{{ $todayCoursePatients }} คน</small>
                        </div>
                    </div>
                    <span class="customer-badge" style="background: #fef3c7; color: #92400e;">
                        @if($coursePatientsChange >= 0)
                            <i class="bi bi-arrow-up"></i> {{ number_format(abs($coursePatientsChange), 0) }}%
                        @else
                            <i class="bi bi-arrow-down"></i> {{ number_format(abs($coursePatientsChange), 0) }}%
                        @endif
                    </span>
                </div>

                <div class="customer-item old-customer">
                    <div class="customer-info">
                        <div class="customer-icon">
                            <i class="bi bi-person-check-fill"></i>
                        </div>
                        <div class="customer-details">
                            <h6>ลูกค้าเก่า</h6>
                            <small>{{ $todayOldPatients }} คน</small>
                        </div>
                    </div>
                    <span class="customer-badge" style="background: #e0f2fe; color: #0369a1;">
                        @if($oldPatientsChange >= 0)
                            <i class="bi bi-arrow-up"></i> {{ number_format(abs($oldPatientsChange), 0) }}%
                        @else
                            <i class="bi bi-arrow-down"></i> {{ number_format(abs($oldPatientsChange), 0) }}%
                        @endif
                    </span>
                </div>
            </div>
        </div>

        <!-- Queue Status -->
        <div class="section-card">
            <div class="section-header">
                <h3 class="section-title">
                    <i class="bi bi-list-ol"></i>
                    สถานะคิว
                </h3>
            </div>
            <div class="queue-list">
                <div class="queue-item waiting">
                    <div class="queue-info">
                        <div class="queue-icon">
                            <i class="bi bi-clock-fill"></i>
                        </div>
                        <div class="queue-details">
                            <h6>รอตรวจ</h6>
                            <small>{{ $queueWaiting }} คน</small>
                        </div>
                    </div>
                    <span class="queue-badge">รอ</span>
                </div>

                <div class="queue-item in-progress">
                    <div class="queue-info">
                        <div class="queue-icon">
                            <i class="bi bi-play-circle-fill"></i>
                        </div>
                        <div class="queue-details">
                            <h6>กำลังตรวจ</h6>
                            <small>{{ $queueInProgress }} คน</small>
                        </div>
                    </div>
                    <span class="queue-badge">ดำเนินการ</span>
                </div>

                <div class="queue-item completed">
                    <div class="queue-info">
                        <div class="queue-icon">
                            <i class="bi bi-check-circle-fill"></i>
                        </div>
                        <div class="queue-details">
                            <h6>เสร็จแล้ว</h6>
                            <small>{{ $queueCompleted }} คน</small>
                        </div>
                    </div>
                    <span class="queue-badge">เสร็จ</span>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('switchBranchBtn')?.addEventListener('click', function() {
    const branchId = document.getElementById('branchSelector').value;
    const button = this;

    button.disabled = true;
    button.innerHTML = '<span class="spinner-border spinner-border-sm"></span>';

    fetch('{{ route('branch.switch') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify({ branch_id: branchId })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            window.location.reload();
        } else {
            alert('เกิดข้อผิดพลาด');
            button.disabled = false;
            button.innerHTML = '<i class="bi bi-arrow-repeat"></i>';
        }
    })
    .catch(error => {
        alert('เกิดข้อผิดพลาด');
        button.disabled = false;
        button.innerHTML = '<i class="bi bi-arrow-repeat"></i>';
    });
});
</script>
@endpush
