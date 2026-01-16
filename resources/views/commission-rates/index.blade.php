@extends('layouts.app')

@section('title', 'สรุปรายได้พนักงาน - GCMS')

@push('styles')
<style>
    /* Header */
    .page-header {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        border-radius: 16px;
        padding: 1.5rem 2rem;
        color: white;
        margin-bottom: 1.5rem;
        box-shadow: 0 10px 25px rgba(14, 165, 233, 0.2);
    }

    /* Summary Cards */
    .summary-card {
        background: white;
        border-radius: 12px;
        padding: 1.25rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        border-left: 4px solid;
        height: 100%;
        transition: transform 0.2s;
    }

    .summary-card:hover {
        transform: translateY(-2px);
    }

    .summary-card.total { border-left-color: #0ea5e9; }
    .summary-card.salary { border-left-color: #8b5cf6; }
    .summary-card.df { border-left-color: #10b981; }
    .summary-card.commission { border-left-color: #f59e0b; }

    .summary-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .summary-icon.total { background: #f0f9ff; color: #0ea5e9; }
    .summary-icon.salary { background: #f5f3ff; color: #8b5cf6; }
    .summary-icon.df { background: #ecfdf5; color: #10b981; }
    .summary-icon.commission { background: #fffbeb; color: #f59e0b; }

    /* Table Styling */
    .staff-table {
        border-radius: 12px;
        overflow: hidden;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    }

    .staff-table thead th {
        background: #f8fafc;
        font-weight: 600;
        font-size: 0.8rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        padding: 1rem;
        border: none;
    }

    .staff-table tbody td {
        padding: 1rem;
        vertical-align: middle;
        border-color: #f1f5f9;
    }

    .staff-table tbody tr:hover {
        background: #f8fafc;
    }

    .staff-table tbody tr.clickable-row {
        cursor: pointer;
    }

    .staff-table tbody tr.clickable-row:hover {
        background: #f0f9ff;
    }

    .view-detail-btn {
        opacity: 0;
        transition: opacity 0.2s;
    }

    .clickable-row:hover .view-detail-btn {
        opacity: 1;
    }

    /* Amount Display */
    .amount-primary {
        font-weight: 700;
        color: #0284c7;
    }

    .amount-success {
        font-weight: 600;
        color: #059669;
    }

    .amount-warning {
        font-weight: 600;
        color: #d97706;
    }

    /* Filter Card */
    .filter-card {
        background: white;
        border-radius: 12px;
        padding: 1rem 1.25rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        margin-bottom: 1.5rem;
    }

    /* Staff Avatar */
    .staff-avatar {
        width: 40px;
        height: 40px;
        border-radius: 10px;
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        color: white;
        display: flex;
        align-items: center;
        justify-content: center;
        font-weight: 600;
        font-size: 0.9rem;
    }

    /* Role Badge */
    .role-badge {
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
        font-size: 0.7rem;
        font-weight: 500;
    }

    .role-badge.pt { background: #dbeafe; color: #1e40af; }
    .role-badge.admin { background: #fce7f3; color: #9d174d; }
    .role-badge.manager { background: #dcfce7; color: #166534; }

    /* Editable Salary */
    .salary-input {
        width: 100px;
        text-align: right;
        border: 1px solid #e2e8f0;
        border-radius: 6px;
        padding: 0.25rem 0.5rem;
        font-size: 0.875rem;
    }

    .salary-input:focus {
        border-color: #0ea5e9;
        outline: none;
        box-shadow: 0 0 0 2px rgba(14, 165, 233, 0.2);
    }

    /* Total Row */
    .total-row {
        background: #f8fafc !important;
        font-weight: 600;
    }

    .total-row td {
        border-top: 2px solid #e2e8f0;
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-4">
    <!-- Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1 fw-bold"><i class="bi bi-people me-2"></i>สรุปรายได้พนักงาน</h4>
            <p class="mb-0 opacity-75">{{ \Carbon\Carbon::parse($month . '-01')->locale('th')->translatedFormat('F Y') }}</p>
        </div>
        <div>
            <button class="btn btn-light btn-sm" onclick="window.print()">
                <i class="bi bi-printer me-1"></i>พิมพ์
            </button>
        </div>
    </div>

    <!-- Filter -->
    <div class="filter-card">
        <form method="GET" class="row g-3 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted">เดือน</label>
                <input type="month" class="form-control" name="month" value="{{ $month }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted">สาขา</label>
                <input type="text" class="form-control" value="{{ auth()->user()->branch->name ?? 'ไม่ระบุสาขา' }}" disabled>
                <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id }}">
            </div>
            <div class="col-md-2">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search me-1"></i>ค้นหา
                </button>
            </div>
        </form>
    </div>

    <!-- Summary Cards -->
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="summary-card total">
                <div class="d-flex align-items-center">
                    <div class="summary-icon total me-3">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div>
                        <div class="text-muted small">ยอดจ่ายรวม</div>
                        <div class="h5 mb-0 fw-bold">{{ number_format($totals['total_payout'], 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card salary">
                <div class="d-flex align-items-center">
                    <div class="summary-icon salary me-3">
                        <i class="bi bi-wallet2"></i>
                    </div>
                    <div>
                        <div class="text-muted small">เงินเดือนรวม</div>
                        <div class="h5 mb-0 fw-bold">{{ number_format($totals['salary'], 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card df">
                <div class="d-flex align-items-center">
                    <div class="summary-icon df me-3">
                        <i class="bi bi-hand-index"></i>
                    </div>
                    <div>
                        <div class="text-muted small">ค่ามือรวม ({{ $totals['case_count'] }} เคส)</div>
                        <div class="h5 mb-0 fw-bold">{{ number_format($totals['df_amount'], 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="summary-card commission">
                <div class="d-flex align-items-center">
                    <div class="summary-icon commission me-3">
                        <i class="bi bi-percent"></i>
                    </div>
                    <div>
                        <div class="text-muted small">ค่าคอมรวม ({{ $totals['course_count'] }} คอร์ส)</div>
                        <div class="h5 mb-0 fw-bold">{{ number_format($totals['commission_amount'], 2) }}</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Table -->
    <div class="card border-0 staff-table">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead>
                    <tr>
                        <th>พนักงาน</th>
                        <th>สาขา</th>
                        <th class="text-end">เงินเดือน</th>
                        <th class="text-center">เคส</th>
                        <th class="text-end">ค่ามือ</th>
                        <th class="text-center">คอร์ส</th>
                        <th class="text-end">ค่าคอม</th>
                        <th class="text-end">รวมจ่าย</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($staffSummary as $staff)
                    <tr class="clickable-row" onclick="viewStaffDetail('{{ $staff['user']->id }}')">
                        <td>
                            <div class="d-flex align-items-center">
                                <div class="staff-avatar me-2">
                                    {{ mb_substr($staff['user']->name ?? $staff['user']->username, 0, 1) }}
                                </div>
                                <div>
                                    <div class="fw-medium">{{ $staff['user']->name ?? $staff['user']->username }}</div>
                                    <span class="role-badge {{ strtolower($staff['user']->role->name ?? 'pt') }}">
                                        {{ $staff['user']->role->name ?? '-' }}
                                    </span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class="text-muted small">{{ $staff['user']->branch->name ?? '-' }}</span>
                        </td>
                        <td class="text-end" onclick="event.stopPropagation()">
                            <input type="number"
                                   class="salary-input"
                                   value="{{ $staff['salary'] }}"
                                   data-user-id="{{ $staff['user']->id }}"
                                   onchange="updateSalary(this)">
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark">{{ $staff['case_count'] }}</span>
                        </td>
                        <td class="text-end">
                            <span class="amount-success">{{ number_format($staff['df_amount'], 2) }}</span>
                        </td>
                        <td class="text-center">
                            <span class="badge bg-light text-dark">{{ $staff['course_count'] }}</span>
                        </td>
                        <td class="text-end">
                            <span class="amount-warning">{{ number_format($staff['commission_amount'], 2) }}</span>
                        </td>
                        <td class="text-end">
                            <span class="amount-primary">{{ number_format($staff['total_payout'], 2) }}</span>
                            <i class="bi bi-chevron-right ms-2 view-detail-btn text-muted"></i>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="8" class="text-center py-5">
                            <i class="bi bi-inbox text-muted d-block" style="font-size: 3rem;"></i>
                            <p class="text-muted mt-2 mb-0">ไม่พบข้อมูลพนักงาน</p>
                        </td>
                    </tr>
                    @endforelse

                    @if(count($staffSummary) > 0)
                    <tr class="total-row">
                        <td colspan="2"><strong>รวมทั้งหมด</strong></td>
                        <td class="text-end"><strong>{{ number_format($totals['salary'], 2) }}</strong></td>
                        <td class="text-center"><strong>{{ $totals['case_count'] }}</strong></td>
                        <td class="text-end"><strong>{{ number_format($totals['df_amount'], 2) }}</strong></td>
                        <td class="text-center"><strong>{{ $totals['course_count'] }}</strong></td>
                        <td class="text-end"><strong>{{ number_format($totals['commission_amount'], 2) }}</strong></td>
                        <td class="text-end"><strong class="text-primary">{{ number_format($totals['total_payout'], 2) }}</strong></td>
                    </tr>
                    @endif
                </tbody>
            </table>
        </div>
    </div>

    <!-- Note -->
    <div class="mt-3 text-muted small">
        <i class="bi bi-info-circle me-1"></i>
        คลิกที่ช่องเงินเดือนเพื่อแก้ไข การเปลี่ยนแปลงจะบันทึกอัตโนมัติ
    </div>
</div>

@push('scripts')
<script>
function updateSalary(input) {
    const userId = input.dataset.userId;
    const salary = input.value;

    fetch('/commission-rates/update-salary', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify({
            user_id: userId,
            salary: salary
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Show brief success indicator
            input.style.borderColor = '#10b981';
            setTimeout(() => {
                input.style.borderColor = '#e2e8f0';
            }, 1000);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('เกิดข้อผิดพลาดในการบันทึก');
    });
}

function viewStaffDetail(userId) {
    window.location.href = '{{ url('/commission-rates') }}/' + userId + '/detail?month={{ $month }}';
}
</script>
@endpush

@endsection
