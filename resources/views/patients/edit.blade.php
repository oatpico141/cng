@extends('layouts.app')

@section('title', 'แก้ไขข้อมูลลูกค้า - GCMS')

@push('styles')
<style>
    /* Modern Clean Design */
    .bg-gradient-primary {
        background: linear-gradient(135deg, #0284c7, #0ea5e9);
    }

    .required::after {
        content: " *";
        color: #ef4444;
    }

    .form-label {
        font-weight: 500;
        color: #475569;
        margin-bottom: 0.5rem;
        font-size: 0.85rem;
    }

    /* Clean Inputs */
    .form-control,
    .form-select {
        padding: 0.625rem 0.875rem;
        font-size: 0.875rem;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        transition: all 0.2s;
        background: #ffffff;
    }

    .form-control:focus,
    .form-select:focus {
        border-color: #0ea5e9;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
    }

    textarea.form-control {
        min-height: 80px;
    }

    .avatar-preview {
        width: 100px;
        height: 100px;
        border-radius: 50%;
        border: 3px solid #e2e8f0;
        display: flex;
        align-items: center;
        justify-content: center;
        background: linear-gradient(135deg, #f0f9ff 0%, #e0f2fe 100%);
        font-size: 2.5rem;
        color: #0284c7;
    }

    /* Gender Radio Buttons */
    .form-check-input {
        width: 1.125rem;
        height: 1.125rem;
        margin-top: 0.1rem;
    }

    .form-check-label {
        font-size: 0.875rem;
        padding-left: 0.375rem;
    }

    /* Card Styling */
    .card {
        border: 1px solid #e2e8f0;
        border-radius: 12px;
        box-shadow: 0 1px 3px rgba(0, 0, 0, 0.05);
    }

    .card-header {
        padding: 1rem 1.25rem;
        border-radius: 12px 12px 0 0 !important;
        background: #f8fafc;
        border-bottom: 1px solid #e2e8f0;
    }

    .card-header h5,
    .card-header h6 {
        font-size: 0.9rem;
        font-weight: 600;
        color: #334155;
        margin: 0;
    }

    .card-body {
        padding: 1.25rem;
    }

    /* Section Icons */
    .section-icon {
        width: 32px;
        height: 32px;
        border-radius: 8px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.875rem;
        margin-right: 0.75rem;
    }

    .section-icon.blue {
        background: #e0f2fe;
        color: #0284c7;
    }

    .section-icon.green {
        background: #dcfce7;
        color: #16a34a;
    }

    .section-icon.purple {
        background: #f3e8ff;
        color: #9333ea;
    }

    /* Buttons */
    .btn-primary {
        background: linear-gradient(135deg, #0284c7, #0ea5e9);
        border: none;
        font-weight: 500;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #0369a1, #0284c7);
        transform: translateY(-1px);
    }

    .btn {
        border-radius: 8px;
        padding: 0.5rem 1rem;
        font-size: 0.875rem;
        transition: all 0.2s;
    }

    .btn-lg {
        padding: 0.75rem 1.25rem;
        font-size: 0.95rem;
    }

    /* HN Card */
    .hn-card {
        background: linear-gradient(135deg, #f0f9ff, #e0f2fe);
        border: 2px solid #bae6fd;
    }

    .hn-number {
        font-size: 1.5rem;
        font-weight: 700;
        color: #0284c7;
    }

    /* Sticky Sidebar */
    @media (min-width: 992px) {
        .sticky-sidebar {
            position: sticky;
            top: 1rem;
        }
    }

    /* Mobile Responsive */
    @media (max-width: 768px) {
        .form-control,
        .form-select {
            padding: 0.75rem 1rem;
            font-size: 1rem;
        }

        .card-body {
            padding: 1rem;
        }

        .card-header {
            padding: 0.875rem 1rem;
        }

        .btn-lg {
            padding: 0.875rem 1rem;
            font-size: 1rem;
        }
    }

    /* Input Group */
    .input-group-text {
        background: #f8fafc;
        border: 1px solid #e2e8f0;
        font-size: 0.85rem;
        color: #64748b;
    }
</style>
@endpush

@section('content')
<div class="container-fluid px-3 px-md-4">
    <!-- Header -->
    <div class="row mb-3">
        <div class="col-12">
            <div class="bg-gradient-primary text-white rounded-3 p-3 p-md-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center flex-wrap gap-2">
                    <div>
                        <h2 class="mb-1 fs-5"><i class="bi bi-pencil-square me-2"></i>แก้ไขข้อมูลลูกค้า</h2>
                        <p class="mb-0 opacity-90 small">HN: {{ $patient->hn }} | {{ $patient->name }}</p>
                    </div>
                    <a href="{{ route('patients.index') }}" class="btn btn-light btn-sm">
                        <i class="bi bi-arrow-left me-1"></i>กลับ
                    </a>
                </div>
            </div>
        </div>
    </div>

    <!-- Form -->
    <form method="POST" action="{{ route('patients.update', $patient->id) }}" enctype="multipart/form-data" autocomplete="new-password">
        @csrf
        @method('PUT')
        <div class="row">
            <div class="col-lg-8">
                <!-- Basic Information -->
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <span class="section-icon blue"><i class="bi bi-person-badge"></i></span>
                            <h5>ข้อมูลพื้นฐาน</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label">คำนำหน้า</label>
                                <select class="form-select @error('prefix') is-invalid @enderror" name="prefix">
                                    <option value="">เลือก</option>
                                    <option value="นาย" {{ old('prefix', $patient->prefix) == 'นาย' ? 'selected' : '' }}>นาย</option>
                                    <option value="นาง" {{ old('prefix', $patient->prefix) == 'นาง' ? 'selected' : '' }}>นาง</option>
                                    <option value="นางสาว" {{ old('prefix', $patient->prefix) == 'นางสาว' ? 'selected' : '' }}>นางสาว</option>
                                    <option value="เด็กชาย" {{ old('prefix', $patient->prefix) == 'เด็กชาย' ? 'selected' : '' }}>เด็กชาย</option>
                                    <option value="เด็กหญิง" {{ old('prefix', $patient->prefix) == 'เด็กหญิง' ? 'selected' : '' }}>เด็กหญิง</option>
                                </select>
                                @error('prefix')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">ชื่อ</label>
                                <input type="text" class="form-control @error('first_name') is-invalid @enderror" name="first_name" value="{{ old('first_name', $patient->first_name) }}" autocomplete="new-password">
                                @error('first_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-5">
                                <label class="form-label">นามสกุล</label>
                                <input type="text" class="form-control @error('last_name') is-invalid @enderror" name="last_name" value="{{ old('last_name', $patient->last_name) }}" autocomplete="new-password">
                                @error('last_name')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">ชื่อ (ภาษาอังกฤษ)</label>
                                <input type="text" class="form-control @error('first_name_en') is-invalid @enderror" name="first_name_en" value="{{ old('first_name_en', $patient->first_name_en) }}" placeholder="First Name" autocomplete="new-password">
                                @error('first_name_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">นามสกุล (ภาษาอังกฤษ)</label>
                                <input type="text" class="form-control @error('last_name_en') is-invalid @enderror" name="last_name_en" value="{{ old('last_name_en', $patient->last_name_en) }}" placeholder="Last Name" autocomplete="new-password">
                                @error('last_name_en')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>

                            <div class="col-md-4">
                                <label class="form-label">เลขบัตรประชาชน</label>
                                <input type="text" class="form-control @error('id_card') is-invalid @enderror" name="id_card" value="{{ old('id_card', $patient->id_card) }}" pattern="[0-9]{13}" maxlength="13" autocomplete="new-password">
                                @error('id_card')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">วันเกิด</label>
                                @php
                                    $birthDay = '';
                                    $birthMonth = '';
                                    $birthYear = '';
                                    if ($patient->birth_date) {
                                        $birthDay = $patient->birth_date->format('d');
                                        $birthMonth = $patient->birth_date->format('m');
                                        $birthYear = $patient->birth_date->year + 543;
                                    }
                                @endphp
                                <div class="d-flex gap-2">
                                    <input type="text" class="form-control text-center @error('birth_date') is-invalid @enderror" id="birth_day" placeholder="วัน" maxlength="2" style="width: 60px;" autocomplete="new-password" value="{{ $birthDay }}">
                                    <input type="text" class="form-control text-center" id="birth_month" placeholder="เดือน" maxlength="2" style="width: 70px;" autocomplete="new-password" value="{{ $birthMonth }}">
                                    <input type="text" class="form-control text-center" id="birth_year" placeholder="ปี พ.ศ." maxlength="4" style="flex: 1;" autocomplete="new-password" value="{{ $birthYear }}">
                                </div>
                                <input type="hidden" name="birth_date" id="birth_date_hidden" value="{{ $patient->birth_date ? $patient->birth_date->format('Y-m-d') : '' }}">
                                @error('birth_date')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">อายุ</label>
                                <div class="input-group">
                                    <input type="number" class="form-control" id="age" value="{{ $patient->age }}" readonly>
                                    <span class="input-group-text">ปี</span>
                                </div>
                            </div>

                            <div class="col-md-6">
                                <label class="form-label">เพศ</label>
                                <div class="d-flex gap-3">
                                    <div class="form-check">
                                        <input class="form-check-input @error('gender') is-invalid @enderror" type="radio" name="gender" id="male" value="male" {{ old('gender', $patient->gender) == 'male' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="male">
                                            <i class="bi bi-gender-male text-primary"></i> ชาย
                                        </label>
                                    </div>
                                    <div class="form-check">
                                        <input class="form-check-input @error('gender') is-invalid @enderror" type="radio" name="gender" id="female" value="female" {{ old('gender', $patient->gender) == 'female' ? 'checked' : '' }}>
                                        <label class="form-check-label" for="female">
                                            <i class="bi bi-gender-female text-danger"></i> หญิง
                                        </label>
                                    </div>
                                </div>
                                @error('gender')<div class="invalid-feedback d-block">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">กรุ๊ปเลือด</label>
                                <select class="form-select @error('blood_group') is-invalid @enderror" name="blood_group">
                                    <option value="">ไม่ระบุ</option>
                                    <option value="A" {{ old('blood_group', $patient->blood_group) == 'A' ? 'selected' : '' }}>A</option>
                                    <option value="B" {{ old('blood_group', $patient->blood_group) == 'B' ? 'selected' : '' }}>B</option>
                                    <option value="AB" {{ old('blood_group', $patient->blood_group) == 'AB' ? 'selected' : '' }}>AB</option>
                                    <option value="O" {{ old('blood_group', $patient->blood_group) == 'O' ? 'selected' : '' }}>O</option>
                                </select>
                                @error('blood_group')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Contact Information -->
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <span class="section-icon green"><i class="bi bi-telephone"></i></span>
                            <h5>ข้อมูลการติดต่อ</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">เบอร์โทรศัพท์</label>
                                <input type="tel" class="form-control @error('phone') is-invalid @enderror" name="phone" value="{{ old('phone', $patient->phone) }}" pattern="[0-9]{10}" maxlength="10" autocomplete="new-password">
                                @error('phone')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">อีเมล</label>
                                <input type="email" class="form-control @error('email') is-invalid @enderror" name="email" value="{{ old('email', $patient->email) }}" autocomplete="new-password">
                                @error('email')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">LINE ID</label>
                                <input type="text" class="form-control @error('line_id') is-invalid @enderror" name="line_id" value="{{ old('line_id', $patient->line_id) }}" autocomplete="new-password">
                                @error('line_id')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ผู้ติดต่อฉุกเฉิน</label>
                                <input type="text" class="form-control @error('emergency_contact') is-invalid @enderror" name="emergency_contact" value="{{ old('emergency_contact', $patient->emergency_contact) }}" autocomplete="new-password">
                                @error('emergency_contact')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">ที่อยู่</label>
                                <textarea class="form-control @error('address') is-invalid @enderror" name="address" rows="2">{{ old('address', $patient->address) }}</textarea>
                                @error('address')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">ตำบล/แขวง</label>
                                <input type="text" class="form-control @error('subdistrict') is-invalid @enderror" name="subdistrict" value="{{ old('subdistrict', $patient->subdistrict) }}" autocomplete="new-password">
                                @error('subdistrict')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">อำเภอ/เขต</label>
                                <input type="text" class="form-control @error('district') is-invalid @enderror" name="district" value="{{ old('district', $patient->district) }}" autocomplete="new-password">
                                @error('district')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">จังหวัด</label>
                                <select class="form-select @error('province') is-invalid @enderror" name="province">
                                    <option value="">เลือกจังหวัด</option>
                                    <option value="กรุงเทพมหานคร" {{ old('province', $patient->province) == 'กรุงเทพมหานคร' ? 'selected' : '' }}>กรุงเทพมหานคร</option>
                                    <option value="นนทบุรี" {{ old('province', $patient->province) == 'นนทบุรี' ? 'selected' : '' }}>นนทบุรี</option>
                                    <option value="ปทุมธานี" {{ old('province', $patient->province) == 'ปทุมธานี' ? 'selected' : '' }}>ปทุมธานี</option>
                                    <option value="สมุทรปราการ" {{ old('province', $patient->province) == 'สมุทรปราการ' ? 'selected' : '' }}>สมุทรปราการ</option>
                                    <option value="สมุทรสาคร" {{ old('province', $patient->province) == 'สมุทรสาคร' ? 'selected' : '' }}>สมุทรสาคร</option>
                                    <option value="นครปฐม" {{ old('province', $patient->province) == 'นครปฐม' ? 'selected' : '' }}>นครปฐม</option>
                                </select>
                                @error('province')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Medical Information -->
                <div class="card mb-3">
                    <div class="card-header">
                        <div class="d-flex align-items-center">
                            <span class="section-icon purple"><i class="bi bi-heart-pulse"></i></span>
                            <h5>ข้อมูลทางการแพทย์</h5>
                        </div>
                    </div>
                    <div class="card-body">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">โรคประจำตัว</label>
                                <textarea class="form-control @error('chronic_diseases') is-invalid @enderror" name="chronic_diseases" rows="3" placeholder="เบาหวาน, ความดันโลหิตสูง, โรคหัวใจ...">{{ old('chronic_diseases', $patient->chronic_diseases) }}</textarea>
                                @error('chronic_diseases')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ประวัติการแพ้ยา</label>
                                <textarea class="form-control @error('drug_allergy') is-invalid @enderror" name="drug_allergy" rows="3" placeholder="ชื่อยาที่แพ้...">{{ old('drug_allergy', $patient->drug_allergy) }}</textarea>
                                @error('drug_allergy')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">อาหารที่แพ้</label>
                                <input type="text" class="form-control @error('food_allergy') is-invalid @enderror" name="food_allergy" value="{{ old('food_allergy', $patient->food_allergy) }}" placeholder="อาหารทะเล, ถั่ว..." autocomplete="new-password">
                                @error('food_allergy')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">ประวัติการผ่าตัด</label>
                                <input type="text" class="form-control @error('surgery_history') is-invalid @enderror" name="surgery_history" value="{{ old('surgery_history', $patient->surgery_history) }}" autocomplete="new-password">
                                @error('surgery_history')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                            <div class="col-12">
                                <label class="form-label">อาการเบื้องต้น</label>
                                <textarea class="form-control @error('chief_complaint') is-invalid @enderror" name="chief_complaint" rows="3" placeholder="อธิบายอาการที่ต้องการรักษา...">{{ old('chief_complaint', $patient->chief_complaint) }}</textarea>
                                @error('chief_complaint')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="col-lg-4">
                <div class="sticky-sidebar">
                    <!-- HN Card -->
                    <div class="card hn-card mb-3">
                        <div class="card-body text-center py-3">
                            <small class="text-muted d-block mb-1">เลข HN</small>
                            <div class="hn-number">{{ $patient->hn }}</div>
                            <div class="small text-muted mt-2">
                                <div><i class="bi bi-calendar-plus me-1"></i>สร้าง: {{ $patient->created_at->locale('th')->isoFormat('D MMM') }} {{ substr($patient->created_at->year + 543, -2) }}</div>
                            </div>
                        </div>
                    </div>

                    <!-- Profile Photo -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6><i class="bi bi-camera me-2 text-primary"></i>รูปถ่าย</h6>
                        </div>
                        <div class="card-body text-center py-3">
                            <div class="avatar-preview mx-auto mb-3">
                                @if($patient->photo)
                                    <img src="{{ asset('storage/' . $patient->photo) }}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">
                                @else
                                    <i class="bi bi-person"></i>
                                @endif
                            </div>
                            <input type="file" class="form-control form-control-sm @error('photo') is-invalid @enderror" name="photo" accept="image/*">
                            @error('photo')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <small class="text-muted">JPG, PNG ไม่เกิน 2MB</small>
                        </div>
                    </div>

                    <!-- Insurance -->
                    <div class="card mb-3">
                        <div class="card-header">
                            <h6><i class="bi bi-shield-check me-2 text-success"></i>สิทธิการรักษา</h6>
                        </div>
                        <div class="card-body">
                            <select class="form-select mb-3 @error('insurance_type') is-invalid @enderror" name="insurance_type">
                                <option value="cash" {{ old('insurance_type', $patient->insurance_type) == 'cash' ? 'selected' : '' }}>ชำระเงินสด</option>
                                <option value="social" {{ old('insurance_type', $patient->insurance_type) == 'social' ? 'selected' : '' }}>ประกันสังคม</option>
                                <option value="government" {{ old('insurance_type', $patient->insurance_type) == 'government' ? 'selected' : '' }}>ข้าราชการ</option>
                                <option value="company" {{ old('insurance_type', $patient->insurance_type) == 'company' ? 'selected' : '' }}>บริษัทคู่สัญญา</option>
                                <option value="insurance" {{ old('insurance_type', $patient->insurance_type) == 'insurance' ? 'selected' : '' }}>ประกันสุขภาพ</option>
                            </select>
                            @error('insurance_type')<div class="invalid-feedback">{{ $message }}</div>@enderror
                            <input type="text" class="form-control @error('insurance_number') is-invalid @enderror" name="insurance_number" value="{{ old('insurance_number', $patient->insurance_number) }}" placeholder="เลขที่บัตรประกัน (ถ้ามี)" autocomplete="new-password">
                            @error('insurance_number')<div class="invalid-feedback">{{ $message }}</div>@enderror
                        </div>
                    </div>

                    <!-- Actions -->
                    <div class="card">
                        <div class="card-body d-grid gap-2">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="bi bi-check-circle me-2"></i>บันทึกการแก้ไข
                            </button>
                            <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary">
                                <i class="bi bi-x-circle me-2"></i>ยกเลิก
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
</div>

@push('scripts')
<script>
// Birth date separate fields
const dayInput = document.getElementById('birth_day');
const monthInput = document.getElementById('birth_month');
const yearInput = document.getElementById('birth_year');
const hiddenDate = document.getElementById('birth_date_hidden');

function calculateAge() {
    const day = parseInt(dayInput.value);
    const month = parseInt(monthInput.value);
    const year = parseInt(yearInput.value);

    if (day && month && year && day <= 31 && month <= 12 && yearInput.value.length === 4) {
        // Convert Buddhist year to Christian year if > 2400
        const christianYear = year > 2400 ? year - 543 : year;
        const isoDate = `${christianYear}-${String(month).padStart(2, '0')}-${String(day).padStart(2, '0')}`;

        // Update hidden field for form submission
        hiddenDate.value = isoDate;

        // Calculate age
        const today = new Date();
        const birth = new Date(christianYear, month - 1, day);
        let age = today.getFullYear() - birth.getFullYear();
        const monthDiff = today.getMonth() - birth.getMonth();
        if (monthDiff < 0 || (monthDiff === 0 && today.getDate() < birth.getDate())) {
            age--;
        }
        document.getElementById('age').value = age;
    }
}

// Auto move to next field
dayInput.addEventListener('input', function() {
    this.value = this.value.replace(/\D/g, '');
    if (this.value.length === 2) {
        monthInput.focus();
    }
    calculateAge();
});

monthInput.addEventListener('input', function() {
    this.value = this.value.replace(/\D/g, '');
    if (this.value.length === 2) {
        yearInput.focus();
    }
    calculateAge();
});

yearInput.addEventListener('input', function() {
    this.value = this.value.replace(/\D/g, '');
    calculateAge();
});

// Preview image
document.querySelector('input[name="photo"]')?.addEventListener('change', function(e) {
    const file = e.target.files[0];
    if (file && file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            document.querySelector('.avatar-preview').innerHTML = `<img src="${e.target.result}" style="width:100%;height:100%;object-fit:cover;border-radius:50%;">`;
        };
        reader.readAsDataURL(file);
    }
});

// Auto format ID card
document.querySelector('input[name="id_card"]')?.addEventListener('input', function(e) {
    this.value = this.value.replace(/\D/g, '');
});

// Auto format phone
document.querySelector('input[name="phone"]')?.addEventListener('input', function(e) {
    this.value = this.value.replace(/\D/g, '');
});
</script>
@endpush
@endsection
