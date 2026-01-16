@extends('layouts.app')

@section('title', 'เพิ่มอุปกรณ์ใหม่ - GCMS')

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
                <h2 class="mb-2"><i class="bi bi-plus-circle me-2"></i>เพิ่มอุปกรณ์ใหม่</h2>
                <p class="mb-0 opacity-90">ลงทะเบียนอุปกรณ์ใหม่เข้าระบบ</p>
            </div>
            <a href="{{ route('equipment.index') }}" class="btn btn-light">
                <i class="bi bi-arrow-left me-1"></i> กลับ
            </a>
        </div>
    </div>

    <!-- Form -->
    <div class="form-card">
        <form method="POST" action="{{ route('equipment.store') }}" id="equipmentForm">
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
                <div class="col-md-4">
                    <label class="form-label">รหัสอุปกรณ์ <span class="text-danger">*</span></label>
                    <input type="text" name="equipment_code" class="form-control" value="{{ old('equipment_code') }}" required placeholder="เช่น EQP-001">
                </div>
                <div class="col-md-8">
                    <label class="form-label">ชื่ออุปกรณ์ <span class="text-danger">*</span></label>
                    <input type="text" name="name" class="form-control" value="{{ old('name') }}" required placeholder="ชื่ออุปกรณ์">
                </div>
                <div class="col-12">
                    <label class="form-label">รายละเอียด</label>
                    <textarea name="description" class="form-control" rows="2" placeholder="รายละเอียดเพิ่มเติม...">{{ old('description') }}</textarea>
                </div>
                <div class="col-md-4">
                    <label class="form-label">หมวดหมู่ <span class="text-danger">*</span></label>
                    <select name="category" class="form-select" required>
                        <option value="">เลือกหมวด</option>
                        <option value="treatment_equipment" {{ old('category') == 'treatment_equipment' ? 'selected' : '' }}>อุปกรณ์รักษา</option>
                        <option value="office_equipment" {{ old('category') == 'office_equipment' ? 'selected' : '' }}>อุปกรณ์สำนักงาน</option>
                        <option value="furniture" {{ old('category') == 'furniture' ? 'selected' : '' }}>เฟอร์นิเจอร์</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label class="form-label">สาขา <span class="text-danger">*</span></label>
                    <input type="text" class="form-control" value="{{ auth()->user()->branch->name ?? 'ไม่ระบุสาขา' }}" disabled>
                    <input type="hidden" name="branch_id" value="{{ auth()->user()->branch_id }}" required>
                </div>
                <div class="col-md-4">
                    <label class="form-label">Serial Number</label>
                    <input type="text" name="serial_number" class="form-control" value="{{ old('serial_number') }}" placeholder="S/N">
                </div>
                <div class="col-md-4">
                    <label class="form-label">วันที่ซื้อ</label>
                    <input type="date" name="purchase_date" class="form-control" value="{{ old('purchase_date') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">ราคาซื้อ</label>
                    <div class="input-group">
                        <span class="input-group-text">฿</span>
                        <input type="number" name="purchase_price" class="form-control" value="{{ old('purchase_price') }}" min="0" step="0.01">
                    </div>
                </div>
                <div class="col-md-4">
                    <label class="form-label">ผู้จัดจำหน่าย</label>
                    <input type="text" name="supplier" class="form-control" value="{{ old('supplier') }}" placeholder="ชื่อผู้จัดจำหน่าย">
                </div>
                <div class="col-md-4">
                    <label class="form-label">เลขที่รับประกัน</label>
                    <input type="text" name="warranty_number" class="form-control" value="{{ old('warranty_number') }}" placeholder="Warranty No.">
                </div>
                <div class="col-md-4">
                    <label class="form-label">วันหมดประกัน</label>
                    <input type="date" name="warranty_expiry" class="form-control" value="{{ old('warranty_expiry') }}">
                </div>
                <div class="col-md-4">
                    <label class="form-label">รอบซ่อมบำรุง (วัน)</label>
                    <input type="number" name="maintenance_interval_days" class="form-control" value="{{ old('maintenance_interval_days') }}" min="1" placeholder="เช่น 90">
                    <small class="text-muted">กำหนดรอบซ่อมบำรุงอัตโนมัติ</small>
                </div>
                <div class="col-12">
                    <label class="form-label">หมายเหตุ</label>
                    <textarea name="notes" class="form-control" rows="2" placeholder="หมายเหตุเพิ่มเติม...">{{ old('notes') }}</textarea>
                </div>
            </div>

            <hr class="my-4">

            <div class="d-flex justify-content-end gap-2">
                <a href="{{ route('equipment.index') }}" class="btn btn-secondary">ยกเลิก</a>
                <button type="submit" class="btn btn-primary">
                    <i class="bi bi-check-lg me-1"></i> บันทึก
                </button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.getElementById('equipmentForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const form = this;
    const formData = new FormData(form);

    fetch(form.action, {
        method: 'POST',
        body: formData,
        headers: {
            'X-Requested-With': 'XMLHttpRequest',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('บันทึกอุปกรณ์สำเร็จ');
            window.location.href = '{{ route('equipment.index') }}';
        } else {
            alert('เกิดข้อผิดพลาด: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('เกิดข้อผิดพลาดในการบันทึก');
    });
});
</script>
@endpush
