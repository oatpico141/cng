@extends('layouts.app')

@section('title', 'แก้ไขสาขา - GCMS')

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
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
            <h4 class="mb-1"><i class="bi bi-pencil me-2"></i>แก้ไขสาขา</h4>
            <p class="mb-0 opacity-75">{{ $branch->name }}</p>
        </div>
        <a href="{{ route('branches.index') }}" class="btn btn-light">
            <i class="bi bi-arrow-left me-1"></i>กลับ
        </a>
    </div>

    <div class="card">
        <div class="card-body">
            <form action="{{ route('branches.update', $branch->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ชื่อสาขา <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $branch->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">รหัสสาขา</label>
                        <input type="text" name="code" class="form-control @error('code') is-invalid @enderror"
                               value="{{ old('code', $branch->code) }}">
                        @error('code')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">เบอร์โทรศัพท์</label>
                        <input type="text" name="phone" class="form-control"
                               value="{{ old('phone', $branch->phone) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">อีเมล</label>
                        <input type="email" name="email" class="form-control"
                               value="{{ old('email', $branch->email) }}">
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">ที่อยู่</label>
                        <textarea name="address" class="form-control" rows="3">{{ old('address', $branch->address) }}</textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">เวลาเปิด</label>
                        <input type="time" name="open_time" class="form-control"
                               value="{{ old('open_time', $branch->open_time) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">เวลาปิด</label>
                        <input type="time" name="close_time" class="form-control"
                               value="{{ old('close_time', $branch->close_time) }}">
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">สถานะ</label>
                        <select name="is_active" class="form-select">
                            <option value="1" {{ old('is_active', $branch->is_active) ? 'selected' : '' }}>เปิดใช้งาน</option>
                            <option value="0" {{ !old('is_active', $branch->is_active) ? 'selected' : '' }}>ปิดใช้งาน</option>
                        </select>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">หมายเหตุ</label>
                        <textarea name="notes" class="form-control" rows="2">{{ old('notes', $branch->notes) }}</textarea>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                        <i class="bi bi-trash me-1"></i>ลบสาขา
                    </button>
                    <div>
                        <a href="{{ route('branches.index') }}" class="btn btn-secondary me-2">ยกเลิก</a>
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
<form id="deleteForm" action="{{ route('branches.destroy', $branch->id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    function confirmDelete() {
        if (confirm('ต้องการลบสาขานี้หรือไม่? การดำเนินการนี้ไม่สามารถยกเลิกได้')) {
            document.getElementById('deleteForm').submit();
        }
    }
</script>
@endpush
