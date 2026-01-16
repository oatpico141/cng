@extends('layouts.app')

@section('title', 'แก้ไขสินค้า - GCMS')

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #0891b2, #06b6d4);
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        color: white;
        margin-bottom: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1"><i class="bi bi-pencil me-2"></i>แก้ไขสินค้า</h4>
            <p class="mb-0 opacity-75">{{ $stock->name }}</p>
        </div>
        <a href="{{ route('stock.index') }}" class="btn btn-light">
            <i class="bi bi-arrow-left me-1"></i>กลับ
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('stock.update', $stock->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ชื่อสินค้า <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $stock->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">รหัสสินค้า (SKU)</label>
                        <input type="text" name="sku" class="form-control @error('sku') is-invalid @enderror"
                               value="{{ old('sku', $stock->sku) }}">
                        @error('sku')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">หมวดหมู่</label>
                        <input type="text" name="category" class="form-control"
                               value="{{ old('category', $stock->category) }}" list="categoryList">
                        <datalist id="categoryList">
                            <option value="วัสดุสิ้นเปลือง">
                            <option value="อุปกรณ์การแพทย์">
                            <option value="เวชภัณฑ์">
                            <option value="ผลิตภัณฑ์ขาย">
                            <option value="อื่นๆ">
                        </datalist>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">หน่วย</label>
                        <input type="text" name="unit" class="form-control"
                               value="{{ old('unit', $stock->unit ?? 'ชิ้น') }}" list="unitList">
                        <datalist id="unitList">
                            <option value="ชิ้น">
                            <option value="กล่อง">
                            <option value="แพ็ค">
                            <option value="ขวด">
                            <option value="หลอด">
                            <option value="ซอง">
                        </datalist>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">ราคาต้นทุน (บาท)</label>
                        <input type="number" name="cost_price" class="form-control" step="0.01" min="0"
                               value="{{ old('cost_price', $stock->cost_price) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">ราคาขาย (บาท)</label>
                        <input type="number" name="selling_price" class="form-control" step="0.01" min="0"
                               value="{{ old('selling_price', $stock->selling_price) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">จำนวนขั้นต่ำ (แจ้งเตือน)</label>
                        <input type="number" name="min_stock" class="form-control" min="0"
                               value="{{ old('min_stock', $stock->min_stock ?? 10) }}">
                        <small class="text-muted">ระบบจะแจ้งเตือนเมื่อสินค้าต่ำกว่าจำนวนนี้</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">สาขา</label>
                        <select name="branch_id" class="form-select">
                            <option value="">ทุกสาขา</option>
                            @foreach($branches ?? [] as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id', $stock->branch_id) == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">รายละเอียด</label>
                        <textarea name="description" class="form-control" rows="3">{{ old('description', $stock->description) }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                        <i class="bi bi-trash me-1"></i>ลบสินค้า
                    </button>
                    <div>
                        <a href="{{ route('stock.index') }}" class="btn btn-secondary me-2">ยกเลิก</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>บันทึก
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" action="{{ route('stock.destroy', $stock->id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    function confirmDelete() {
        if (confirm('ต้องการลบสินค้านี้หรือไม่? การดำเนินการนี้ไม่สามารถยกเลิกได้')) {
            document.getElementById('deleteForm').submit();
        }
    }
</script>
@endpush
