@extends('layouts.app')

@section('title', 'รายละเอียดสินค้า - GCMS')

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #0891b2, #06b6d4);
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        color: white;
        margin-bottom: 1.5rem;
    }
    .stock-status {
        padding: 0.5rem 1rem;
        border-radius: 8px;
        font-weight: 600;
    }
    .stock-status.in-stock { background: #d1fae5; color: #065f46; }
    .stock-status.low-stock { background: #fef3c7; color: #92400e; }
    .stock-status.out-of-stock { background: #fee2e2; color: #991b1b; }
    .info-row {
        padding: 0.75rem 0;
        border-bottom: 1px solid #f1f5f9;
    }
    .info-row:last-child { border-bottom: none; }
    .info-label { color: #64748b; font-size: 0.875rem; }
    .info-value { color: #1e293b; font-weight: 500; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1"><i class="bi bi-box-seam me-2"></i>{{ $stock->name }}</h4>
            <p class="mb-0 opacity-75">รหัส: {{ $stock->sku ?? $stock->id }}</p>
        </div>
        <div>
            <a href="{{ route('stock.edit', $stock->id) }}" class="btn btn-warning me-2">
                <i class="bi bi-pencil me-1"></i>แก้ไข
            </a>
            <a href="{{ route('stock.index') }}" class="btn btn-light">
                <i class="bi bi-arrow-left me-1"></i>กลับ
            </a>
        </div>
    </div>

    <div class="row">
        <!-- Stock Info -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-info-circle me-2"></i>ข้อมูลสินค้า
                </div>
                <div class="card-body">
                    <div class="info-row d-flex justify-content-between">
                        <span class="info-label">ชื่อสินค้า</span>
                        <span class="info-value">{{ $stock->name }}</span>
                    </div>
                    <div class="info-row d-flex justify-content-between">
                        <span class="info-label">รหัสสินค้า (SKU)</span>
                        <span class="info-value">{{ $stock->sku ?? '-' }}</span>
                    </div>
                    <div class="info-row d-flex justify-content-between">
                        <span class="info-label">หมวดหมู่</span>
                        <span class="info-value">{{ $stock->category ?? '-' }}</span>
                    </div>
                    <div class="info-row d-flex justify-content-between">
                        <span class="info-label">หน่วย</span>
                        <span class="info-value">{{ $stock->unit ?? 'ชิ้น' }}</span>
                    </div>
                    <div class="info-row d-flex justify-content-between">
                        <span class="info-label">ราคาต้นทุน</span>
                        <span class="info-value">{{ number_format($stock->cost_price ?? 0, 2) }} บาท</span>
                    </div>
                    <div class="info-row d-flex justify-content-between">
                        <span class="info-label">ราคาขาย</span>
                        <span class="info-value">{{ number_format($stock->selling_price ?? 0, 2) }} บาท</span>
                    </div>
                    <div class="info-row d-flex justify-content-between">
                        <span class="info-label">สาขา</span>
                        <span class="info-value">{{ $stock->branch->name ?? 'ทุกสาขา' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Stock Quantity -->
        <div class="col-lg-6 mb-4">
            <div class="card">
                <div class="card-header">
                    <i class="bi bi-box me-2"></i>จำนวนคงเหลือ
                </div>
                <div class="card-body text-center">
                    @php
                        $quantity = $stock->quantity ?? 0;
                        $minStock = $stock->min_stock ?? 10;
                        $status = $quantity <= 0 ? 'out-of-stock' : ($quantity <= $minStock ? 'low-stock' : 'in-stock');
                    @endphp
                    <div class="display-3 fw-bold mb-3 {{ $status === 'out-of-stock' ? 'text-danger' : ($status === 'low-stock' ? 'text-warning' : 'text-success') }}">
                        {{ number_format($quantity) }}
                    </div>
                    <div class="stock-status {{ $status }}">
                        @if($status === 'out-of-stock')
                            สินค้าหมด
                        @elseif($status === 'low-stock')
                            สินค้าใกล้หมด (ต่ำกว่า {{ $minStock }})
                        @else
                            มีสินค้า
                        @endif
                    </div>

                    <div class="mt-4">
                        <button class="btn btn-success me-2" data-bs-toggle="modal" data-bs-target="#adjustModal" data-type="add">
                            <i class="bi bi-plus-circle me-1"></i>เพิ่มสต็อก
                        </button>
                        <button class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#adjustModal" data-type="subtract">
                            <i class="bi bi-dash-circle me-1"></i>ลดสต็อก
                        </button>
                    </div>
                </div>
            </div>

            <!-- Recent Transactions -->
            <div class="card mt-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="bi bi-clock-history me-2"></i>ความเคลื่อนไหวล่าสุด</span>
                    <a href="{{ route('stock.transactions') }}?stock_id={{ $stock->id }}" class="btn btn-sm btn-outline-primary">ดูทั้งหมด</a>
                </div>
                <div class="card-body">
                    @forelse($stock->transactions ?? [] as $transaction)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>
                            <span class="badge {{ $transaction->type === 'in' ? 'bg-success' : 'bg-danger' }}">
                                {{ $transaction->type === 'in' ? '+' : '-' }}{{ $transaction->quantity }}
                            </span>
                            <span class="ms-2 small">{{ $transaction->notes ?? $transaction->type }}</span>
                        </div>
                        <small class="text-muted">{{ $transaction->created_at->diffForHumans() }}</small>
                    </div>
                    @empty
                    <p class="text-muted text-center mb-0">ยังไม่มีความเคลื่อนไหว</p>
                    @endforelse
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Adjust Stock Modal -->
<div class="modal fade" id="adjustModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <form action="{{ route('stock.adjust', $stock->id) }}" method="POST">
                @csrf
                <div class="modal-header">
                    <h5 class="modal-title"><i class="bi bi-box me-2"></i>ปรับสต็อก</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <input type="hidden" name="type" id="adjustType" value="add">
                    <div class="mb-3">
                        <label class="form-label">จำนวน</label>
                        <input type="number" name="quantity" class="form-control" min="1" required>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">หมายเหตุ</label>
                        <input type="text" name="notes" class="form-control" placeholder="เหตุผลในการปรับสต็อก">
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
    document.getElementById('adjustModal').addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;
        const type = button.getAttribute('data-type');
        document.getElementById('adjustType').value = type;
        this.querySelector('.modal-title').innerHTML = type === 'add'
            ? '<i class="bi bi-plus-circle me-2"></i>เพิ่มสต็อก'
            : '<i class="bi bi-dash-circle me-2"></i>ลดสต็อก';
    });
</script>
@endpush
