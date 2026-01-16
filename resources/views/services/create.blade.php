@extends('layouts.app')

@section('title', 'เพิ่มบริการใหม่ - GCMS')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h4 class="mb-0">
            <i class="bi bi-heart-pulse me-2 text-primary"></i>
            เพิ่มบริการใหม่
        </h4>
        <a href="{{ route('services.index') }}" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left me-1"></i> กลับ
        </a>
    </div>

    <div class="card shadow-sm border-0">
        <div class="card-body p-4">
            <form method="POST" action="{{ route('services.store') }}" autocomplete="off">
                @csrf

                <div class="row g-3">
                    <!-- Basic Information -->
                    <div class="col-12">
                        <h6 class="text-primary mb-3">
                            <i class="bi bi-info-circle me-2"></i> ข้อมูลพื้นฐาน
                        </h6>
                    </div>

                    <div class="col-md-8">
                        <label for="name" class="form-label">ชื่อบริการ <span class="text-danger">*</span></label>
                        <input type="text" class="form-control @error('name') is-invalid @enderror"
                               id="name" name="name" value="{{ old('name') }}" required autofocus>
                        @error('name')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="code" class="form-label">รหัสบริการ</label>
                        <input type="text" class="form-control @error('code') is-invalid @enderror"
                               id="code" name="code" value="{{ old('code') }}" placeholder="เช่น SVC001">
                        @error('code')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="category" class="form-label">หมวดหมู่</label>
                        <select class="form-select @error('category') is-invalid @enderror" id="category" name="category">
                            <option value="">-- เลือกหมวดหมู่ --</option>
                            <option value="facial" {{ old('category') == 'facial' ? 'selected' : '' }}>Facial Treatment</option>
                            <option value="body" {{ old('category') == 'body' ? 'selected' : '' }}>Body Treatment</option>
                            <option value="laser" {{ old('category') == 'laser' ? 'selected' : '' }}>Laser Treatment</option>
                            <option value="injection" {{ old('category') == 'injection' ? 'selected' : '' }}>Injection</option>
                            <option value="surgery" {{ old('category') == 'surgery' ? 'selected' : '' }}>Minor Surgery</option>
                            <option value="consultation" {{ old('category') == 'consultation' ? 'selected' : '' }}>Consultation</option>
                        </select>
                        @error('category')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6">
                        <label for="default_duration_minutes" class="form-label">ระยะเวลา (นาที)</label>
                        <input type="number" class="form-control @error('default_duration_minutes') is-invalid @enderror"
                               id="default_duration_minutes" name="default_duration_minutes"
                               value="{{ old('default_duration_minutes', 30) }}" min="1">
                        @error('default_duration_minutes')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12">
                        <label for="description" class="form-label">รายละเอียด</label>
                        <textarea class="form-control @error('description') is-invalid @enderror"
                                  id="description" name="description" rows="3">{{ old('description') }}</textarea>
                        @error('description')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Pricing Section -->
                    <div class="col-12 mt-4">
                        <h6 class="text-primary mb-3">
                            <i class="bi bi-cash-stack me-2"></i> ราคาและค่าคอมมิชชั่น
                        </h6>
                    </div>

                    <div class="col-md-4">
                        <label for="default_price" class="form-label">ราคามาตรฐาน (บาท) <span class="text-danger">*</span></label>
                        <input type="number" class="form-control @error('default_price') is-invalid @enderror"
                               id="default_price" name="default_price" value="{{ old('default_price') }}"
                               step="0.01" min="0" required>
                        @error('default_price')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="default_commission_rate" class="form-label">ค่าคอมมิชชั่น (%)</label>
                        <input type="number" class="form-control @error('default_commission_rate') is-invalid @enderror"
                               id="default_commission_rate" name="default_commission_rate"
                               value="{{ old('default_commission_rate') }}" step="0.01" min="0" max="100">
                        @error('default_commission_rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-4">
                        <label for="default_df_rate" class="form-label">ค่า DF (%)</label>
                        <input type="number" class="form-control @error('default_df_rate') is-invalid @enderror"
                               id="default_df_rate" name="default_df_rate"
                               value="{{ old('default_df_rate') }}" step="0.01" min="0" max="100">
                        @error('default_df_rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <!-- Package Settings -->
                    <div class="col-12 mt-4">
                        <h6 class="text-primary mb-3">
                            <i class="bi bi-box-seam me-2"></i> ตั้งค่าแพ็คเกจ
                        </h6>
                    </div>

                    <div class="col-12">
                        <div class="form-check form-switch mb-3">
                            <input class="form-check-input" type="checkbox" id="is_package" name="is_package"
                                   {{ old('is_package') ? 'checked' : '' }} onchange="togglePackageSettings()">
                            <label class="form-check-label" for="is_package">
                                บริการนี้เป็นแพ็คเกจ
                            </label>
                        </div>
                    </div>

                    <div id="packageSettings" class="row g-3" style="display: none;">
                        <div class="col-md-6">
                            <label for="package_sessions" class="form-label">จำนวนครั้ง</label>
                            <input type="number" class="form-control @error('package_sessions') is-invalid @enderror"
                                   id="package_sessions" name="package_sessions"
                                   value="{{ old('package_sessions') }}" min="1">
                            @error('package_sessions')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                        <div class="col-md-6">
                            <label for="package_validity_days" class="form-label">อายุการใช้งาน (วัน)</label>
                            <input type="number" class="form-control @error('package_validity_days') is-invalid @enderror"
                                   id="package_validity_days" name="package_validity_days"
                                   value="{{ old('package_validity_days') }}" min="1">
                            @error('package_validity_days')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="col-12 mt-4">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                            <label class="form-check-label" for="is_active">
                                เปิดใช้งานบริการนี้
                            </label>
                        </div>
                    </div>
                </div>

                <div class="d-flex justify-content-end gap-2 mt-4 pt-3 border-top">
                    <a href="{{ route('services.index') }}" class="btn btn-secondary">
                        <i class="bi bi-x-lg me-1"></i> ยกเลิก
                    </a>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-lg me-1"></i> บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function togglePackageSettings() {
        const isPackage = document.getElementById('is_package').checked;
        const packageSettings = document.getElementById('packageSettings');
        packageSettings.style.display = isPackage ? 'flex' : 'none';

        // Clear values if unchecked
        if (!isPackage) {
            document.getElementById('package_sessions').value = '';
            document.getElementById('package_validity_days').value = '';
        }
    }

    // Initialize on page load
    document.addEventListener('DOMContentLoaded', function() {
        if (document.getElementById('is_package').checked) {
            togglePackageSettings();
        }
    });
</script>
@endpush

@push('styles')
<style>
    .card {
        border-radius: 12px;
    }

    .form-label {
        font-weight: 500;
        color: #374151;
        margin-bottom: 0.5rem;
    }

    .form-control, .form-select {
        border: 2px solid #e5e7eb;
        border-radius: 8px;
        padding: 0.625rem 0.875rem;
    }

    .form-control:focus, .form-select:focus {
        border-color: #60a5fa;
        box-shadow: 0 0 0 3px rgba(96, 165, 250, 0.1);
    }

    .form-check-input:checked {
        background-color: #2563eb;
        border-color: #2563eb;
    }

    h6 {
        font-weight: 600;
        letter-spacing: -0.025em;
    }
</style>
@endpush