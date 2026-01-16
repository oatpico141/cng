@extends('layouts.app')

@section('title', 'เพิ่มแพ็คเกจคอร์ส - GCMS')

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #7c3aed, #8b5cf6);
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        color: white;
        margin-bottom: 1.5rem;
    }
    .pattern-option {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 1rem;
        cursor: pointer;
        transition: all 0.2s;
    }
    .pattern-option:hover {
        border-color: #8b5cf6;
        background: #faf5ff;
    }
    .pattern-option.selected {
        border-color: #7c3aed;
        background: #f5f3ff;
    }
    .pattern-option input {
        display: none;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1"><i class="bi bi-plus-circle me-2"></i>เพิ่มแพ็คเกจคอร์ส</h4>
            <p class="mb-0 opacity-75">สร้างแพ็คเกจคอร์สใหม่</p>
        </div>
        <a href="{{ route('course-packages.index') }}" class="btn btn-light">
            <i class="bi bi-arrow-left me-1"></i>กลับ
        </a>
    </div>

    <form action="{{ route('course-packages.store') }}" method="POST">
        @csrf

        <div class="row">
            <!-- Basic Info -->
            <div class="col-lg-8">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-info-circle me-2"></i>ข้อมูลพื้นฐาน
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-8 mb-3">
                                <label class="form-label">ชื่อแพ็คเกจ <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                                       value="{{ old('name') }}" required placeholder="เช่น Botox 5+1">
                                @error('name')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">รหัสแพ็คเกจ</label>
                                <input type="text" name="code" class="form-control"
                                       value="{{ old('code') }}" placeholder="PKG-001">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">บริการหลัก <span class="text-danger">*</span></label>
                                <select name="service_id" class="form-select @error('service_id') is-invalid @enderror" required>
                                    <option value="">-- เลือกบริการ --</option>
                                    @foreach($services ?? [] as $service)
                                    <option value="{{ $service->id }}" {{ old('service_id') == $service->id ? 'selected' : '' }}>
                                        {{ $service->name }} - {{ number_format($service->default_price) }} บาท
                                    </option>
                                    @endforeach
                                </select>
                                @error('service_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">หมวดหมู่</label>
                                <select name="category_id" class="form-select">
                                    <option value="">-- ไม่ระบุ --</option>
                                    @foreach($categories ?? [] as $category)
                                    <option value="{{ $category->id }}" {{ old('category_id') == $category->id ? 'selected' : '' }}>
                                        {{ $category->name }}
                                    </option>
                                    @endforeach
                                </select>
                            </div>

                            <div class="col-12 mb-3">
                                <label class="form-label">รายละเอียด</label>
                                <textarea name="description" class="form-control" rows="3" placeholder="รายละเอียดแพ็คเกจ...">{{ old('description') }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Sessions & Pricing -->
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-calculator me-2"></i>จำนวนครั้งและราคา
                    </div>
                    <div class="card-body">
                        <div class="row">
                            <div class="col-md-4 mb-3">
                                <label class="form-label">ครั้งที่จ่ายเงิน <span class="text-danger">*</span></label>
                                <input type="number" name="paid_sessions" class="form-control" min="1"
                                       value="{{ old('paid_sessions', 5) }}" required>
                                <small class="text-muted">จำนวนครั้งที่ลูกค้าจ่ายเงิน</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">ครั้งที่แถม</label>
                                <input type="number" name="bonus_sessions" class="form-control" min="0"
                                       value="{{ old('bonus_sessions', 1) }}">
                                <small class="text-muted">จำนวนครั้งที่แถมฟรี</small>
                            </div>

                            <div class="col-md-4 mb-3">
                                <label class="form-label">รวมทั้งหมด</label>
                                <input type="number" name="total_sessions" class="form-control" min="1"
                                       value="{{ old('total_sessions', 6) }}" readonly id="totalSessions">
                                <small class="text-muted">ครั้งที่จ่าย + ครั้งที่แถม</small>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">ราคาแพ็คเกจ (บาท) <span class="text-danger">*</span></label>
                                <input type="number" name="price" class="form-control" min="0" step="0.01"
                                       value="{{ old('price') }}" required>
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">ค่ามือ PT ต่อครั้ง (บาท)</label>
                                <input type="number" name="df_amount" class="form-control" min="0" step="0.01"
                                       value="{{ old('df_amount', 0) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">อัตราค่าคอมมิชชัน (%)</label>
                                <input type="number" name="commission_rate" class="form-control" min="0" max="100" step="0.01"
                                       value="{{ old('commission_rate', 0) }}">
                            </div>

                            <div class="col-md-6 mb-3">
                                <label class="form-label">อายุแพ็คเกจ (วัน)</label>
                                <input type="number" name="validity_days" class="form-control" min="1"
                                       value="{{ old('validity_days', 365) }}">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-gear me-2"></i>รูปแบบการซื้อที่อนุญาต
                    </div>
                    <div class="card-body">
                        <label class="pattern-option d-block mb-2 {{ old('allow_buy_and_use', true) ? 'selected' : '' }}">
                            <input type="checkbox" name="allow_buy_and_use" value="1" {{ old('allow_buy_and_use', true) ? 'checked' : '' }}>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-play-circle text-success fs-4 me-2"></i>
                                <div>
                                    <strong>ซื้อและใช้เลย</strong>
                                    <div class="small text-muted">ลูกค้าซื้อแล้วใช้ทันที</div>
                                </div>
                            </div>
                        </label>

                        <label class="pattern-option d-block mb-2 {{ old('allow_buy_for_later', true) ? 'selected' : '' }}">
                            <input type="checkbox" name="allow_buy_for_later" value="1" {{ old('allow_buy_for_later', true) ? 'checked' : '' }}>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-clock-history text-warning fs-4 me-2"></i>
                                <div>
                                    <strong>ซื้อเก็บไว้</strong>
                                    <div class="small text-muted">ซื้อเก็บไว้ใช้ทีหลัง</div>
                                </div>
                            </div>
                        </label>

                        <label class="pattern-option d-block {{ old('allow_retroactive', false) ? 'selected' : '' }}">
                            <input type="checkbox" name="allow_retroactive" value="1" {{ old('allow_retroactive', false) ? 'checked' : '' }}>
                            <div class="d-flex align-items-center">
                                <i class="bi bi-arrow-counterclockwise text-info fs-4 me-2"></i>
                                <div>
                                    <strong>ซื้อย้อนหลัง</strong>
                                    <div class="small text-muted">นับครั้งที่มาก่อนหน้า</div>
                                </div>
                            </div>
                        </label>
                    </div>
                </div>

                <div class="card mb-4">
                    <div class="card-header">
                        <i class="bi bi-toggle-on me-2"></i>สถานะ
                    </div>
                    <div class="card-body">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="isActive"
                                   value="1" {{ old('is_active', true) ? 'checked' : '' }}>
                            <label class="form-check-label" for="isActive">เปิดใช้งาน</label>
                        </div>
                    </div>
                </div>

                <div class="d-grid gap-2">
                    <button type="submit" class="btn btn-primary btn-lg">
                        <i class="bi bi-check-circle me-2"></i>บันทึกแพ็คเกจ
                    </button>
                    <a href="{{ route('course-packages.index') }}" class="btn btn-secondary">ยกเลิก</a>
                </div>
            </div>
        </div>
    </form>
</div>
@endsection

@push('scripts')
<script>
    // Calculate total sessions
    function updateTotalSessions() {
        const paid = parseInt(document.querySelector('[name="paid_sessions"]').value) || 0;
        const bonus = parseInt(document.querySelector('[name="bonus_sessions"]').value) || 0;
        document.getElementById('totalSessions').value = paid + bonus;
    }

    document.querySelector('[name="paid_sessions"]').addEventListener('input', updateTotalSessions);
    document.querySelector('[name="bonus_sessions"]').addEventListener('input', updateTotalSessions);

    // Pattern option toggle
    document.querySelectorAll('.pattern-option input').forEach(input => {
        input.addEventListener('change', function() {
            this.closest('.pattern-option').classList.toggle('selected', this.checked);
        });
    });
</script>
@endpush
