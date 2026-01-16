@extends('layouts.app')

@section('title', 'จัดการบริการ')

@push('styles')
<style>
    /* ===== CSS Variables ===== */
    :root {
        --sv-primary: #0ea5e9;
        --sv-primary-dark: #0284c7;
        --sv-secondary: #8b5cf6;
        --sv-success: #10b981;
        --sv-warning: #f59e0b;
        --sv-danger: #ef4444;
        --sv-gray-50: #f8fafc;
        --sv-gray-100: #f1f5f9;
        --sv-gray-200: #e2e8f0;
        --sv-gray-300: #cbd5e1;
        --sv-gray-400: #94a3b8;
        --sv-gray-500: #64748b;
        --sv-gray-600: #475569;
        --sv-gray-700: #334155;
        --sv-gray-800: #1e293b;
        --sv-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
        --sv-shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        --sv-shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
        --sv-radius: 16px;
        --sv-radius-sm: 10px;
        --sv-radius-xs: 6px;
    }

    /* ===== Page Header ===== */
    .page-header {
        background: linear-gradient(135deg, #0ea5e9 0%, #8b5cf6 100%);
        border-radius: var(--sv-radius);
        padding: 2rem;
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
        background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
        border-radius: 50%;
    }

    .page-header::after {
        content: '';
        position: absolute;
        bottom: -30%;
        left: 10%;
        width: 200px;
        height: 200px;
        background: radial-gradient(circle, rgba(255,255,255,0.1) 0%, transparent 70%);
        border-radius: 50%;
    }

    .page-header h1 {
        font-size: 1.75rem;
        font-weight: 700;
        margin-bottom: 0.5rem;
        position: relative;
        z-index: 1;
    }

    .page-header p {
        opacity: 0.9;
        margin-bottom: 0;
        position: relative;
        z-index: 1;
    }

    .btn-add-new {
        background: rgba(255,255,255,0.2);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255,255,255,0.3);
        color: white;
        padding: 0.75rem 1.5rem;
        border-radius: var(--sv-radius-sm);
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
        z-index: 1;
    }

    .btn-add-new:hover {
        background: white;
        color: var(--sv-primary);
        transform: translateY(-2px);
        box-shadow: var(--sv-shadow-lg);
    }

    /* ===== Stats Cards ===== */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: white;
        border-radius: var(--sv-radius-sm);
        padding: 1.25rem;
        box-shadow: var(--sv-shadow);
        border: 1px solid var(--sv-gray-100);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .stat-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        width: 4px;
        height: 100%;
    }

    .stat-card.total::before { background: linear-gradient(180deg, var(--sv-primary), var(--sv-secondary)); }
    .stat-card.active::before { background: linear-gradient(180deg, var(--sv-success), #34d399); }
    .stat-card.inactive::before { background: linear-gradient(180deg, var(--sv-warning), #fbbf24); }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--sv-shadow-md);
    }

    .stat-icon {
        width: 44px;
        height: 44px;
        border-radius: var(--sv-radius-xs);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.2rem;
    }

    .stat-card.total .stat-icon { background: linear-gradient(135deg, rgba(14, 165, 233, 0.15), rgba(139, 92, 246, 0.15)); color: var(--sv-primary); }
    .stat-card.active .stat-icon { background: rgba(16, 185, 129, 0.15); color: var(--sv-success); }
    .stat-card.inactive .stat-icon { background: rgba(245, 158, 11, 0.15); color: var(--sv-warning); }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--sv-gray-800);
        line-height: 1;
    }

    .stat-label {
        font-size: 0.8rem;
        color: var(--sv-gray-500);
        margin-top: 0.25rem;
    }

    /* ===== Search & Filter ===== */
    .filter-bar {
        background: white;
        border-radius: var(--sv-radius-sm);
        padding: 1rem 1.25rem;
        box-shadow: var(--sv-shadow);
        border: 1px solid var(--sv-gray-100);
        margin-bottom: 1.5rem;
        display: flex;
        gap: 1rem;
        flex-wrap: wrap;
        align-items: center;
    }

    .search-box {
        position: relative;
        flex: 1;
        min-width: 200px;
    }

    .search-box input {
        width: 100%;
        padding: 0.625rem 1rem 0.625rem 2.5rem;
        border: 1px solid var(--sv-gray-200);
        border-radius: 50px;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        background: var(--sv-gray-50);
    }

    .search-box input:focus {
        outline: none;
        border-color: var(--sv-primary);
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
        background: white;
    }

    .search-box i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--sv-gray-400);
    }

    .filter-select {
        padding: 0.625rem 1rem;
        border: 1px solid var(--sv-gray-200);
        border-radius: var(--sv-radius-xs);
        font-size: 0.875rem;
        min-width: 150px;
        background: var(--sv-gray-50);
        transition: all 0.2s ease;
    }

    .filter-select:focus {
        outline: none;
        border-color: var(--sv-primary);
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
    }

    /* ===== Main Card ===== */
    .main-card {
        background: white;
        border-radius: var(--sv-radius);
        box-shadow: var(--sv-shadow);
        border: 1px solid var(--sv-gray-100);
        overflow: hidden;
    }

    .card-toolbar {
        padding: 1rem 1.5rem;
        background: var(--sv-gray-50);
        border-bottom: 1px solid var(--sv-gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .card-title {
        font-size: 1rem;
        font-weight: 600;
        color: var(--sv-gray-800);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0;
    }

    .card-title i {
        color: var(--sv-primary);
    }

    /* ===== Services Grid ===== */
    .services-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(320px, 1fr));
        gap: 1.25rem;
        padding: 1.5rem;
    }

    .service-card {
        background: white;
        border-radius: var(--sv-radius-sm);
        border: 1px solid var(--sv-gray-200);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .service-card:hover {
        border-color: var(--sv-primary);
        box-shadow: var(--sv-shadow-md);
        transform: translateY(-2px);
    }

    .service-card.inactive {
        opacity: 0.7;
    }

    .service-header {
        padding: 1.25rem;
        background: linear-gradient(135deg, var(--sv-gray-50), white);
        border-bottom: 1px solid var(--sv-gray-100);
        position: relative;
    }

    .service-status {
        position: absolute;
        top: 1rem;
        right: 1rem;
        padding: 0.25rem 0.75rem;
        border-radius: 50px;
        font-size: 0.7rem;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
    }

    .service-status.active {
        background: rgba(16, 185, 129, 0.15);
        color: var(--sv-success);
    }

    .service-status.inactive {
        background: rgba(100, 116, 139, 0.15);
        color: var(--sv-gray-500);
    }

    .service-code {
        display: inline-block;
        padding: 0.25rem 0.625rem;
        background: linear-gradient(135deg, var(--sv-primary), var(--sv-secondary));
        color: white;
        border-radius: var(--sv-radius-xs);
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .service-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--sv-gray-800);
        margin-bottom: 0.25rem;
        padding-right: 70px;
        line-height: 1.3;
    }

    .service-category {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.625rem;
        background: rgba(139, 92, 246, 0.1);
        color: var(--sv-secondary);
        border-radius: var(--sv-radius-xs);
        font-size: 0.75rem;
        font-weight: 500;
    }

    .service-desc {
        font-size: 0.8rem;
        color: var(--sv-gray-500);
        margin-top: 0.5rem;
        line-height: 1.4;
    }

    .service-body {
        padding: 1.25rem;
    }

    .service-info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 0.75rem;
    }

    .info-item {
        text-align: center;
        padding: 0.75rem;
        background: var(--sv-gray-50);
        border-radius: var(--sv-radius-xs);
    }

    .info-item.highlight {
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.1), rgba(139, 92, 246, 0.1));
    }

    .info-value {
        font-size: 1.15rem;
        font-weight: 700;
        color: var(--sv-gray-800);
    }

    .info-value.price {
        color: var(--sv-primary);
    }

    .info-value.df {
        color: var(--sv-warning);
    }

    .info-label {
        font-size: 0.7rem;
        color: var(--sv-gray-500);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 0.25rem;
    }

    .service-footer {
        padding: 1rem 1.25rem;
        background: var(--sv-gray-50);
        border-top: 1px solid var(--sv-gray-100);
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
    }

    .btn-action {
        padding: 0.5rem 1rem;
        border-radius: var(--sv-radius-xs);
        font-size: 0.8rem;
        font-weight: 500;
        border: none;
        cursor: pointer;
        transition: all 0.2s ease;
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
    }

    .btn-edit {
        background: rgba(14, 165, 233, 0.1);
        color: var(--sv-primary);
    }

    .btn-edit:hover {
        background: var(--sv-primary);
        color: white;
    }

    .btn-delete {
        background: rgba(239, 68, 68, 0.1);
        color: var(--sv-danger);
    }

    .btn-delete:hover {
        background: var(--sv-danger);
        color: white;
    }

    /* ===== Empty State ===== */
    .empty-state {
        text-align: center;
        padding: 4rem 2rem;
    }

    .empty-state-icon {
        width: 100px;
        height: 100px;
        background: linear-gradient(135deg, var(--sv-gray-100), var(--sv-gray-200));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
    }

    .empty-state-icon i {
        font-size: 2.5rem;
        color: var(--sv-gray-400);
    }

    .empty-state h3 {
        font-size: 1.25rem;
        color: var(--sv-gray-700);
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: var(--sv-gray-500);
        margin-bottom: 1.5rem;
    }

    /* ===== Modal Styles ===== */
    .modal-content {
        border: none;
        border-radius: var(--sv-radius);
        overflow: hidden;
    }

    .modal-header {
        background: linear-gradient(135deg, var(--sv-primary), var(--sv-secondary));
        color: white;
        padding: 1.25rem 1.5rem;
        border: none;
    }

    .modal-header .btn-close {
        filter: brightness(0) invert(1);
        opacity: 0.8;
    }

    .modal-header .btn-close:hover {
        opacity: 1;
    }

    .modal-title {
        font-weight: 600;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .modal-body {
        padding: 1.5rem;
    }

    .form-section {
        margin-bottom: 1.5rem;
    }

    .form-section-title {
        font-size: 0.85rem;
        font-weight: 600;
        color: var(--sv-gray-600);
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--sv-gray-100);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-section-title i {
        color: var(--sv-primary);
    }

    .form-label {
        font-size: 0.85rem;
        font-weight: 500;
        color: var(--sv-gray-700);
        margin-bottom: 0.375rem;
    }

    .form-control, .form-select {
        border: 1px solid var(--sv-gray-200);
        border-radius: var(--sv-radius-xs);
        padding: 0.625rem 0.875rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--sv-primary);
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
    }

    .input-group-text {
        background: var(--sv-gray-100);
        border: 1px solid var(--sv-gray-200);
        border-radius: var(--sv-radius-xs);
        font-weight: 600;
        color: var(--sv-gray-600);
    }

    .form-hint {
        font-size: 0.75rem;
        color: var(--sv-gray-500);
        margin-top: 0.25rem;
    }

    .modal-footer {
        background: var(--sv-gray-50);
        border-top: 1px solid var(--sv-gray-200);
        padding: 1rem 1.5rem;
    }

    .btn-save {
        background: linear-gradient(135deg, var(--sv-primary), var(--sv-secondary));
        border: none;
        color: white;
        padding: 0.625rem 1.5rem;
        border-radius: var(--sv-radius-xs);
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-save:hover {
        transform: translateY(-1px);
        box-shadow: var(--sv-shadow-md);
        color: white;
    }

    .btn-cancel {
        background: white;
        border: 1px solid var(--sv-gray-300);
        color: var(--sv-gray-600);
        padding: 0.625rem 1.5rem;
        border-radius: var(--sv-radius-xs);
        font-weight: 500;
    }

    .btn-cancel:hover {
        background: var(--sv-gray-100);
    }

    /* ===== Delete Modal ===== */
    .delete-modal .modal-header {
        background: linear-gradient(135deg, var(--sv-danger), #dc2626);
    }

    .delete-warning {
        text-align: center;
        padding: 1rem;
    }

    .delete-warning-icon {
        width: 70px;
        height: 70px;
        background: rgba(239, 68, 68, 0.1);
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1rem;
    }

    .delete-warning-icon i {
        font-size: 2rem;
        color: var(--sv-danger);
    }

    .btn-delete-confirm {
        background: var(--sv-danger);
        border: none;
        color: white;
        padding: 0.625rem 1.5rem;
        border-radius: var(--sv-radius-xs);
        font-weight: 600;
    }

    .btn-delete-confirm:hover {
        background: #dc2626;
        color: white;
    }

    /* ===== Pagination ===== */
    .pagination-wrapper {
        padding: 1rem 1.5rem;
        background: var(--sv-gray-50);
        border-top: 1px solid var(--sv-gray-200);
    }

    /* ===== Alert ===== */
    .alert-float {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        border-radius: var(--sv-radius-sm);
        box-shadow: var(--sv-shadow-lg);
        animation: slideIn 0.3s ease;
    }

    @keyframes slideIn {
        from {
            transform: translateX(100%);
            opacity: 0;
        }
        to {
            transform: translateX(0);
            opacity: 1;
        }
    }

    /* ===== Responsive ===== */
    @media (max-width: 768px) {
        .page-header {
            padding: 1.5rem;
        }

        .page-header h1 {
            font-size: 1.5rem;
        }

        .stats-grid {
            grid-template-columns: repeat(3, 1fr);
        }

        .services-grid {
            grid-template-columns: 1fr;
            padding: 1rem;
        }

        .filter-bar {
            flex-direction: column;
        }

        .search-box {
            width: 100%;
        }

        .filter-select {
            width: 100%;
        }
    }

    @media (max-width: 480px) {
        .stats-grid {
            grid-template-columns: 1fr;
        }

        .service-info-grid {
            grid-template-columns: 1fr;
        }
    }
</style>
@endpush

@section('content')
<div class="container-fluid py-3">
    <!-- Page Header -->
    <div class="page-header text-white">
        <div class="d-flex justify-content-between align-items-center flex-wrap gap-3">
            <div>
                <h1><i class="bi bi-clipboard2-pulse me-2"></i>จัดการบริการ</h1>
                <p><i class="bi bi-building me-1"></i>จัดการรายการบริการทั้งหมดของคลินิก</p>
            </div>
            <button class="btn-add-new" data-bs-toggle="modal" data-bs-target="#serviceModal" onclick="openCreateModal()">
                <i class="bi bi-plus-lg me-2"></i>เพิ่มบริการใหม่
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card total">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-value">{{ $services->total() }}</div>
                    <div class="stat-label">บริการทั้งหมด</div>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-clipboard2-pulse"></i>
                </div>
            </div>
        </div>
        <div class="stat-card active">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-value">{{ $services->where('is_active', true)->count() }}</div>
                    <div class="stat-label">เปิดใช้งาน</div>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="stat-card inactive">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-value">{{ $services->where('is_active', false)->count() }}</div>
                    <div class="stat-label">ปิดใช้งาน</div>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-pause-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-float alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Filter Bar -->
    <div class="filter-bar">
        <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" id="searchInput" placeholder="ค้นหาบริการ...">
        </div>
        <select class="filter-select" id="categoryFilter">
            <option value="">ทุกหมวดหมู่</option>
            @foreach($categories as $category)
            <option value="{{ strtolower($category->name) }}">{{ $category->name }}</option>
            @endforeach
        </select>
        <select class="filter-select" id="statusFilter">
            <option value="">ทุกสถานะ</option>
            <option value="active">เปิดใช้งาน</option>
            <option value="inactive">ปิดใช้งาน</option>
        </select>
    </div>

    <!-- Main Card -->
    <div class="main-card">
        <div class="card-toolbar">
            <h5 class="card-title">
                <i class="bi bi-grid-3x3-gap"></i>
                รายการบริการ
            </h5>
            <span class="badge bg-primary">{{ $services->total() }} รายการ</span>
        </div>

        @if($services->count() > 0)
        <div class="services-grid">
            @foreach($services as $service)
            <div class="service-card {{ !$service->is_active ? 'inactive' : '' }}"
                 data-search="{{ strtolower($service->name . ' ' . $service->code . ' ' . ($service->serviceCategory->name ?? '')) }}"
                 data-category="{{ strtolower($service->serviceCategory->name ?? '') }}"
                 data-status="{{ $service->is_active ? 'active' : 'inactive' }}">
                <div class="service-header">
                    <span class="service-status {{ $service->is_active ? 'active' : 'inactive' }}">
                        {{ $service->is_active ? 'ใช้งาน' : 'ปิด' }}
                    </span>
                    @if($service->code)
                    <span class="service-code">{{ $service->code }}</span>
                    @endif
                    <h3 class="service-name">{{ $service->name }}</h3>
                    @if($service->serviceCategory)
                    <span class="service-category">
                        <i class="bi bi-folder"></i>
                        {{ $service->serviceCategory->name }}
                    </span>
                    @endif
                    @if($service->description)
                    <p class="service-desc">{{ Str::limit($service->description, 80) }}</p>
                    @endif
                </div>

                <div class="service-body">
                    <div class="service-info-grid" style="grid-template-columns: repeat(3, 1fr);">
                        <div class="info-item highlight">
                            <div class="info-value price">{{ number_format($service->default_price, 0) }}</div>
                            <div class="info-label">ราคา (บาท)</div>
                        </div>
                        <div class="info-item">
                            <div class="info-value df">{{ $service->default_df_rate ? number_format($service->default_df_rate, 0) : '-' }}</div>
                            <div class="info-label">ค่ามือ PT</div>
                        </div>
                        <div class="info-item">
                            <div class="info-value">{{ $service->default_duration_minutes ?? '-' }}</div>
                            <div class="info-label">นาที</div>
                        </div>
                    </div>
                </div>

                <div class="service-footer">
                    <button type="button" class="btn-action btn-edit" onclick="openEditModal('{{ $service->id }}')">
                        <i class="bi bi-pencil"></i> แก้ไข
                    </button>
                    <button type="button" class="btn-action btn-delete" onclick="confirmDelete('{{ $service->id }}', '{{ $service->name }}')">
                        <i class="bi bi-trash"></i> ลบ
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        @if($services->hasPages())
        <div class="pagination-wrapper">
            {{ $services->links() }}
        </div>
        @endif
        @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="bi bi-clipboard2-x"></i>
            </div>
            <h3>ยังไม่มีบริการ</h3>
            <p>เริ่มต้นเพิ่มบริการแรกของคุณ</p>
            <button class="btn-add-new" style="background: var(--sv-primary); border: none;" data-bs-toggle="modal" data-bs-target="#serviceModal" onclick="openCreateModal()">
                <i class="bi bi-plus-lg me-2"></i>เพิ่มบริการใหม่
            </button>
        </div>
        @endif
    </div>
</div>

<!-- Create/Edit Modal -->
<div class="modal fade" id="serviceModal" tabindex="-1" aria-labelledby="serviceModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="serviceForm" autocomplete="off">
                @csrf
                <input type="hidden" id="serviceId" name="service_id">

                <div class="modal-header">
                    <h5 class="modal-title" id="serviceModalLabel">
                        <i class="bi bi-clipboard2-pulse"></i>
                        <span id="modalTitle">เพิ่มบริการใหม่</span>
                    </h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>

                <div class="modal-body">
                    <!-- Basic Info Section -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="bi bi-info-circle"></i>
                            ข้อมูลพื้นฐาน
                        </div>
                        <div class="row g-3">
                            <div class="col-md-8">
                                <label for="name" class="form-label">ชื่อบริการ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="เช่น กายภาพบำบัด" required>
                            </div>
                            <div class="col-md-4">
                                <label for="code_number" class="form-label">รหัส</label>
                                <div class="input-group">
                                    <span class="input-group-text">GSR</span>
                                    <input type="text" class="form-control" id="code_number" name="code_number" placeholder="001">
                                </div>
                                <input type="hidden" id="code" name="code">
                            </div>
                            <div class="col-md-6">
                                <label for="category_id" class="form-label">หมวดหมู่ <span class="text-danger">*</span></label>
                                <select class="form-select" id="category_id" name="category_id" required>
                                    <option value="">-- เลือกหมวดหมู่ --</option>
                                    @foreach($categories as $category)
                                    <option value="{{ $category->id }}">{{ $category->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="default_price" class="form-label">ราคา (บาท) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="default_price" name="default_price" step="0.01" min="0" placeholder="0.00" required>
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label">รายละเอียด</label>
                                <textarea class="form-control" id="description" name="description" rows="2" placeholder="รายละเอียดเพิ่มเติม (ถ้ามี)"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Settings Section -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="bi bi-gear"></i>
                            การตั้งค่า
                        </div>
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="default_duration_minutes" class="form-label">ระยะเวลา (นาที)</label>
                                <input type="number" class="form-control" id="default_duration_minutes" name="default_duration_minutes" min="1" placeholder="60">
                            </div>
                            <div class="col-md-6">
                                <label for="default_df_rate" class="form-label">ค่ามือ PT (บาท)</label>
                                <input type="number" class="form-control" id="default_df_rate" name="default_df_rate" step="0.01" min="0" placeholder="0">
                                <div class="form-hint">จำนวนเงินที่ PT ได้รับต่อครั้ง</div>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked style="width: 3em; height: 1.5em;">
                        <label class="form-check-label ms-2" for="is_active">
                            <strong>เปิดใช้งานบริการ</strong>
                        </label>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">
                        <i class="bi bi-x-lg me-1"></i>ยกเลิก
                    </button>
                    <button type="submit" class="btn btn-save">
                        <i class="bi bi-check-lg me-1"></i>บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade delete-modal" id="deleteModal" tabindex="-1" aria-labelledby="deleteModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="deleteModalLabel">
                    <i class="bi bi-exclamation-triangle"></i>
                    ยืนยันการลบ
                </h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="delete-warning">
                    <div class="delete-warning-icon">
                        <i class="bi bi-trash"></i>
                    </div>
                    <h5>คุณต้องการลบบริการนี้?</h5>
                    <p class="text-muted mb-0">
                        <strong id="deleteServiceName" class="text-danger"></strong>
                    </p>
                    <p class="text-muted small mt-2">การดำเนินการนี้ไม่สามารถย้อนกลับได้</p>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i>ยกเลิก
                </button>
                <button type="button" class="btn btn-delete-confirm" id="confirmDeleteBtn">
                    <i class="bi bi-trash me-1"></i>ยืนยันลบ
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const serviceModal = new bootstrap.Modal(document.getElementById('serviceModal'));
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    let deleteServiceId = null;

    // Base URL for API calls
    const baseUrl = '{{ url("/") }}';

    // Open create modal
    window.openCreateModal = function() {
        document.getElementById('serviceForm').reset();
        document.getElementById('serviceId').value = '';
        document.getElementById('code_number').value = '';
        document.getElementById('modalTitle').textContent = 'เพิ่มบริการใหม่';
        document.getElementById('is_active').checked = true;
    };

    // Open edit modal
    window.openEditModal = function(id) {
        fetch(`${baseUrl}/services/${id}/edit`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('serviceId').value = data.id;
            document.getElementById('name').value = data.name;

            // Extract number from GSR code
            let codeNumber = data.code || '';
            if (codeNumber.startsWith('GSR')) {
                codeNumber = codeNumber.substring(3);
            }
            document.getElementById('code_number').value = codeNumber;
            document.getElementById('category_id').value = data.category_id || '';
            document.getElementById('default_price').value = data.default_price;
            document.getElementById('default_duration_minutes').value = data.default_duration_minutes || '';
            document.getElementById('default_df_rate').value = data.default_df_rate || '';
            document.getElementById('description').value = data.description || '';
            document.getElementById('is_active').checked = data.is_active;

            document.getElementById('modalTitle').textContent = 'แก้ไขบริการ';
            serviceModal.show();
        })
        .catch(error => {
            showAlert('เกิดข้อผิดพลาดในการโหลดข้อมูล', 'danger');
        });
    };

    // Form submit
    document.getElementById('serviceForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const serviceId = document.getElementById('serviceId').value;
        const formData = new FormData(this);

        // Combine GSR prefix with number
        const codeNumber = document.getElementById('code_number').value.trim();
        if (codeNumber) {
            formData.set('code', 'GSR' + codeNumber);
        } else {
            formData.set('code', '');
        }

        let url = `${baseUrl}/services`;

        if (serviceId) {
            url = `${baseUrl}/services/${serviceId}`;
            formData.append('_method', 'PUT');
        }

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => { throw err; });
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                serviceModal.hide();
                showAlert(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert('เกิดข้อผิดพลาด', 'danger');
            }
        })
        .catch(error => {
            if (error.errors) {
                const messages = Object.values(error.errors).flat().join('<br>');
                showAlert(messages, 'danger');
            } else {
                showAlert('เกิดข้อผิดพลาดในการบันทึกข้อมูล', 'danger');
            }
        });
    });

    // Confirm delete
    window.confirmDelete = function(id, name) {
        deleteServiceId = id;
        document.getElementById('deleteServiceName').textContent = name;
        deleteModal.show();
    };

    // Delete service
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (!deleteServiceId) return;

        fetch(`${baseUrl}/services/${deleteServiceId}`, {
            method: 'DELETE',
            headers: {
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                deleteModal.hide();
                showAlert(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert('เกิดข้อผิดพลาดในการลบ', 'danger');
            }
        })
        .catch(error => {
            showAlert('เกิดข้อผิดพลาดในการลบข้อมูล', 'danger');
        });
    });

    // Search and Filter functionality
    function applyFilters() {
        const searchTerm = document.getElementById('searchInput').value.toLowerCase().trim();
        const categoryFilter = document.getElementById('categoryFilter').value.toLowerCase();
        const statusFilter = document.getElementById('statusFilter').value.toLowerCase();

        const cards = document.querySelectorAll('.service-card');

        cards.forEach(card => {
            const searchData = card.getAttribute('data-search') || '';
            const cardCategory = card.getAttribute('data-category') || '';
            const cardStatus = card.getAttribute('data-status') || '';

            let showCard = true;

            // Search filter
            if (searchTerm && !searchData.includes(searchTerm)) {
                showCard = false;
            }

            // Category filter
            if (categoryFilter && cardCategory !== categoryFilter) {
                showCard = false;
            }

            // Status filter
            if (statusFilter && cardStatus !== statusFilter) {
                showCard = false;
            }

            card.style.display = showCard ? '' : 'none';
        });
    }

    document.getElementById('searchInput').addEventListener('input', applyFilters);
    document.getElementById('categoryFilter').addEventListener('change', applyFilters);
    document.getElementById('statusFilter').addEventListener('change', applyFilters);

    // Show alert
    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `alert alert-${type} alert-float alert-dismissible fade show`;
        alertDiv.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        `;
        document.body.appendChild(alertDiv);

        setTimeout(() => {
            alertDiv.classList.remove('show');
            setTimeout(() => alertDiv.remove(), 300);
        }, 4000);
    }

    // Auto-hide alerts on page load
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-float');
        alerts.forEach(alert => {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 300);
        });
    }, 4000);
});
</script>
@endpush
