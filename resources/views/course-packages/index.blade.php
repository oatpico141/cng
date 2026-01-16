@extends('layouts.app')

@section('title', 'จัดการแพ็คเกจคอร์ส')

@push('styles')
<style>
    /* ===== CSS Variables ===== */
    :root {
        --cp-primary: #0ea5e9;
        --cp-primary-dark: #0284c7;
        --cp-secondary: #6366f1;
        --cp-success: #10b981;
        --cp-warning: #f59e0b;
        --cp-danger: #ef4444;
        --cp-gray-50: #f8fafc;
        --cp-gray-100: #f1f5f9;
        --cp-gray-200: #e2e8f0;
        --cp-gray-300: #cbd5e1;
        --cp-gray-500: #64748b;
        --cp-gray-600: #475569;
        --cp-gray-700: #334155;
        --cp-gray-800: #1e293b;
        --cp-shadow: 0 1px 3px rgba(0,0,0,0.1), 0 1px 2px rgba(0,0,0,0.06);
        --cp-shadow-md: 0 4px 6px -1px rgba(0,0,0,0.1), 0 2px 4px -1px rgba(0,0,0,0.06);
        --cp-shadow-lg: 0 10px 15px -3px rgba(0,0,0,0.1), 0 4px 6px -2px rgba(0,0,0,0.05);
        --cp-radius: 16px;
        --cp-radius-sm: 10px;
        --cp-radius-xs: 6px;
    }

    /* ===== Page Header ===== */
    .page-header {
        background: linear-gradient(135deg, #0ea5e9 0%, #6366f1 100%);
        border-radius: var(--cp-radius);
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
        border-radius: var(--cp-radius-sm);
        font-weight: 600;
        transition: all 0.3s ease;
        position: relative;
        z-index: 1;
    }

    .btn-add-new:hover {
        background: white;
        color: var(--cp-primary);
        transform: translateY(-2px);
        box-shadow: var(--cp-shadow-lg);
    }

    /* ===== Stats Cards ===== */
    .stats-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: white;
        border-radius: var(--cp-radius-sm);
        padding: 1.25rem;
        box-shadow: var(--cp-shadow);
        border: 1px solid var(--cp-gray-100);
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

    .stat-card.total::before { background: linear-gradient(180deg, var(--cp-primary), var(--cp-secondary)); }
    .stat-card.active::before { background: linear-gradient(180deg, var(--cp-success), #34d399); }
    .stat-card.inactive::before { background: linear-gradient(180deg, var(--cp-gray-400), var(--cp-gray-500)); }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: var(--cp-shadow-md);
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: var(--cp-radius-xs);
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
    }

    .stat-card.total .stat-icon { background: linear-gradient(135deg, rgba(14, 165, 233, 0.15), rgba(99, 102, 241, 0.15)); color: var(--cp-primary); }
    .stat-card.active .stat-icon { background: rgba(16, 185, 129, 0.15); color: var(--cp-success); }
    .stat-card.inactive .stat-icon { background: rgba(100, 116, 139, 0.15); color: var(--cp-gray-500); }

    .stat-value {
        font-size: 1.75rem;
        font-weight: 700;
        color: var(--cp-gray-800);
        line-height: 1;
    }

    .stat-label {
        font-size: 0.8rem;
        color: var(--cp-gray-500);
        margin-top: 0.25rem;
    }

    /* ===== Main Card ===== */
    .main-card {
        background: white;
        border-radius: var(--cp-radius);
        box-shadow: var(--cp-shadow);
        border: 1px solid var(--cp-gray-100);
        overflow: hidden;
    }

    .card-toolbar {
        padding: 1.25rem 1.5rem;
        background: var(--cp-gray-50);
        border-bottom: 1px solid var(--cp-gray-200);
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
    }

    .card-title {
        font-size: 1.1rem;
        font-weight: 600;
        color: var(--cp-gray-800);
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0;
    }

    .card-title i {
        color: var(--cp-primary);
    }

    /* ===== Search Box ===== */
    .search-box {
        position: relative;
        width: 280px;
    }

    .search-box input {
        width: 100%;
        padding: 0.625rem 1rem 0.625rem 2.5rem;
        border: 1px solid var(--cp-gray-200);
        border-radius: 50px;
        font-size: 0.875rem;
        transition: all 0.3s ease;
        background: white;
    }

    .search-box input:focus {
        outline: none;
        border-color: var(--cp-primary);
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
    }

    .search-box i {
        position: absolute;
        left: 1rem;
        top: 50%;
        transform: translateY(-50%);
        color: var(--cp-gray-400);
    }

    /* ===== Package Grid ===== */
    .packages-grid {
        display: grid;
        grid-template-columns: repeat(auto-fill, minmax(340px, 1fr));
        gap: 1.25rem;
        padding: 1.5rem;
    }

    .package-card {
        background: white;
        border-radius: var(--cp-radius-sm);
        border: 1px solid var(--cp-gray-200);
        transition: all 0.3s ease;
        position: relative;
        overflow: hidden;
    }

    .package-card:hover {
        border-color: var(--cp-primary);
        box-shadow: var(--cp-shadow-md);
        transform: translateY(-2px);
    }

    .package-card.inactive {
        opacity: 0.7;
    }

    .package-header {
        padding: 1.25rem;
        background: linear-gradient(135deg, var(--cp-gray-50), white);
        border-bottom: 1px solid var(--cp-gray-100);
        position: relative;
    }

    .package-status {
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

    .package-status.active {
        background: rgba(16, 185, 129, 0.15);
        color: var(--cp-success);
    }

    .package-status.inactive {
        background: rgba(100, 116, 139, 0.15);
        color: var(--cp-gray-500);
    }

    .package-code {
        display: inline-block;
        padding: 0.25rem 0.625rem;
        background: linear-gradient(135deg, var(--cp-primary), var(--cp-secondary));
        color: white;
        border-radius: var(--cp-radius-xs);
        font-size: 0.7rem;
        font-weight: 600;
        letter-spacing: 0.5px;
        margin-bottom: 0.5rem;
    }

    .package-name {
        font-size: 1.1rem;
        font-weight: 700;
        color: var(--cp-gray-800);
        margin-bottom: 0.25rem;
        padding-right: 70px;
    }

    .package-service {
        display: inline-flex;
        align-items: center;
        gap: 0.375rem;
        padding: 0.25rem 0.625rem;
        background: rgba(14, 165, 233, 0.1);
        color: var(--cp-primary-dark);
        border-radius: var(--cp-radius-xs);
        font-size: 0.75rem;
        font-weight: 500;
    }

    .package-body {
        padding: 1.25rem;
    }

    .package-info-grid {
        display: grid;
        grid-template-columns: repeat(2, 1fr);
        gap: 1rem;
        margin-bottom: 1rem;
    }

    .info-item {
        text-align: center;
        padding: 0.75rem;
        background: var(--cp-gray-50);
        border-radius: var(--cp-radius-xs);
    }

    .info-item.highlight {
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.1), rgba(99, 102, 241, 0.1));
    }

    .info-value {
        font-size: 1.25rem;
        font-weight: 700;
        color: var(--cp-gray-800);
    }

    .info-value.price {
        color: var(--cp-primary);
    }

    .info-label {
        font-size: 0.7rem;
        color: var(--cp-gray-500);
        text-transform: uppercase;
        letter-spacing: 0.5px;
        margin-top: 0.25rem;
    }

    .commission-section {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 0.5rem;
        padding-top: 1rem;
        border-top: 1px dashed var(--cp-gray-200);
    }

    .commission-item {
        text-align: center;
        padding: 0.5rem;
        background: var(--cp-gray-50);
        border-radius: var(--cp-radius-xs);
    }

    .commission-value {
        font-size: 0.9rem;
        font-weight: 600;
        color: var(--cp-warning);
    }

    .commission-label {
        font-size: 0.65rem;
        color: var(--cp-gray-500);
        margin-top: 0.125rem;
    }

    .package-footer {
        padding: 1rem 1.25rem;
        background: var(--cp-gray-50);
        border-top: 1px solid var(--cp-gray-100);
        display: flex;
        justify-content: flex-end;
        gap: 0.5rem;
    }

    .btn-action {
        padding: 0.5rem 1rem;
        border-radius: var(--cp-radius-xs);
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
        color: var(--cp-primary);
    }

    .btn-edit:hover {
        background: var(--cp-primary);
        color: white;
    }

    .btn-delete {
        background: rgba(239, 68, 68, 0.1);
        color: var(--cp-danger);
    }

    .btn-delete:hover {
        background: var(--cp-danger);
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
        background: linear-gradient(135deg, var(--cp-gray-100), var(--cp-gray-200));
        border-radius: 50%;
        display: flex;
        align-items: center;
        justify-content: center;
        margin: 0 auto 1.5rem;
    }

    .empty-state-icon i {
        font-size: 2.5rem;
        color: var(--cp-gray-400);
    }

    .empty-state h3 {
        font-size: 1.25rem;
        color: var(--cp-gray-700);
        margin-bottom: 0.5rem;
    }

    .empty-state p {
        color: var(--cp-gray-500);
        margin-bottom: 1.5rem;
    }

    /* ===== Modal Styles ===== */
    .modal-content {
        border: none;
        border-radius: var(--cp-radius);
        overflow: hidden;
    }

    .modal-header {
        background: linear-gradient(135deg, var(--cp-primary), var(--cp-secondary));
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
        color: var(--cp-gray-600);
        margin-bottom: 1rem;
        padding-bottom: 0.5rem;
        border-bottom: 2px solid var(--cp-gray-100);
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-section-title i {
        color: var(--cp-primary);
    }

    .form-label {
        font-size: 0.85rem;
        font-weight: 500;
        color: var(--cp-gray-700);
        margin-bottom: 0.375rem;
    }

    .form-control, .form-select {
        border: 1px solid var(--cp-gray-200);
        border-radius: var(--cp-radius-xs);
        padding: 0.625rem 0.875rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: var(--cp-primary);
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.15);
    }

    .input-group-text {
        background: var(--cp-gray-100);
        border: 1px solid var(--cp-gray-200);
        border-radius: var(--cp-radius-xs);
        font-weight: 600;
        color: var(--cp-gray-600);
    }

    .form-hint {
        font-size: 0.75rem;
        color: var(--cp-gray-500);
        margin-top: 0.25rem;
    }

    .total-display {
        background: linear-gradient(135deg, rgba(14, 165, 233, 0.1), rgba(99, 102, 241, 0.1));
        border: 1px solid rgba(14, 165, 233, 0.2);
        border-radius: var(--cp-radius-xs);
        padding: 0.625rem 0.875rem;
        font-size: 1rem;
        font-weight: 700;
        color: var(--cp-primary);
        text-align: center;
    }

    .modal-footer {
        background: var(--cp-gray-50);
        border-top: 1px solid var(--cp-gray-200);
        padding: 1rem 1.5rem;
    }

    .btn-save {
        background: linear-gradient(135deg, var(--cp-primary), var(--cp-secondary));
        border: none;
        color: white;
        padding: 0.625rem 1.5rem;
        border-radius: var(--cp-radius-xs);
        font-weight: 600;
        transition: all 0.3s ease;
    }

    .btn-save:hover {
        transform: translateY(-1px);
        box-shadow: var(--cp-shadow-md);
        color: white;
    }

    .btn-cancel {
        background: white;
        border: 1px solid var(--cp-gray-300);
        color: var(--cp-gray-600);
        padding: 0.625rem 1.5rem;
        border-radius: var(--cp-radius-xs);
        font-weight: 500;
    }

    .btn-cancel:hover {
        background: var(--cp-gray-100);
    }

    /* ===== Delete Modal ===== */
    .delete-modal .modal-header {
        background: linear-gradient(135deg, var(--cp-danger), #dc2626);
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
        color: var(--cp-danger);
    }

    .btn-delete-confirm {
        background: var(--cp-danger);
        border: none;
        color: white;
        padding: 0.625rem 1.5rem;
        border-radius: var(--cp-radius-xs);
        font-weight: 600;
    }

    .btn-delete-confirm:hover {
        background: #dc2626;
        color: white;
    }

    /* ===== Pagination ===== */
    .pagination-wrapper {
        padding: 1rem 1.5rem;
        background: var(--cp-gray-50);
        border-top: 1px solid var(--cp-gray-200);
        display: flex;
        justify-content: center;
        align-items: center;
    }

    .pagination-wrapper nav {
        display: flex;
        flex-direction: column;
        align-items: center;
        gap: 0.75rem;
        width: auto;
    }

    /* Target Tailwind pagination wrapper */
    .pagination-wrapper nav > div {
        display: flex;
        flex-wrap: wrap;
        align-items: center;
        justify-content: center;
        gap: 0.5rem;
    }

    /* Hide Tailwind's info text on mobile - keep only pagination buttons */
    .pagination-wrapper nav > div.hidden {
        display: none !important;
    }

    .pagination-wrapper .pagination,
    .pagination-wrapper nav > div:first-child {
        display: flex;
        align-items: center;
        gap: 0.25rem;
        margin: 0;
    }

    /* All clickable pagination elements */
    .pagination-wrapper .page-link,
    .pagination-wrapper nav a,
    .pagination-wrapper nav span[aria-current],
    .pagination-wrapper nav > div > span > a,
    .pagination-wrapper nav > div > a {
        display: inline-flex !important;
        align-items: center !important;
        justify-content: center !important;
        min-width: 36px !important;
        height: 36px !important;
        padding: 0.5rem 0.75rem !important;
        font-size: 0.875rem !important;
        font-weight: 500 !important;
        color: var(--cp-gray-600) !important;
        background: white !important;
        border: 1px solid var(--cp-gray-200) !important;
        border-radius: var(--cp-radius-xs) !important;
        text-decoration: none !important;
        transition: all 0.2s ease !important;
        line-height: 1 !important;
    }

    .pagination-wrapper .page-link:hover,
    .pagination-wrapper nav a:hover,
    .pagination-wrapper nav > div > span > a:hover,
    .pagination-wrapper nav > div > a:hover {
        background: var(--cp-primary) !important;
        border-color: var(--cp-primary) !important;
        color: white !important;
    }

    .pagination-wrapper .page-item.active .page-link,
    .pagination-wrapper nav span[aria-current] {
        background: linear-gradient(135deg, var(--cp-primary), var(--cp-secondary)) !important;
        border-color: var(--cp-primary) !important;
        color: white !important;
    }

    .pagination-wrapper .page-item.disabled .page-link {
        color: var(--cp-gray-300) !important;
        pointer-events: none !important;
        background: var(--cp-gray-50) !important;
    }

    /* Critical SVG fixes - force all SVG icons to proper size */
    .pagination-wrapper svg,
    .pagination-wrapper .page-link svg,
    .pagination-wrapper nav svg,
    .pagination-wrapper nav a svg,
    .pagination-wrapper nav > div svg,
    .pagination-wrapper nav > div > a svg,
    .pagination-wrapper nav > div > span svg {
        width: 16px !important;
        height: 16px !important;
        min-width: 16px !important;
        max-width: 16px !important;
        min-height: 16px !important;
        max-height: 16px !important;
        flex-shrink: 0 !important;
        display: inline-block !important;
    }

    /* Info text styling */
    .pagination-wrapper p,
    .pagination-wrapper nav > div:last-child p,
    .pagination-wrapper nav > div p {
        margin: 0;
        font-size: 0.8rem;
        color: var(--cp-gray-500);
    }

    .pagination-wrapper nav > div:last-child {
        display: flex;
        justify-content: center;
    }

    /* Fix for Tailwind pagination spans that might contain text */
    .pagination-wrapper nav > div > span {
        display: inline-flex;
        align-items: center;
        gap: 0.25rem;
    }

    /* ===== Alert ===== */
    .alert-float {
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        min-width: 300px;
        border-radius: var(--cp-radius-sm);
        box-shadow: var(--cp-shadow-lg);
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
            grid-template-columns: 1fr;
        }

        .packages-grid {
            grid-template-columns: 1fr;
            padding: 1rem;
        }

        .card-toolbar {
            flex-direction: column;
            align-items: stretch;
        }

        .search-box {
            width: 100%;
        }

        .commission-section {
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
                <h1><i class="bi bi-box-seam me-2"></i>แพ็คเกจคอร์ส</h1>
                <p><i class="bi bi-building me-1"></i>จัดการแพ็คเกจคอร์สการรักษาทั้งหมด</p>
            </div>
            <button class="btn-add-new" data-bs-toggle="modal" data-bs-target="#packageModal" onclick="resetForm()">
                <i class="bi bi-plus-lg me-2"></i>เพิ่มแพ็คเกจใหม่
            </button>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="stats-grid">
        <div class="stat-card total">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-value">{{ $packages->total() }}</div>
                    <div class="stat-label">แพ็คเกจทั้งหมด</div>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-box-seam"></i>
                </div>
            </div>
        </div>
        <div class="stat-card active">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-value">{{ $packages->where('is_active', true)->count() }}</div>
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
                    <div class="stat-value">{{ $packages->where('is_active', false)->count() }}</div>
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

    <!-- Main Card -->
    <div class="main-card">
        <div class="card-toolbar">
            <h5 class="card-title">
                <i class="bi bi-grid-3x3-gap"></i>
                รายการแพ็คเกจ
            </h5>
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" placeholder="ค้นหาแพ็คเกจ...">
            </div>
        </div>

        @if($packages->count() > 0)
        <div class="packages-grid">
            @foreach($packages as $package)
            <div class="package-card {{ !$package->is_active ? 'inactive' : '' }}" data-search="{{ strtolower($package->name . ' ' . $package->code . ' ' . ($package->service->name ?? '')) }}">
                <div class="package-header">
                    <span class="package-status {{ $package->is_active ? 'active' : 'inactive' }}">
                        {{ $package->is_active ? 'ใช้งาน' : 'ปิด' }}
                    </span>
                    @if($package->code)
                    <span class="package-code">{{ $package->code }}</span>
                    @endif
                    <h3 class="package-name">{{ $package->name }}</h3>
                    @if($package->service)
                    <span class="package-service">
                        <i class="bi bi-heart-pulse"></i>
                        {{ $package->service->name }}
                    </span>
                    @endif
                </div>

                <div class="package-body">
                    <div class="package-info-grid">
                        <div class="info-item highlight">
                            <div class="info-value price">{{ number_format($package->price, 0) }}</div>
                            <div class="info-label">ราคา (บาท)</div>
                        </div>
                        <div class="info-item">
                            <div class="info-value">{{ $package->total_sessions }}</div>
                            <div class="info-label">จำนวนครั้ง</div>
                        </div>
                        @if($package->paid_sessions && $package->bonus_sessions)
                        <div class="info-item">
                            <div class="info-value">{{ $package->paid_sessions }}</div>
                            <div class="info-label">จ่าย</div>
                        </div>
                        <div class="info-item">
                            <div class="info-value">+{{ $package->bonus_sessions }}</div>
                            <div class="info-label">แถม</div>
                        </div>
                        @endif
                    </div>

                    <div class="commission-section">
                        <div class="commission-item">
                            <div class="commission-value">{{ $package->commission_rate ? number_format($package->commission_rate, 0) : '-' }}</div>
                            <div class="commission-label">คอม (เต็ม)</div>
                        </div>
                        <div class="commission-item">
                            <div class="commission-value">{{ $package->commission_installment ? number_format($package->commission_installment, 0) : '-' }}</div>
                            <div class="commission-label">คอม (ผ่อน)</div>
                        </div>
                        <div class="commission-item">
                            <div class="commission-value">{{ $package->df_amount ? number_format($package->df_amount, 0) : '-' }}</div>
                            <div class="commission-label">ค่ามือ/ครั้ง</div>
                        </div>
                    </div>
                </div>

                <div class="package-footer">
                    <button type="button" class="btn-action btn-edit"
                            onclick="editPackage({{ json_encode($package) }})"
                            data-bs-toggle="modal" data-bs-target="#packageModal">
                        <i class="bi bi-pencil"></i> แก้ไข
                    </button>
                    <button type="button" class="btn-action btn-delete"
                            onclick="confirmDelete('{{ $package->id }}', '{{ $package->name }}')"
                            data-bs-toggle="modal" data-bs-target="#deleteModal">
                        <i class="bi bi-trash"></i> ลบ
                    </button>
                </div>
            </div>
            @endforeach
        </div>

        @if($packages->hasPages())
        <div class="pagination-wrapper">
            {{ $packages->links() }}
        </div>
        @endif
        @else
        <div class="empty-state">
            <div class="empty-state-icon">
                <i class="bi bi-box-seam"></i>
            </div>
            <h3>ยังไม่มีแพ็คเกจคอร์ส</h3>
            <p>เริ่มต้นสร้างแพ็คเกจคอร์สแรกของคุณ</p>
            <button class="btn-add-new" style="background: var(--cp-primary); border: none;" data-bs-toggle="modal" data-bs-target="#packageModal" onclick="resetForm()">
                <i class="bi bi-plus-lg me-2"></i>สร้างแพ็คเกจใหม่
            </button>
        </div>
        @endif
    </div>
</div>

<!-- Create/Edit Modal -->
<div class="modal fade" id="packageModal" tabindex="-1" aria-labelledby="packageModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <form id="packageForm" method="POST" action="{{ url('course-packages') }}" autocomplete="off">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-header">
                    <h5 class="modal-title" id="packageModalLabel">
                        <i class="bi bi-box-seam"></i>
                        <span id="modalTitle">เพิ่มแพ็คเกจคอร์ส</span>
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
                                <label for="name" class="form-label">ชื่อแพ็คเกจ <span class="text-danger">*</span></label>
                                <input type="text" class="form-control" id="name" name="name" placeholder="เช่น กายภาพบำบัด M (5+1)" required>
                            </div>
                            <div class="col-md-4">
                                <label for="code_number" class="form-label">รหัส</label>
                                <div class="input-group">
                                    <span class="input-group-text">CGSR</span>
                                    <input type="text" class="form-control" id="code_number" name="code_number" placeholder="001">
                                </div>
                                <input type="hidden" id="code" name="code">
                            </div>
                            <div class="col-md-6">
                                <label for="service_id" class="form-label">บริการหลัก <span class="text-danger">*</span></label>
                                <select class="form-select" id="service_id" name="service_id" required>
                                    <option value="">-- เลือกบริการ --</option>
                                    @foreach($services as $service)
                                        <option value="{{ $service->id }}">{{ $service->name }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-6">
                                <label for="price" class="form-label">ราคา (บาท) <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="price" name="price" step="0.01" min="0" placeholder="0.00" required>
                            </div>
                            <div class="col-12">
                                <label for="description" class="form-label">รายละเอียด</label>
                                <textarea class="form-control" id="description" name="description" rows="2" placeholder="รายละเอียดเพิ่มเติม (ถ้ามี)"></textarea>
                            </div>
                        </div>
                    </div>

                    <!-- Sessions Section -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="bi bi-calendar-check"></i>
                            จำนวนครั้งการใช้งาน
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="paid_sessions" class="form-label">จ่าย <span class="text-danger">*</span></label>
                                <input type="number" class="form-control" id="paid_sessions" name="paid_sessions" min="1" required oninput="updateTotalSessions()">
                                <div class="form-hint">จำนวนครั้งที่ลูกค้าจ่าย</div>
                            </div>
                            <div class="col-md-4">
                                <label for="bonus_sessions" class="form-label">แถม</label>
                                <input type="number" class="form-control" id="bonus_sessions" name="bonus_sessions" min="0" value="0" oninput="updateTotalSessions()">
                                <div class="form-hint">จำนวนครั้งที่แถมให้</div>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label">รวมทั้งหมด</label>
                                <div class="total-display" id="total_display">0 ครั้ง</div>
                            </div>
                        </div>
                    </div>

                    <!-- Commission Section -->
                    <div class="form-section">
                        <div class="form-section-title">
                            <i class="bi bi-cash-coin"></i>
                            ค่าตอบแทน
                        </div>
                        <div class="row g-3">
                            <div class="col-md-4">
                                <label for="commission_rate" class="form-label">คอมขาย (เต็ม)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-currency-exchange"></i></span>
                                    <input type="number" class="form-control" id="commission_rate" name="commission_rate" step="0.01" min="0" placeholder="0">
                                </div>
                                <div class="form-hint">เมื่อลูกค้าชำระเต็ม</div>
                            </div>
                            <div class="col-md-4">
                                <label for="commission_installment" class="form-label">คอมขาย (ผ่อน)</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-currency-exchange"></i></span>
                                    <input type="number" class="form-control" id="commission_installment" name="commission_installment" step="0.01" min="0" placeholder="0">
                                </div>
                                <div class="form-hint">เมื่อลูกค้าผ่อนชำระ</div>
                            </div>
                            <div class="col-md-4">
                                <label for="per_session_commission_rate" class="form-label">ค่ามือ/ครั้ง</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="bi bi-hand-index"></i></span>
                                    <input type="number" class="form-control" id="per_session_commission_rate" name="per_session_commission_rate" step="0.01" min="0" placeholder="0">
                                </div>
                                <div class="form-hint">ผู้ทำหัตถการได้รับ</div>
                            </div>
                        </div>
                    </div>

                    <!-- Status -->
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked style="width: 3em; height: 1.5em;">
                        <label class="form-check-label ms-2" for="is_active">
                            <strong>เปิดใช้งานแพ็คเกจ</strong>
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
                    <h5>คุณต้องการลบแพ็คเกจนี้?</h5>
                    <p class="text-muted mb-0">
                        <strong id="deletePackageName" class="text-danger"></strong>
                    </p>
                    <p class="text-muted small mt-2">การดำเนินการนี้ไม่สามารถย้อนกลับได้</p>
                </div>
            </div>
            <div class="modal-footer justify-content-center">
                <button type="button" class="btn btn-cancel" data-bs-dismiss="modal">
                    <i class="bi bi-x-lg me-1"></i>ยกเลิก
                </button>
                <form id="deleteForm" method="POST" class="d-inline">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-delete-confirm">
                        <i class="bi bi-trash me-1"></i>ยืนยันลบ
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Base URL for form actions
    const baseUrl = '{{ url("/") }}';

    function resetForm() {
        document.getElementById('packageForm').reset();
        document.getElementById('packageForm').action = baseUrl + "/course-packages";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('modalTitle').textContent = 'เพิ่มแพ็คเกจคอร์ส';
        document.getElementById('is_active').checked = true;
        document.getElementById('total_display').textContent = '0 ครั้ง';
        document.getElementById('code_number').value = '';
    }

    // Combine CGSR prefix with number before submit
    document.getElementById('packageForm').addEventListener('submit', function(e) {
        const codeNumber = document.getElementById('code_number').value.trim();
        if (codeNumber) {
            document.getElementById('code').value = 'CGSR' + codeNumber;
        } else {
            document.getElementById('code').value = '';
        }
    });

    function editPackage(pkg) {
        document.getElementById('packageForm').action = baseUrl + "/course-packages/" + pkg.id;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('modalTitle').textContent = 'แก้ไขแพ็คเกจคอร์ส';

        document.getElementById('name').value = pkg.name || '';
        // Extract number from CGSR code (e.g., CGSR001 -> 001)
        let codeNumber = pkg.code || '';
        if (codeNumber.startsWith('CGSR')) {
            codeNumber = codeNumber.substring(4);
        }
        document.getElementById('code_number').value = codeNumber;
        document.getElementById('description').value = pkg.description || '';
        document.getElementById('service_id').value = pkg.service_id || '';
        document.getElementById('price').value = pkg.price || '';

        // Set paid and bonus sessions
        const paidSessions = pkg.paid_sessions || pkg.total_sessions || 0;
        const bonusSessions = pkg.bonus_sessions || 0;
        document.getElementById('paid_sessions').value = paidSessions;
        document.getElementById('bonus_sessions').value = bonusSessions;
        updateTotalSessions();

        document.getElementById('commission_rate').value = pkg.commission_rate || '';
        document.getElementById('commission_installment').value = pkg.commission_installment || '';
        document.getElementById('per_session_commission_rate').value = pkg.df_amount || pkg.per_session_commission_rate || '';
        document.getElementById('is_active').checked = pkg.is_active;
    }

    function updateTotalSessions() {
        const paid = parseInt(document.getElementById('paid_sessions').value) || 0;
        const bonus = parseInt(document.getElementById('bonus_sessions').value) || 0;
        const total = paid + bonus;
        document.getElementById('total_display').textContent = total + ' ครั้ง';
    }

    function confirmDelete(id, name) {
        document.getElementById('deleteForm').action = baseUrl + "/course-packages/" + id;
        document.getElementById('deletePackageName').textContent = name;
    }

    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchValue = this.value.toLowerCase().trim();
        const cards = document.querySelectorAll('.package-card');

        cards.forEach(card => {
            const searchData = card.getAttribute('data-search') || '';
            if (searchData.includes(searchValue)) {
                card.style.display = '';
            } else {
                card.style.display = 'none';
            }
        });
    });

    // Auto-hide alerts
    setTimeout(function() {
        const alerts = document.querySelectorAll('.alert-float');
        alerts.forEach(alert => {
            alert.classList.remove('show');
            setTimeout(() => alert.remove(), 300);
        });
    }, 4000);
</script>
@endpush
