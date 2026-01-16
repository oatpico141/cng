@extends('layouts.app')

@section('title', 'รายงานกำไรขาดทุน - GCMS')

@push('styles')
<style>
    /* MODERN P&L REPORT - CLEAN & INFORMATIVE */

    .page-header {
        background: linear-gradient(135deg, #0284c7, #0ea5e9);
        border-radius: 12px;
        padding: 1.5rem;
        color: white;
        margin-bottom: 1.25rem;
    }

    /* Filter Card */
    .filter-card {
        background: #fff;
        border-radius: 10px;
        padding: 1.25rem;
        border: 1px solid #e2e8f0;
        margin-bottom: 1.25rem;
    }

    /* KPI Cards */
    .kpi-card {
        background: #fff;
        border-radius: 10px;
        padding: 1.25rem;
        border: 1px solid #e2e8f0;
        height: 100%;
        transition: all 0.2s;
    }

    .kpi-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(0,0,0,0.08);
    }

    .kpi-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
        margin-bottom: 1rem;
    }

    .kpi-icon.revenue { background: #dbeafe; color: #0369a1; }
    .kpi-icon.expense { background: #fed7aa; color: #ea580c; }
    .kpi-icon.profit { background: #dcfce7; color: #16a34a; }
    .kpi-icon.margin { background: #e0e7ff; color: #4f46e5; }

    .kpi-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: #1e293b;
        line-height: 1;
    }

    .kpi-label {
        font-size: 0.75rem;
        color: #64748b;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 0.5rem;
    }

    .kpi-change {
        font-size: 0.75rem;
        font-weight: 600;
        margin-top: 0.75rem;
        padding: 2px 8px;
        border-radius: 12px;
        display: inline-block;
    }

    .kpi-change.positive { background: #dcfce7; color: #166534; }
    .kpi-change.negative { background: #fee2e2; color: #dc2626; }

    /* Section Card */
    .section-card {
        background: #fff;
        border-radius: 10px;
        padding: 1.25rem;
        border: 1px solid #e2e8f0;
        margin-bottom: 1.25rem;
    }

    .section-title {
        font-size: 1rem;
        font-weight: 600;
        color: #334155;
        margin-bottom: 1rem;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    /* Chart Canvas */
    canvas {
        max-height: 300px;
    }

    /* Table */
    .data-table {
        font-size: 0.875rem;
    }

    .data-table thead {
        background: #f8fafc;
    }

    .data-table thead th {
        padding: 0.75rem 1rem;
        font-weight: 600;
        color: #475569;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        border: none;
    }

    .data-table tbody td {
        padding: 0.875rem 1rem;
        vertical-align: middle;
        border-top: 1px solid #e2e8f0;
    }

    .data-table tbody tr:hover {
        background: #f8fafc;
    }

    .data-table tfoot {
        background: #f1f5f9;
        font-weight: 700;
    }

    /* Type Badge */
    .type-badge {
        padding: 3px 10px;
        border-radius: 12px;
        font-size: 0.7rem;
        font-weight: 600;
        display: inline-block;
    }

    .type-badge.service { background: #dbeafe; color: #0369a1; }
    .type-badge.product { background: #fef3c7; color: #92400e; }
    .type-badge.course { background: #dcfce7; color: #166534; }

    /* Mobile */
    @media (max-width: 768px) {
        .kpi-value { font-size: 1.5rem; }
        .page-header { padding: 1rem; }
        .section-card { padding: 1rem; }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-1" style="font-size: 1.25rem; font-weight: 600;">
                    <i class="bi bi-graph-up-arrow me-2"></i>รายงานกำไรขาดทุน (P&L)
                </h2>
                <p class="mb-0 opacity-90" style="font-size: 0.85rem;">Profit & Loss Statement</p>
            </div>
            <button class="btn btn-light btn-sm" onclick="window.print()">
                <i class="bi bi-printer me-1"></i>พิมพ์
            </button>
        </div>
    </div>

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('reports.pnl') }}">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">สาขา</label>
                    <input type="text" class="form-control form-control-sm" value="{{ auth()->user()->branch->name ?? 'ไม่ระบุสาขา' }}" disabled>
                    <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">วันที่เริ่มต้น</label>
                    <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
                </div>
                <div class="col-md-3">
                    <label class="form-label small text-muted mb-1">วันที่สิ้นสุด</label>
                    <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary btn-sm w-100">
                        <i class="bi bi-funnel me-1"></i>กรอง
                    </button>
                </div>
            </div>
        </form>
    </div>

    <!-- KPI Cards -->
    @php
        $profitMargin = $totalRevenue > 0 ? ($netProfit / $totalRevenue) * 100 : 0;
    @endphp
    <div class="row g-3 mb-3">
        <div class="col-6 col-lg-3">
            <div class="kpi-card">
                <div class="kpi-icon revenue">
                    <i class="bi bi-cash-stack"></i>
                </div>
                <div class="kpi-value">฿{{ number_format($totalRevenue, 0) }}</div>
                <div class="kpi-label">รายได้สุทธิ</div>
                @if($revenueChange != 0)
                <span class="kpi-change {{ $revenueChange > 0 ? 'positive' : 'negative' }}">
                    <i class="bi bi-arrow-{{ $revenueChange > 0 ? 'up' : 'down' }}"></i>
                    {{ number_format(abs($revenueChange), 1) }}%
                </span>
                @endif
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="kpi-card">
                <div class="kpi-icon expense">
                    <i class="bi bi-receipt-cutoff"></i>
                </div>
                <div class="kpi-value">฿{{ number_format($totalExpenses, 0) }}</div>
                <div class="kpi-label">ค่าใช้จ่ายรวม</div>
                @if($expenseChange != 0)
                <span class="kpi-change {{ $expenseChange > 0 ? 'negative' : 'positive' }}">
                    <i class="bi bi-arrow-{{ $expenseChange > 0 ? 'up' : 'down' }}"></i>
                    {{ number_format(abs($expenseChange), 1) }}%
                </span>
                @endif
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="kpi-card">
                <div class="kpi-icon profit">
                    <i class="bi bi-graph-up"></i>
                </div>
                <div class="kpi-value {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                    ฿{{ number_format($netProfit, 0) }}
                </div>
                <div class="kpi-label">กำไรสุทธิ</div>
                @if($profitChange != 0)
                <span class="kpi-change {{ $profitChange > 0 ? 'positive' : 'negative' }}">
                    <i class="bi bi-arrow-{{ $profitChange > 0 ? 'up' : 'down' }}"></i>
                    {{ number_format(abs($profitChange), 1) }}%
                </span>
                @endif
            </div>
        </div>

        <div class="col-6 col-lg-3">
            <div class="kpi-card">
                <div class="kpi-icon margin">
                    <i class="bi bi-percent"></i>
                </div>
                <div class="kpi-value">{{ number_format($profitMargin, 1) }}%</div>
                <div class="kpi-label">อัตรากำไร (Margin)</div>
            </div>
        </div>
    </div>

    <!-- Charts Row -->
    <div class="row g-3 mb-3">
        <!-- Revenue by Type -->
        <div class="col-lg-6">
            <div class="section-card">
                <div class="section-title">
                    <i class="bi bi-pie-chart-fill text-primary"></i>
                    สัดส่วนรายได้ตามประเภท
                </div>
                <canvas id="revenueTypeChart"></canvas>
            </div>
        </div>

        <!-- Top 5 Items -->
        <div class="col-lg-6">
            <div class="section-card">
                <div class="section-title">
                    <i class="bi bi-trophy-fill text-warning"></i>
                    รายการขายดี Top 5
                </div>
                <div class="table-responsive">
                    <table class="table data-table mb-0">
                        <thead>
                            <tr>
                                <th>รายการ</th>
                                <th>ประเภท</th>
                                <th class="text-end">ยอดขาย</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($topItems as $item)
                            <tr>
                                <td>
                                    <div class="fw-semibold">{{ $item->item_name }}</div>
                                    <small class="text-muted">{{ $item->qty }} รายการ</small>
                                </td>
                                <td>
                                    <span class="type-badge {{ strtolower($item->item_type) }}">
                                        {{ ucfirst($item->item_type) }}
                                    </span>
                                </td>
                                <td class="text-end fw-semibold">฿{{ number_format($item->revenue, 0) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center text-muted py-3">
                                    <i class="bi bi-inbox fs-3 d-block mb-1"></i>
                                    ไม่มีข้อมูล
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Expense Breakdown -->
    <div class="section-card">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <div class="section-title mb-0">
                <i class="bi bi-calculator-fill text-danger"></i>
                รายละเอียดค่าใช้จ่าย
            </div>
            <a href="{{ route('expenses.index') }}" class="btn btn-danger btn-sm">
                <i class="bi bi-plus-circle me-1"></i>จัดการรายจ่าย
            </a>
        </div>
        <div class="table-responsive">
            <table class="table data-table mb-0">
                <thead>
                    <tr>
                        <th>ประเภทค่าใช้จ่าย</th>
                        <th class="text-end">จำนวนเงิน</th>
                        <th class="text-end">% ของรายได้</th>
                        <th class="text-end">% ของค่าใช้จ่าย</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><i class="bi bi-box-seam text-warning me-2"></i>เบิกจ่ายสต็อก/วัสดุ</td>
                        <td class="text-end">฿{{ number_format($stockExpenses, 0) }}</td>
                        <td class="text-end">{{ $totalRevenue > 0 ? number_format(($stockExpenses / $totalRevenue) * 100, 1) : 0 }}%</td>
                        <td class="text-end">{{ $totalExpenses > 0 ? number_format(($stockExpenses / $totalExpenses) * 100, 1) : 0 }}%</td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-tools text-info me-2"></i>ซ่อมบำรุงอุปกรณ์</td>
                        <td class="text-end">฿{{ number_format($maintenanceExpenses, 0) }}</td>
                        <td class="text-end">{{ $totalRevenue > 0 ? number_format(($maintenanceExpenses / $totalRevenue) * 100, 1) : 0 }}%</td>
                        <td class="text-end">{{ $totalExpenses > 0 ? number_format(($maintenanceExpenses / $totalExpenses) * 100, 1) : 0 }}%</td>
                    </tr>
                    <tr>
                        <td><i class="bi bi-receipt text-danger me-2"></i>ค่าใช้จ่ายสาขา</td>
                        <td class="text-end">฿{{ number_format($branchExpensesTotal ?? 0, 0) }}</td>
                        <td class="text-end">{{ $totalRevenue > 0 ? number_format((($branchExpensesTotal ?? 0) / $totalRevenue) * 100, 1) : 0 }}%</td>
                        <td class="text-end">{{ $totalExpenses > 0 ? number_format((($branchExpensesTotal ?? 0) / $totalExpenses) * 100, 1) : 0 }}%</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr>
                        <td>รวมค่าใช้จ่ายทั้งหมด</td>
                        <td class="text-end">฿{{ number_format($totalExpenses, 0) }}</td>
                        <td class="text-end">{{ $totalRevenue > 0 ? number_format(($totalExpenses / $totalRevenue) * 100, 1) : 0 }}%</td>
                        <td class="text-end">100%</td>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>

    <!-- Branch P&L Table -->
    <div class="section-card">
        <div class="section-title">
            <i class="bi bi-building text-primary"></i>
            กำไรขาดทุนแยกตามสาขา
        </div>
        <div class="table-responsive">
            <table class="table data-table mb-0">
                <thead>
                    <tr>
                        <th>สาขา</th>
                        <th class="text-end">รายได้</th>
                        <th class="text-end">ค่าใช้จ่าย</th>
                        <th class="text-end">กำไรสุทธิ</th>
                        <th class="text-end">อัตรากำไร</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($branchPL as $item)
                    <tr>
                        <td>
                            <i class="bi bi-building me-2 text-primary"></i>
                            <strong>{{ $item['branch']->name }}</strong>
                        </td>
                        <td class="text-end">฿{{ number_format($item['revenue'], 0) }}</td>
                        <td class="text-end">฿{{ number_format($item['expenses'], 0) }}</td>
                        <td class="text-end fw-bold {{ $item['net_profit'] >= 0 ? 'text-success' : 'text-danger' }}">
                            ฿{{ number_format($item['net_profit'], 0) }}
                        </td>
                        <td class="text-end">
                            @if($item['revenue'] > 0)
                                {{ number_format(($item['net_profit'] / $item['revenue']) * 100, 1) }}%
                            @else
                                0%
                            @endif
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            <i class="bi bi-inbox fs-2 d-block mb-2"></i>
                            ไม่พบข้อมูลในช่วงเวลาที่เลือก
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                @if(count($branchPL) > 0)
                <tfoot>
                    <tr>
                        <td><strong>รวมทั้งหมด</strong></td>
                        <td class="text-end">฿{{ number_format($totalRevenue, 0) }}</td>
                        <td class="text-end">฿{{ number_format($totalExpenses, 0) }}</td>
                        <td class="text-end {{ $netProfit >= 0 ? 'text-success' : 'text-danger' }}">
                            ฿{{ number_format($netProfit, 0) }}
                        </td>
                        <td class="text-end">{{ number_format($profitMargin, 1) }}%</td>
                    </tr>
                </tfoot>
                @endif
            </table>
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
// Revenue by Type Pie Chart
const revenueTypeData = {!! json_encode($revenueByType) !!};
const labels = revenueTypeData.map(item => {
    const type = item.item_type || 'Other';
    return type.charAt(0).toUpperCase() + type.slice(1);
});
const data = revenueTypeData.map(item => item.revenue);
const backgroundColors = ['#3b82f6', '#f59e0b', '#10b981', '#8b5cf6', '#ec4899'];

new Chart(document.getElementById('revenueTypeChart'), {
    type: 'doughnut',
    data: {
        labels: labels,
        datasets: [{
            data: data,
            backgroundColor: backgroundColors.slice(0, labels.length),
            borderWidth: 0
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom',
                labels: { padding: 15, font: { size: 12 } }
            },
            tooltip: {
                callbacks: {
                    label: function(context) {
                        const value = context.parsed;
                        const total = context.dataset.data.reduce((a, b) => a + b, 0);
                        const percentage = ((value / total) * 100).toFixed(1);
                        return context.label + ': ฿' + value.toLocaleString() + ' (' + percentage + '%)';
                    }
                }
            }
        }
    }
});
</script>
@endpush
