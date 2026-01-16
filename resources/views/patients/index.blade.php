@extends('layouts.app')

@section('title', 'รายชื่อลูกค้า - GCMS')

@push('styles')
<style>
    /* ==================== MODERN PATIENTS PAGE 2024 ==================== */

    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 50%, #6366f1 100%);
        border-radius: 20px;
        padding: 2rem 2.5rem;
        color: white;
        margin-bottom: 1.5rem;
        position: relative;
        overflow: hidden;
    }

    .page-header::before {
        content: '';
        position: absolute;
        top: -50%;
        right: -20%;
        width: 400px;
        height: 400px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .page-header-content {
        position: relative;
        z-index: 1;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .page-header h2 {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
    }

    .page-header h2 i {
        font-size: 1.5rem;
    }

    .page-header p {
        margin: 0;
        opacity: 0.95;
        font-size: 0.95rem;
    }

    .btn-add-new {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.3);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .btn-add-new:hover {
        background: white;
        color: #0ea5e9;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    /* Search Card */
    .search-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        border: 1px solid #f1f5f9;
        margin-bottom: 1.5rem;
    }

    .search-input-wrapper {
        position: relative;
    }

    .search-input-wrapper i {
        position: absolute;
        left: 16px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
        font-size: 1rem;
    }

    .search-input {
        width: 100%;
        padding: 14px 16px 14px 48px;
        border: 2px solid #e2e8f0;
        border-radius: 12px;
        font-size: 0.95rem;
        transition: all 0.3s ease;
        color: #334155;
    }

    .search-input::placeholder {
        color: #94a3b8;
    }

    .search-input:focus {
        outline: none;
        border-color: #0ea5e9;
        box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1);
    }

    .btn-search {
        background: linear-gradient(135deg, #0ea5e9, #3b82f6);
        color: white;
        border: none;
        padding: 14px 24px;
        border-radius: 12px;
        font-weight: 600;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        cursor: pointer;
    }

    .btn-search:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(14, 165, 233, 0.3);
    }

    .btn-reset {
        background: #f1f5f9;
        color: #64748b;
        border: none;
        padding: 14px 24px;
        border-radius: 12px;
        font-weight: 500;
        font-size: 0.9rem;
        display: inline-flex;
        align-items: center;
        gap: 0.5rem;
        transition: all 0.3s ease;
        text-decoration: none;
    }

    .btn-reset:hover {
        background: #e2e8f0;
        color: #475569;
    }

    /* Filter Selects */
    .filter-row {
        display: grid;
        grid-template-columns: repeat(4, 1fr);
        gap: 1rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #f1f5f9;
    }

    .filter-select {
        padding: 12px 16px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.875rem;
        color: #475569;
        background: white;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .filter-select:focus {
        outline: none;
        border-color: #0ea5e9;
    }

    /* Active Filters */
    .active-filters {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        gap: 0.5rem;
        margin-top: 1rem;
        padding-top: 1rem;
        border-top: 1px solid #f1f5f9;
    }

    .filter-label {
        font-size: 0.8rem;
        color: #64748b;
        font-weight: 500;
    }

    .filter-tag {
        background: #e0f2fe;
        color: #0369a1;
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 500;
    }

    /* Patient Table */
    .patient-table-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }

    .patient-table {
        width: 100%;
        border-collapse: collapse;
    }

    .patient-table thead tr {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    }

    .patient-table thead th {
        padding: 16px 20px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
    }

    .patient-table tbody tr {
        transition: all 0.2s ease;
    }

    .patient-table tbody tr:hover {
        background: #f8fafc;
    }

    .patient-table tbody td {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 0.9rem;
        color: #334155;
    }

    .patient-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* Patient Avatar */
    .patient-avatar {
        width: 44px;
        height: 44px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: 600;
        flex-shrink: 0;
    }

    .patient-avatar.male {
        background: linear-gradient(135deg, #e0f2fe, #bae6fd);
        color: #0369a1;
    }

    .patient-avatar.female {
        background: linear-gradient(135deg, #fce7f3, #fbcfe8);
        color: #be185d;
    }

    .patient-avatar.other {
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        color: #475569;
    }

    .patient-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .patient-name {
        font-weight: 600;
        color: #1e293b;
    }

    .patient-hn {
        font-weight: 600;
        color: #0369a1;
        font-size: 0.85rem;
    }

    /* Phone Button */
    .phone-link {
        display: inline-flex;
        align-items: center;
        gap: 6px;
        color: #334155;
        text-decoration: none;
        padding: 6px 12px;
        border-radius: 8px;
        transition: all 0.2s ease;
    }

    .phone-link:hover {
        background: #dcfce7;
        color: #166534;
    }

    .phone-link i {
        color: #10b981;
    }

    /* Gender Badge */
    .gender-badge {
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
        display: inline-block;
    }

    .gender-badge.male {
        background: #e0f2fe;
        color: #0369a1;
    }

    .gender-badge.female {
        background: #fce7f3;
        color: #be185d;
    }

    .gender-badge.other {
        background: #f1f5f9;
        color: #475569;
    }

    /* Action Buttons */
    .action-btns {
        display: flex;
        gap: 6px;
        justify-content: center;
    }

    .btn-action {
        width: 36px;
        height: 36px;
        border-radius: 10px;
        display: inline-flex;
        align-items: center;
        justify-content: center;
        font-size: 0.9rem;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
    }

    .btn-action:hover {
        transform: translateY(-2px);
    }

    .btn-action.view {
        background: #e0f2fe;
        color: #0369a1;
    }

    .btn-action.view:hover {
        background: #bae6fd;
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
    }

    .btn-action.edit {
        background: #fef3c7;
        color: #92400e;
    }

    .btn-action.edit:hover {
        background: #fde68a;
        box-shadow: 0 4px 12px rgba(245, 158, 11, 0.3);
    }

    .btn-action.delete {
        background: #fee2e2;
        color: #dc2626;
    }

    .btn-action.delete:hover {
        background: #fecaca;
        box-shadow: 0 4px 12px rgba(220, 38, 38, 0.3);
    }

    /* Pagination */
    .pagination-wrapper {
        padding: 1.25rem 1.5rem;
        background: #f8fafc;
        border-top: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .pagination-info {
        font-size: 0.85rem;
        color: #64748b;
    }

    /* Mobile Card View */
    .patient-card-mobile {
        background: white;
        border-radius: 16px;
        padding: 1.25rem;
        margin-bottom: 1rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        border: 1px solid #f1f5f9;
        transition: all 0.2s ease;
    }

    .patient-card-mobile:hover {
        transform: translateY(-2px);
        box-shadow: 0 8px 30px rgba(0,0,0,0.08);
    }

    .patient-card-header {
        display: flex;
        justify-content: space-between;
        align-items: flex-start;
        margin-bottom: 1rem;
    }

    .patient-card-info {
        display: flex;
        gap: 12px;
    }

    .patient-card-details h6 {
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
        margin: 0 0 4px 0;
    }

    .patient-card-meta {
        display: flex;
        flex-wrap: wrap;
        gap: 8px;
        font-size: 0.8rem;
        color: #64748b;
    }

    .patient-card-meta .hn {
        color: #0369a1;
        font-weight: 600;
    }

    .patient-card-actions {
        display: flex;
        gap: 6px;
    }

    .btn-call-mobile {
        display: inline-flex;
        align-items: center;
        gap: 8px;
        background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        color: #166534;
        padding: 10px 16px;
        border-radius: 10px;
        text-decoration: none;
        font-weight: 600;
        font-size: 0.85rem;
        transition: all 0.2s ease;
    }

    .btn-call-mobile:hover {
        transform: translateY(-2px);
        box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
        color: #166534;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-state i {
        font-size: 4rem;
        color: #cbd5e1;
        margin-bottom: 1rem;
    }

    .empty-state h4 {
        color: #64748b;
        font-weight: 600;
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: #94a3b8;
        font-size: 0.9rem;
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .filter-row {
            grid-template-columns: repeat(2, 1fr);
        }
    }

    @media (max-width: 768px) {
        .page-header {
            padding: 1.5rem;
            border-radius: 16px;
        }

        .page-header h2 {
            font-size: 1.35rem;
        }

        .page-header-content {
            flex-direction: column;
            align-items: flex-start;
        }

        .btn-add-new {
            width: 100%;
            justify-content: center;
        }

        .search-card {
            padding: 1rem;
            border-radius: 12px;
        }

        .filter-row {
            grid-template-columns: 1fr 1fr;
            gap: 0.75rem;
        }

        .filter-select {
            padding: 10px 12px;
            font-size: 0.8rem;
        }
    }

    @media (max-width: 480px) {
        .filter-row {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header">
        <div class="page-header-content">
            <div>
                <h2><i class="bi bi-people-fill"></i>จัดการข้อมูลลูกค้า</h2>
                <p>ระบบจัดการข้อมูลลูกค้า ประวัติการรักษา และข้อมูลสุขภาพ</p>
            </div>
            <a href="{{ route('patients.create') }}" class="btn-add-new">
                <i class="bi bi-plus-circle-fill"></i>
                ลูกค้าใหม่
            </a>
        </div>
    </div>

    <!-- Search Section -->
    <div class="search-card">
        <form method="GET" action="{{ route('patients.index') }}">
            <div class="row g-3 align-items-end">
                <div class="col-12 col-lg-6">
                    <div class="search-input-wrapper">
                        <i class="bi bi-search"></i>
                        <input type="text"
                               name="search"
                               class="search-input"
                               placeholder="ค้นหาชื่อ, เบอร์โทรศัพท์, HN..."
                               value="{{ request('search') }}">
                    </div>
                </div>
                <div class="col-6 col-lg-3">
                    <button type="submit" class="btn-search w-100">
                        <i class="bi bi-search"></i>
                        ค้นหา
                    </button>
                </div>
                <div class="col-6 col-lg-3">
                    <a href="{{ route('patients.index') }}" class="btn-reset w-100">
                        <i class="bi bi-arrow-clockwise"></i>
                        รีเซ็ต
                    </a>
                </div>
            </div>

            <!-- Filters -->
            <div class="filter-row">
                <select name="filter" class="filter-select" onchange="this.form.submit()">
                    <option value="">ลูกค้าทั้งหมด</option>
                    <option value="course" {{ request('filter') == 'course' ? 'selected' : '' }}>ลูกค้าคอร์ส</option>
                    <option value="normal" {{ request('filter') == 'normal' ? 'selected' : '' }}>ลูกค้าทั่วไป</option>
                </select>
                <select name="gender" class="filter-select" onchange="this.form.submit()">
                    <option value="">เพศทั้งหมด</option>
                    <option value="male" {{ request('gender') == 'male' ? 'selected' : '' }}>ชาย</option>
                    <option value="female" {{ request('gender') == 'female' ? 'selected' : '' }}>หญิง</option>
                    <option value="other" {{ request('gender') == 'other' ? 'selected' : '' }}>อื่นๆ</option>
                </select>
                <select name="age_range" class="filter-select" onchange="this.form.submit()">
                    <option value="">ทุกช่วงอายุ</option>
                    <option value="0-20" {{ request('age_range') == '0-20' ? 'selected' : '' }}>0-20 ปี</option>
                    <option value="21-40" {{ request('age_range') == '21-40' ? 'selected' : '' }}>21-40 ปี</option>
                    <option value="41-60" {{ request('age_range') == '41-60' ? 'selected' : '' }}>41-60 ปี</option>
                    <option value="60+" {{ request('age_range') == '60+' ? 'selected' : '' }}>60+ ปี</option>
                </select>
                <select name="sort" class="filter-select" onchange="this.form.submit()">
                    <option value="hn_desc" {{ request('sort') == 'hn_desc' || !request('sort') ? 'selected' : '' }}>HN (มาก-น้อย)</option>
                    <option value="hn_asc" {{ request('sort') == 'hn_asc' ? 'selected' : '' }}>HN (น้อย-มาก)</option>
                    <option value="name_asc" {{ request('sort') == 'name_asc' ? 'selected' : '' }}>ชื่อ (ก-ฮ)</option>
                    <option value="name_desc" {{ request('sort') == 'name_desc' ? 'selected' : '' }}>ชื่อ (ฮ-ก)</option>
                    <option value="newest" {{ request('sort') == 'newest' ? 'selected' : '' }}>ล่าสุด</option>
                    <option value="oldest" {{ request('sort') == 'oldest' ? 'selected' : '' }}>เก่าสุด</option>
                </select>
            </div>

            <!-- Active Filters -->
            @if(request('search') || request('gender') || request('filter') || request('age_range') || request('sort'))
                <div class="active-filters">
                    <span class="filter-label">ฟิลเตอร์ที่ใช้:</span>
                    @if(request('search'))
                        <span class="filter-tag">"{{ request('search') }}"</span>
                    @endif
                    @if(request('filter'))
                        <span class="filter-tag">{{ request('filter') == 'course' ? 'ลูกค้าคอร์ส' : 'ลูกค้าทั่วไป' }}</span>
                    @endif
                    @if(request('gender'))
                        <span class="filter-tag">{{ request('gender') == 'male' ? 'ชาย' : (request('gender') == 'female' ? 'หญิง' : 'อื่นๆ') }}</span>
                    @endif
                    @if(request('age_range'))
                        <span class="filter-tag">อายุ {{ request('age_range') }} ปี</span>
                    @endif
                    @if(request('sort') && request('sort') != 'hn_desc')
                        <span class="filter-tag">
                            @switch(request('sort'))
                                @case('hn_asc') HN (น้อย-มาก) @break
                                @case('name_asc') ชื่อ (ก-ฮ) @break
                                @case('name_desc') ชื่อ (ฮ-ก) @break
                                @case('newest') ล่าสุด @break
                                @case('oldest') เก่าสุด @break
                            @endswitch
                        </span>
                    @endif
                </div>
            @endif
        </form>
    </div>

    <!-- Mobile Card View -->
    <div class="d-block d-md-none">
        @forelse($patients as $patient)
            <div class="patient-card-mobile">
                <div class="patient-card-header">
                    <div class="patient-card-info">
                        <div class="patient-avatar {{ $patient->gender ?? 'other' }}">
                            {{ mb_substr($patient->name, 0, 1) }}
                        </div>
                        <div class="patient-card-details">
                            <h6>{{ $patient->name }}</h6>
                            <div class="patient-card-meta">
                                <span class="hn">HN: {{ $patient->hn }}</span>
                                <span>{{ $patient->gender == 'male' ? 'ชาย' : ($patient->gender == 'female' ? 'หญิง' : 'อื่นๆ') }}</span>
                                @if($patient->age)
                                    <span>{{ $patient->age }} ปี</span>
                                @endif
                            </div>
                        </div>
                    </div>
                    <div class="patient-card-actions">
                        <a href="{{ route('patients.show', $patient->id) }}" class="btn-action view">
                            <i class="bi bi-eye"></i>
                        </a>
                        <a href="{{ route('patients.edit', $patient->id) }}" class="btn-action edit">
                            <i class="bi bi-pencil"></i>
                        </a>
                    </div>
                </div>
                <a href="tel:{{ $patient->phone }}" class="btn-call-mobile">
                    <i class="bi bi-telephone-fill"></i>
                    {{ $patient->phone }}
                </a>
            </div>
        @empty
            <div class="patient-card-mobile">
                <div class="empty-state">
                    <i class="bi bi-person-x"></i>
                    <h4>ไม่พบข้อมูลลูกค้า</h4>
                    <p>ลองค้นหาด้วยคำค้นอื่น หรือเพิ่มลูกค้าใหม่</p>
                </div>
            </div>
        @endforelse

        @if($patients->hasPages())
            <div class="patient-card-mobile">
                {{ $patients->links('pagination::bootstrap-5') }}
            </div>
        @endif
    </div>

    <!-- Desktop Table View -->
    <div class="d-none d-md-block">
        <div class="patient-table-card">
            <table class="patient-table">
                <thead>
                    <tr>
                        <th width="100">HN</th>
                        <th>ชื่อลูกค้า</th>
                        <th width="160">เบอร์โทรศัพท์</th>
                        <th width="80" class="text-center">อายุ</th>
                        <th width="100" class="text-center">เพศ</th>
                        <th width="150">สาขา</th>
                        <th width="140" class="text-center">จัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                        <tr>
                            <td>
                                <span class="patient-hn">{{ $patient->hn }}</span>
                            </td>
                            <td>
                                <div class="patient-info">
                                    <div class="patient-avatar {{ $patient->gender ?? 'other' }}">
                                        {{ mb_substr($patient->name, 0, 1) }}
                                    </div>
                                    <span class="patient-name">{{ $patient->name }}</span>
                                </div>
                            </td>
                            <td>
                                <a href="tel:{{ $patient->phone }}" class="phone-link">
                                    <i class="bi bi-telephone-fill"></i>
                                    {{ $patient->phone }}
                                </a>
                            </td>
                            <td class="text-center">
                                @if($patient->age)
                                    <span>{{ $patient->age }} ปี</span>
                                @else
                                    <span style="color: #94a3b8;">-</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @if($patient->gender == 'male')
                                    <span class="gender-badge male">ชาย</span>
                                @elseif($patient->gender == 'female')
                                    <span class="gender-badge female">หญิง</span>
                                @else
                                    <span class="gender-badge other">อื่นๆ</span>
                                @endif
                            </td>
                            <td>
                                <span style="color: #475569;">{{ $patient->firstVisitBranch->name ?? '-' }}</span>
                            </td>
                            <td>
                                <div class="action-btns">
                                    <a href="{{ route('patients.show', $patient->id) }}"
                                       class="btn-action view"
                                       title="ดูข้อมูล">
                                        <i class="bi bi-eye"></i>
                                    </a>
                                    <a href="{{ route('patients.edit', $patient->id) }}"
                                       class="btn-action edit"
                                       title="แก้ไข">
                                        <i class="bi bi-pencil"></i>
                                    </a>
                                    <button type="button"
                                            class="btn-action delete"
                                            title="ลบ"
                                            onclick="if(confirm('ต้องการลบข้อมูลลูกค้านี้?')) { document.getElementById('delete-form-{{ $patient->id }}').submit(); }">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                    <form id="delete-form-{{ $patient->id }}"
                                          method="POST"
                                          action="{{ route('patients.destroy', $patient->id) }}"
                                          class="d-none">
                                        @csrf
                                        @method('DELETE')
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7">
                                <div class="empty-state">
                                    <i class="bi bi-person-x"></i>
                                    <h4>ไม่พบข้อมูลลูกค้า</h4>
                                    <p>ลองค้นหาด้วยคำค้นอื่น หรือเพิ่มลูกค้าใหม่</p>
                                </div>
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>

            @if($patients->hasPages())
                <div class="pagination-wrapper">
                    <div class="pagination-info">
                        แสดง {{ $patients->firstItem() }} - {{ $patients->lastItem() }} จาก {{ $patients->total() }} รายการ
                    </div>
                    {{ $patients->links('pagination::bootstrap-5') }}
                </div>
            @endif
        </div>
    </div>
</div>
@endsection
