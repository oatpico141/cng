@extends('layouts.app')

@section('title', 'จัดการสต็อก - GCMS')

@push('styles')
<style>
    .stock-header {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        border-radius: 20px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        height: 100%;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .stat-icon.items { background: #e0f2fe; color: #0284c7; }
    .stat-icon.low { background: #fee2e2; color: #dc2626; }
    .stat-icon.value { background: #d1fae5; color: #059669; }

    .filter-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        margin-bottom: 1.5rem;
    }

    .stock-table-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    }

    .stock-table thead th {
        background: #f8fafc;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        padding: 1rem;
        border: none;
    }

    .stock-table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }

    .stock-table tbody tr:hover {
        background: #f8fafc;
    }

    .modal-content {
        border-radius: 16px;
        border: none;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="stock-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-2"><i class="bi bi-box-seam me-2"></i>จัดการสต็อก</h2>
                <p class="mb-0 opacity-90">จัดการวัสดุสิ้นเปลืองและอุปกรณ์</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <a href="{{ route('stock.create') }}" class="btn btn-light">
                    <i class="bi bi-plus-lg me-1"></i> เพิ่มสินค้า
                </a>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="stat-icon items me-3">
                        <i class="bi bi-box"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold">{{ number_format($totalItems) }}</div>
                        <div class="text-muted small">รายการสินค้า</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="stat-icon low me-3">
                        <i class="bi bi-exclamation-triangle"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold text-danger">{{ number_format($lowStockCount) }}</div>
                        <div class="text-muted small">สต็อกต่ำ</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="stat-icon value me-3">
                        <i class="bi bi-currency-exchange"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold">฿{{ number_format($totalValue, 0) }}</div>
                        <div class="text-muted small">มูลค่าสต็อก</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alerts -->
    @if($lowStockCount > 0)
    <div class="alert alert-warning alert-dismissible fade show mb-4" role="alert">
        <i class="bi bi-exclamation-triangle me-2"></i>
        <strong>แจ้งเตือน!</strong> มีสินค้า {{ $lowStockCount }} รายการที่สต็อกต่ำกว่าขั้นต่ำ
        <a href="{{ route('stock.index', ['low_stock' => 1]) }}" class="alert-link">ดูรายการ</a>
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
        {{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filters -->
    <div class="filter-card">
        <form method="GET" action="{{ route('stock.index') }}">
            <div class="row g-3">
                <div class="col-md-3">
                    <input type="text" name="search" class="form-control" placeholder="ค้นหาชื่อ/รหัส..." value="{{ request('search') }}">
                    <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id }}">
                </div>
                <div class="col-md-2">
                    <select name="category" class="form-select">
                        <option value="">ทุกหมวด</option>
                        @foreach($categories as $category)
                            <option value="{{ $category }}" {{ request('category') == $category ? 'selected' : '' }}>
                                {{ $category }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-2">
                    <div class="form-check mt-2">
                        <input class="form-check-input" type="checkbox" name="low_stock" value="1" id="lowStock" {{ request('low_stock') ? 'checked' : '' }}>
                        <label class="form-check-label" for="lowStock">สต็อกต่ำ</label>
                    </div>
                </div>
                <div class="col-md-3">
                    <button type="submit" class="btn btn-primary me-2">
                        <i class="bi bi-search me-1"></i> ค้นหา
                    </button>
                    <a href="{{ route('stock.index') }}" class="btn btn-outline-secondary">รีเซ็ต</a>
                </div>
            </div>
        </form>
    </div>

    <!-- Stock Table -->
    <div class="stock-table-card">
        <div class="table-responsive">
            <table class="table stock-table mb-0">
                <thead>
                    <tr>
                        <th>รหัส</th>
                        <th>ชื่อสินค้า</th>
                        <th>หมวด</th>
                        <th>สาขา</th>
                        <th class="text-center">คงเหลือ</th>
                        <th class="text-center">ขั้นต่ำ</th>
                        <th class="text-end">ต้นทุน/หน่วย</th>
                        <th class="text-center">สถานะ</th>
                        <th class="text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($stockItems as $item)
                    <tr class="{{ $item->isLowStock() ? 'table-warning' : '' }}">
                        <td><code>{{ $item->item_code }}</code></td>
                        <td>
                            <strong>{{ $item->name }}</strong>
                            @if($item->description)
                                <br><small class="text-muted">{{ Str::limit($item->description, 30) }}</small>
                            @endif
                        </td>
                        <td>{{ $item->category }}</td>
                        <td>{{ $item->branch->name ?? '-' }}</td>
                        <td class="text-center fw-bold {{ $item->isLowStock() ? 'text-danger' : '' }}">
                            {{ number_format($item->quantity_on_hand) }} {{ $item->unit }}
                        </td>
                        <td class="text-center">{{ number_format($item->minimum_quantity) }}</td>
                        <td class="text-end">฿{{ number_format($item->unit_cost, 2) }}</td>
                        <td class="text-center">
                            @if($item->isLowStock())
                                <span class="badge bg-warning">สต็อกต่ำ</span>
                            @else
                                <span class="badge bg-success">ปกติ</span>
                            @endif
                        </td>
                        <td class="text-center">
                            <button class="btn btn-sm btn-outline-primary me-1"
                                    onclick="openAdjustModal('{{ $item->id }}', '{{ $item->name }}', {{ $item->quantity_on_hand }})"
                                    title="ปรับสต็อก">
                                <i class="bi bi-plus-slash-minus"></i>
                            </button>
                            <a href="{{ route('stock.edit', $item) }}" class="btn btn-sm btn-outline-secondary" title="แก้ไข">
                                <i class="bi bi-pencil"></i>
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="9" class="text-center py-5 text-muted">
                            <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                            ไม่พบข้อมูลสินค้า
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Pagination -->
        @if($stockItems->hasPages())
        <div class="p-3 border-top">
            {{ $stockItems->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Adjust Stock Modal -->
<div class="modal fade" id="adjustModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-plus-slash-minus me-2"></i>ปรับปรุงสต็อก</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="adjustForm" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">สินค้า</label>
                        <div id="adjustItemName" class="form-control-plaintext"></div>
                        <small class="text-muted">คงเหลือปัจจุบัน: <span id="adjustCurrentQty" class="fw-bold"></span></small>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">ประเภทรายการ</label>
                        <select name="transaction_type" class="form-select" required>
                            <option value="in">รับเข้า (เพิ่มสต็อก)</option>
                            <option value="out">เบิกออก (ลดสต็อก)</option>
                            <option value="adjust">ปรับยอด (ตั้งยอดใหม่)</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">จำนวน</label>
                        <input type="number" name="quantity" class="form-control" required min="1">
                    </div>
                    <div class="mb-3">
                        <label class="form-label">หมายเหตุ</label>
                        <textarea name="description" class="form-control" rows="2" required placeholder="ระบุเหตุผล..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
function openAdjustModal(itemId, itemName, currentQty) {
    document.getElementById('adjustItemName').textContent = itemName;
    document.getElementById('adjustCurrentQty').textContent = currentQty;
    document.getElementById('adjustForm').action = '/stock/' + itemId + '/adjust';
    new bootstrap.Modal(document.getElementById('adjustModal')).show();
}
</script>
@endpush
