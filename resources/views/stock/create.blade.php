@extends('layouts.app')

@section('title', 'เพิ่มสินค้าใหม่ - GCMS')

@push('styles')
<style>
    .form-card {
        background: white;
        border-radius: 16px;
        padding: 2rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
    }

    .page-header {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        border-radius: 20px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2 class="mb-2"><i class="bi bi-plus-circle me-2"></i>เพิ่มสินค้าใหม่</h2>
                <p class="mb-0 opacity-90">เพิ่มสินค้าเข้าคลังสต็อก</p>
            </div>
            <a href="{{ route('stock.index') }}" class="btn btn-light">
                <i class="bi bi-arrow-left me-1"></i> กลับ
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="form-card">
        <form method="POST" action="{{ route('stock.store') }}">
            @csrf

            @if($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
            @endif

            <div class="row g-4">
                <div class="col-md-6">
                    <label class="form-label">รหัสสินค้า <span class="text-danger">*</span></label>
                    <input type="text" name="item_code" class="form-control" value="{{ old('item_code') }}" required placeholder="เช่น MED-001">
                </div>
                <div class="col-md-6">
                    <label class="form-label">ชื่อสินค้า <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="ชื่อสินค้า">
                </div>
                <div class="col-12">
                    <label class="form-label">รายละเอียด</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="รายละเอียดเพิ่มเติม...">{{ old('description') }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">หมวดหมู่ <span class="text-danger">*</span></label>
                    <input type="text" name="category" class="form-control" value="{{ old('category') }}" required placeholder="เช่น วัสดุสิ้นเปลือง" list="categoryList">
                    <datalist id="categoryList">
                        <option value="วัสดุสิ้นเปลือง">
                        <option value="อุปกรณ์การแพทย์">
                        <option value="ยา">
                        <option value="เวชภัณฑ์">
                    </datalist>
                </div>
                <div class="col-md-4">
                    <label class="form-label">หน่วยนับ <span class="text-danger">*</span></label>
                    <input type="text" name="unit" class="form-control" value="{{ old('unit') }}" required placeholder="เช่น ชิ้น, กล่อง" list="unitList">
                    <datalist id="unitList">
                        <option value="ชิ้น">
                        <option value="กล่อง">
                        <option value="ขวด">
                        <option value="แพ็ค">
                        <option value="ม้วน">
                    </datalist>
                </div>
                <div class="col-md-4">
                    <label class="form-label">สาขา <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" value="{{ auth()->user()->branch->name ?? 'ไม่ระบุสาขา' }}" disabled>
                    <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id }}" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">จำนวนเริ่มต้น <span class="text-danger">*</span></label>
                    <input type="number" name="quantity_on_hand" class="form-control" value="{{ old('quantity_on_hand', 0) }}" required min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label">จำนวนขั้นต่ำ <span class="text-danger">*</span></label>
                    <input type="number" name="minimum_quantity" class="form-control" value="{{ old('minimum_quantity', 0) }}" required min="0">
                    <small class="text-muted">แจ้งเตือนเมื่อต่ำกว่านี้</small>
                </div>
                <div class="col-md-3">
                    <label class="form-label">จำนวนสูงสุด</label>
                    <input type="number" name="maximum_quantity" class="form-control" value="{{ old('maximum_quantity') }}" min="0">
                </div>
                <div class="col-md-3">
                    <label class="form-label">ต้นทุน/หน่วย <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text">฿</span>
                        <input type="number" name="unit_cost" class="form-control" value="{{ old('unit_cost', 0) }}" required min="0" step="0.01">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">ราคาขาย/หน่วย</label>
                    <div class="input-group">
                        <span class="input-group-text">฿</span>
                        <input type="number" name="unit_price" class="form-control" value="{{ old('unit_price') }}" min="0" step="0.01">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">ผู้จัดจำหน่าย</label>
                    <input type="text" name="supplier" class="form-control" value="{{ old('supplier') }}" placeholder="ชื่อผู้จัดจำหน่าย">
                </div>
                <div class="col-md-4">
                    <label class="form-label">หมายเหตุ</label>
                    <input type="text" name="notes" class="form-control" value="{{ old('notes') }}" placeholder="หมายเหตุเพิ่มเติม">
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('stock.index') }}" class="btn btn-secondary">ยกเลิก</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> บันทึก
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
