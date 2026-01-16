@extends('layouts.app')

@section('title', 'ประวัติการเคลื่อนไหวสต็อก - GCMS')

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #0891b2, #06b6d4);
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        color: white;
        margin-bottom: 1.5rem;
    }
    .transaction-in { color: #059669; }
    .transaction-out { color: #dc2626; }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1"><i class="bi bi-clock-history me-2"></i>ประวัติการเคลื่อนไหวสต็อก</h4>
            <p class="mb-0 opacity-75">ดูประวัติการนำเข้า-ออกสินค้า</p>
        </div>
        <a href="{{ route('stock.index') }}" class="btn btn-light">
            <i class="bi bi-arrow-left me-1"></i>กลับ
        </a>
    </div>

    <!-- Filters -->
    <div class="card mb-4">
        <div class="card-body py-3">
            <form class="row g-2 align-items-center">
                <div class="col-auto">
                    <select name="stock_id" class="form-select form-select-sm">
                        <option value="">สินค้าทั้งหมด</option>
                        @foreach($stocks ?? [] as $stock)
                        <option value="{{ $stock->id }}" {{ request('stock_id') == $stock->id ? 'selected' : '' }}>
                            {{ $stock->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <select name="type" class="form-select form-select-sm">
                        <option value="">ทุกประเภท</option>
                        <option value="in" {{ request('type') == 'in' ? 'selected' : '' }}>นำเข้า</option>
                        <option value="out" {{ request('type') == 'out' ? 'selected' : '' }}>นำออก</option>
                    </select>
                </div>
                <div class="col-auto">
                    <input type="date" name="date_from" class="form-control form-control-sm"
                           value="{{ request('date_from') }}" placeholder="จากวันที่">
                </div>
                <div class="col-auto">
                    <input type="date" name="date_to" class="form-control form-control-sm"
                           value="{{ request('date_to') }}" placeholder="ถึงวันที่">
                </div>
                <div class="col-auto">
                    <select name="branch_id" class="form-select form-select-sm">
                        <option value="">ทุกสาขา</option>
                        @foreach($branches ?? [] as $branch)
                        <option value="{{ $branch->id }}" {{ request('branch_id') == $branch->id ? 'selected' : '' }}>
                            {{ $branch->name }}
                        </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-auto">
                    <button type="submit" class="btn btn-sm btn-primary">
                        <i class="bi bi-search me-1"></i>ค้นหา
                    </button>
                    <a href="{{ route('stock.transactions') }}" class="btn btn-sm btn-secondary">ล้าง</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Transactions Table -->
    <div class="card">
        <div class="card-header">
            <i class="bi bi-list-ul me-2"></i>รายการเคลื่อนไหว
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead>
                        <tr>
                            <th>วันที่/เวลา</th>
                            <th>สินค้า</th>
                            <th>ประเภท</th>
                            <th class="text-end">จำนวน</th>
                            <th class="text-end">คงเหลือ</th>
                            <th>หมายเหตุ</th>
                            <th>ผู้ดำเนินการ</th>
                            <th>สาขา</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($transactions ?? [] as $transaction)
                        <tr>
                            <td>
                                <div>{{ $transaction->created_at->format('d/m/Y') }}</div>
                                <small class="text-muted">{{ $transaction->created_at->format('H:i') }}</small>
                            </td>
                            <td>
                                <a href="{{ route('stock.show', $transaction->stock_id) }}">
                                    {{ $transaction->stock->name ?? '-' }}
                                </a>
                            </td>
                            <td>
                                <span class="badge {{ $transaction->type === 'in' ? 'bg-success' : 'bg-danger' }}">
                                    {{ $transaction->type === 'in' ? 'นำเข้า' : 'นำออก' }}
                                </span>
                            </td>
                            <td class="text-end fw-bold {{ $transaction->type === 'in' ? 'transaction-in' : 'transaction-out' }}">
                                {{ $transaction->type === 'in' ? '+' : '-' }}{{ number_format($transaction->quantity) }}
                            </td>
                            <td class="text-end">{{ number_format($transaction->balance_after ?? 0) }}</td>
                            <td>{{ $transaction->notes ?? '-' }}</td>
                            <td>{{ $transaction->user->name ?? '-' }}</td>
                            <td>{{ $transaction->branch->name ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="8" class="text-center text-muted py-4">
                                <i class="bi bi-inbox display-6 d-block mb-2"></i>
                                ไม่พบรายการเคลื่อนไหว
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if(isset($transactions) && method_exists($transactions, 'links'))
        <div class="card-footer">
            {{ $transactions->withQueryString()->links() }}
        </div>
        @endif
    </div>
</div>
@endsection
