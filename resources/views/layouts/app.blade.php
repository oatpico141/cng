<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    @auth
    <meta name="user-id" content="{{ auth()->id() }}">
    @endauth
    <title>@yield('title', 'CNG Clinic - ระบบจัดการคลินิก')</title>

    <!-- Thai Font (Kanit) -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">

    @stack('styles')

    <style>
        /* ========================================
           CNG CLINIC - MODERN BLUE THEME 2024
           ======================================== */

        :root {
            /* Primary Colors */
            --primary-50: #f0f9ff;
            --primary-100: #e0f2fe;
            --primary-200: #bae6fd;
            --primary-300: #7dd3fc;
            --primary-400: #38bdf8;
            --primary-500: #0ea5e9;
            --primary-600: #0284c7;
            --primary-700: #0369a1;
            --primary-800: #075985;
            --primary-900: #0c4a6e;

            /* Neutral Colors */
            --gray-50: #f8fafc;
            --gray-100: #f1f5f9;
            --gray-200: #e2e8f0;
            --gray-300: #cbd5e1;
            --gray-400: #94a3b8;
            --gray-500: #64748b;
            --gray-600: #475569;
            --gray-700: #334155;
            --gray-800: #1e293b;
            --gray-900: #0f172a;

            /* Sidebar */
            --sidebar-width: 280px;
            --sidebar-collapsed: 80px;
        }

        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Kanit', -apple-system, BlinkMacSystemFont, 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, var(--gray-100) 0%, var(--primary-50) 100%);
            min-height: 100vh;
            color: var(--gray-800);
        }

        /* ========================================
           SIDEBAR - Glass Morphism Design
           ======================================== */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            height: 100vh;
            width: var(--sidebar-width);
            background: linear-gradient(180deg, #ffffff 0%, #f8fafc 100%);
            padding: 0;
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            z-index: 1000;
            box-shadow: 4px 0 24px rgba(0, 0, 0, 0.06);
            border-right: 1px solid var(--gray-200);
            display: flex;
            flex-direction: column;
            overflow: hidden;
        }

        /* Logo Section */
        .sidebar-header {
            padding: 24px;
            background: linear-gradient(135deg, var(--primary-600) 0%, var(--primary-500) 50%, var(--primary-400) 100%);
            position: relative;
            overflow: hidden;
        }

        .sidebar-header::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -50%;
            width: 100%;
            height: 200%;
            background: radial-gradient(circle, rgba(255,255,255,0.15) 0%, transparent 70%);
            pointer-events: none;
        }

        .sidebar-header::after {
            content: '';
            position: absolute;
            bottom: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, var(--primary-300), var(--primary-200), var(--primary-300));
        }

        .sidebar-brand {
            display: flex;
            align-items: center;
            gap: 12px;
            position: relative;
            z-index: 1;
        }

        .sidebar-brand-icon {
            width: 48px;
            height: 48px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.5rem;
            color: white;
            border: 1px solid rgba(255, 255, 255, 0.3);
        }

        .sidebar-brand-text h5 {
            color: white;
            font-size: 1.25rem;
            font-weight: 700;
            margin: 0;
            text-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .sidebar-brand-text small {
            color: rgba(255, 255, 255, 0.9);
            font-size: 0.75rem;
            font-weight: 400;
        }

        /* Navigation */
        .sidebar-nav {
            flex: 1;
            overflow-y: auto;
            padding: 16px 12px;
            scrollbar-width: thin;
            scrollbar-color: var(--gray-300) transparent;
        }

        .sidebar-nav::-webkit-scrollbar {
            width: 5px;
        }

        .sidebar-nav::-webkit-scrollbar-track {
            background: transparent;
        }

        .sidebar-nav::-webkit-scrollbar-thumb {
            background: var(--gray-300);
            border-radius: 10px;
        }

        .nav-section {
            margin-bottom: 8px;
        }

        .nav-section-title {
            font-size: 0.7rem;
            font-weight: 600;
            color: var(--gray-400);
            text-transform: uppercase;
            letter-spacing: 0.1em;
            padding: 12px 16px 8px;
            margin-top: 8px;
        }

        .sidebar .nav-link {
            display: flex;
            align-items: center;
            gap: 12px;
            color: var(--gray-600);
            padding: 12px 16px;
            margin: 2px 0;
            border-radius: 12px;
            transition: all 0.2s ease;
            font-size: 0.9rem;
            font-weight: 500;
            position: relative;
            text-decoration: none;
        }

        .sidebar .nav-link i {
            font-size: 1.1rem;
            width: 24px;
            text-align: center;
            color: var(--gray-500);
            transition: all 0.2s ease;
        }

        .sidebar .nav-link:hover {
            background: var(--primary-50);
            color: var(--primary-700);
            transform: translateX(4px);
        }

        .sidebar .nav-link:hover i {
            color: var(--primary-600);
        }

        .sidebar .nav-link.active {
            background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-600) 100%);
            color: white;
            font-weight: 600;
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
        }

        .sidebar .nav-link.active i {
            color: white;
        }

        .sidebar .nav-link.active::before {
            content: '';
            position: absolute;
            left: 0;
            top: 50%;
            transform: translateY(-50%);
            width: 4px;
            height: 60%;
            background: white;
            border-radius: 0 4px 4px 0;
        }

        /* Submenu */
        .nav-submenu {
            background: var(--gray-50);
            border-radius: 12px;
            margin: 4px 0 8px 0;
            padding: 8px;
            border: 1px solid var(--gray-200);
        }

        .nav-submenu .nav-link {
            font-size: 0.85rem;
            padding: 10px 12px 10px 20px;
            color: var(--gray-500);
            margin: 1px 0;
        }

        .nav-submenu .nav-link:hover {
            background: white;
            color: var(--primary-600);
        }

        .nav-submenu .nav-link.active {
            background: var(--primary-100);
            color: var(--primary-700);
            box-shadow: none;
        }

        .nav-submenu .nav-link.active::before {
            display: none;
        }

        .nav-submenu .nav-link i {
            font-size: 0.9rem;
        }

        /* Sidebar Divider */
        .sidebar-divider {
            height: 1px;
            background: linear-gradient(90deg, transparent, var(--gray-200), transparent);
            margin: 12px 16px;
        }

        /* ========================================
           TOP NAVBAR - Modern Glass Design
           ======================================== */
        .navbar-top {
            margin-left: var(--sidebar-width);
            background: rgba(255, 255, 255, 0.9);
            backdrop-filter: blur(20px);
            border-bottom: 1px solid var(--gray-200);
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04);
            padding: 0 24px;
            height: 70px;
            position: sticky;
            top: 0;
            z-index: 999;
            transition: all 0.3s ease;
        }

        .navbar-top .container-fluid {
            height: 100%;
            display: flex;
            align-items: center;
        }

        .navbar-brand-mobile {
            display: none;
            font-weight: 700;
            color: var(--primary-600);
            font-size: 1.1rem;
        }

        .navbar-top .btn-link {
            color: var(--gray-600);
            text-decoration: none;
            font-weight: 500;
            padding: 8px 12px;
            border-radius: 10px;
            transition: all 0.2s ease;
        }

        .navbar-top .btn-link:hover {
            background: var(--gray-100);
            color: var(--primary-600);
        }

        /* Branch Switcher */
        .branch-switcher {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: var(--primary-50);
            border: 1px solid var(--primary-200);
            border-radius: 10px;
            color: var(--primary-700);
            font-size: 0.85rem;
            font-weight: 500;
            transition: all 0.2s ease;
            text-decoration: none;
        }

        .branch-switcher:hover {
            background: var(--primary-100);
            border-color: var(--primary-300);
            color: var(--primary-800);
            transform: translateY(-1px);
        }

        .branch-switcher i {
            font-size: 1rem;
        }

        /* User Dropdown */
        .user-dropdown {
            display: flex;
            align-items: center;
            gap: 10px;
            padding: 8px 16px;
            border-radius: 12px;
            transition: all 0.2s ease;
            cursor: pointer;
            text-decoration: none;
            color: var(--gray-700);
        }

        .user-dropdown:hover {
            background: var(--gray-100);
        }

        .user-avatar {
            width: 40px;
            height: 40px;
            background: linear-gradient(135deg, var(--primary-500), var(--primary-600));
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 1.1rem;
            box-shadow: 0 2px 8px rgba(14, 165, 233, 0.3);
        }

        .user-info {
            display: flex;
            flex-direction: column;
        }

        .user-name {
            font-weight: 600;
            font-size: 0.9rem;
            color: var(--gray-800);
        }

        .user-role {
            font-size: 0.75rem;
            color: var(--gray-500);
        }

        .dropdown-menu {
            border: 1px solid var(--gray-200);
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            padding: 8px;
            min-width: 200px;
        }

        .dropdown-item {
            padding: 10px 16px;
            border-radius: 8px;
            font-size: 0.9rem;
            color: var(--gray-600);
            transition: all 0.2s ease;
        }

        .dropdown-item:hover {
            background: var(--primary-50);
            color: var(--primary-700);
        }

        .dropdown-item i {
            margin-right: 10px;
            width: 20px;
            text-align: center;
        }

        .dropdown-divider {
            margin: 8px 0;
            border-color: var(--gray-200);
        }

        /* ========================================
           MAIN CONTENT
           ======================================== */
        .main-content {
            margin-left: var(--sidebar-width);
            padding: 24px;
            min-height: calc(100vh - 70px);
            transition: all 0.3s ease;
        }

        /* ========================================
           CARDS - Modern Design
           ======================================== */
        .card {
            background: white !important;
            border: 1px solid var(--gray-200) !important;
            border-radius: 16px !important;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.04) !important;
            overflow: hidden;
            transition: all 0.3s ease;
        }

        .card:hover {
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.08) !important;
        }

        .card-header {
            background: linear-gradient(135deg, var(--gray-50) 0%, white 100%) !important;
            border-bottom: 1px solid var(--gray-200) !important;
            padding: 20px 24px !important;
            font-weight: 600;
            color: var(--gray-800);
        }

        .card-body {
            padding: 24px !important;
        }

        /* ========================================
           BUTTONS - Modern Style
           ======================================== */
        .btn {
            font-weight: 500;
            border-radius: 10px;
            padding: 10px 20px;
            font-size: 0.9rem;
            border: none;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 8px;
        }

        .btn-primary {
            background: linear-gradient(135deg, var(--primary-500) 0%, var(--primary-600) 100%) !important;
            color: white !important;
            box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
        }

        .btn-primary:hover {
            background: linear-gradient(135deg, var(--primary-600) 0%, var(--primary-700) 100%) !important;
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(14, 165, 233, 0.4);
        }

        .btn-outline-primary {
            background: transparent !important;
            color: var(--primary-600) !important;
            border: 2px solid var(--primary-500) !important;
        }

        .btn-outline-primary:hover {
            background: var(--primary-500) !important;
            color: white !important;
        }

        .btn-success {
            background: linear-gradient(135deg, #10b981 0%, #059669 100%) !important;
            color: white !important;
        }

        .btn-danger {
            background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%) !important;
            color: white !important;
        }

        .btn-warning {
            background: linear-gradient(135deg, #f59e0b 0%, #d97706 100%) !important;
            color: white !important;
        }

        .btn-light {
            background: white !important;
            color: var(--gray-700) !important;
            border: 1px solid var(--gray-300) !important;
        }

        .btn-light:hover {
            background: var(--gray-100) !important;
        }

        /* ========================================
           FORMS - Clean Design
           ======================================== */
        .form-control, .form-select {
            background: white !important;
            border: 2px solid var(--gray-200) !important;
            border-radius: 10px !important;
            padding: 12px 16px !important;
            font-size: 0.9rem !important;
            color: var(--gray-800) !important;
            transition: all 0.2s ease;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-500) !important;
            box-shadow: 0 0 0 4px rgba(14, 165, 233, 0.1) !important;
        }

        .form-control::placeholder {
            color: var(--gray-400) !important;
        }

        .form-label {
            color: var(--gray-700) !important;
            font-size: 0.9rem !important;
            font-weight: 600 !important;
            margin-bottom: 8px !important;
        }

        /* ========================================
           TABLES - Modern Design
           ======================================== */
        .table {
            background: white !important;
            border-radius: 12px !important;
            overflow: hidden !important;
            border: 1px solid var(--gray-200);
        }

        .table thead th {
            background: var(--gray-50) !important;
            border-bottom: 2px solid var(--gray-200) !important;
            color: var(--gray-600) !important;
            font-weight: 600 !important;
            font-size: 0.8rem !important;
            padding: 16px !important;
            text-transform: uppercase;
            letter-spacing: 0.05em;
        }

        .table tbody td {
            border-bottom: 1px solid var(--gray-100) !important;
            padding: 16px !important;
            color: var(--gray-700);
            vertical-align: middle;
        }

        .table tbody tr:hover {
            background: var(--primary-50) !important;
        }

        /* ========================================
           ALERTS - Modern Style
           ======================================== */
        .alert {
            border: none !important;
            border-radius: 12px !important;
            padding: 16px 20px !important;
            display: flex;
            align-items: center;
            gap: 12px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.05);
        }

        .alert-success {
            background: linear-gradient(135deg, #ecfdf5 0%, #d1fae5 100%) !important;
            color: #065f46 !important;
            border-left: 4px solid #10b981 !important;
        }

        .alert-danger {
            background: linear-gradient(135deg, #fef2f2 0%, #fee2e2 100%) !important;
            color: #991b1b !important;
            border-left: 4px solid #ef4444 !important;
        }

        .alert-info {
            background: linear-gradient(135deg, var(--primary-50) 0%, var(--primary-100) 100%) !important;
            color: var(--primary-800) !important;
            border-left: 4px solid var(--primary-500) !important;
        }

        .alert-warning {
            background: linear-gradient(135deg, #fffbeb 0%, #fef3c7 100%) !important;
            color: #92400e !important;
            border-left: 4px solid #f59e0b !important;
        }

        /* ========================================
           BADGES
           ======================================== */
        .badge {
            font-weight: 500 !important;
            padding: 6px 12px !important;
            border-radius: 8px !important;
            font-size: 0.75rem !important;
        }

        /* ========================================
           MOBILE BOTTOM NAVIGATION
           ======================================== */
        .mobile-bottom-nav {
            position: fixed;
            bottom: 0;
            left: 0;
            right: 0;
            height: 70px;
            background: white;
            border-top: 1px solid var(--gray-200);
            display: flex;
            justify-content: space-around;
            align-items: center;
            z-index: 1000;
            box-shadow: 0 -4px 20px rgba(0, 0, 0, 0.06);
            padding-bottom: env(safe-area-inset-bottom);
        }

        .mobile-bottom-nav a {
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            text-decoration: none;
            color: var(--gray-500);
            font-size: 0.7rem;
            padding: 8px 16px;
            border-radius: 12px;
            transition: all 0.2s ease;
            font-weight: 500;
        }

        .mobile-bottom-nav a i {
            font-size: 1.4rem;
            margin-bottom: 4px;
        }

        .mobile-bottom-nav a.active {
            color: var(--primary-600);
            background: var(--primary-50);
        }

        .mobile-bottom-nav a:active {
            transform: scale(0.95);
        }

        /* ========================================
           RESPONSIVE
           ======================================== */
        @media (max-width: 992px) {
            .sidebar {
                transform: translateX(-100%);
                box-shadow: none;
            }

            .sidebar.show {
                transform: translateX(0);
                box-shadow: 10px 0 40px rgba(0, 0, 0, 0.15);
            }

            .main-content,
            .navbar-top {
                margin-left: 0;
            }

            .navbar-brand-mobile {
                display: block;
            }

            .main-content {
                padding: 16px;
                padding-bottom: 90px;
            }
        }

        @media (max-width: 768px) {
            .user-info {
                display: none;
            }

            .branch-switcher span {
                display: none;
            }

            .navbar-top {
                padding: 0 16px;
                height: 60px;
            }
        }

        /* Overlay for mobile sidebar */
        .sidebar-overlay {
            display: none;
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.5);
            z-index: 999;
            opacity: 0;
            transition: opacity 0.3s ease;
        }

        .sidebar-overlay.show {
            display: block;
            opacity: 1;
        }
    </style>
</head>
<body>
    <!-- Sidebar Overlay (Mobile) -->
    <div class="sidebar-overlay" id="sidebarOverlay"></div>

    <!-- Sidebar -->
    <div class="sidebar" id="sidebar">
        <!-- Header -->
        <div class="sidebar-header">
            <div class="sidebar-brand">
                <div class="sidebar-brand-icon">
                    <i class="bi bi-heart-pulse"></i>
                </div>
                <div class="sidebar-brand-text">
                    <h5>CNG Clinic</h5>
                    <small>Physical Therapy</small>
                </div>
            </div>
        </div>

        <!-- Navigation -->
        <nav class="sidebar-nav">
            {{-- Main Menu --}}
            <div class="nav-section">
                <a class="nav-link {{ request()->is('dashboard') ? 'active' : '' }}" href="{{ route('dashboard') }}">
                    <i class="bi bi-grid-1x2-fill"></i>
                    <span>แดชบอร์ด</span>
                </a>
                <a class="nav-link {{ request()->is('patients*') ? 'active' : '' }}" href="{{ route('patients.index') }}">
                    <i class="bi bi-people-fill"></i>
                    <span>ลูกค้า</span>
                </a>
                <a class="nav-link {{ request()->is('appointments*') ? 'active' : '' }}" href="{{ route('appointments.index') }}">
                    <i class="bi bi-calendar-check-fill"></i>
                    <span>นัดหมาย</span>
                </a>
                <a class="nav-link {{ request()->is('queue*') ? 'active' : '' }}" href="{{ route('queue.index') }}">
                    <i class="bi bi-card-list"></i>
                    <span>จัดการคิว</span>
                </a>
            </div>

            {{-- PT Menu: My Income --}}
            @if(auth()->check() && auth()->user()->role && auth()->user()->role->name === 'PT')
            <div class="sidebar-divider"></div>
            <div class="nav-section">
                <div class="nav-section-title">รายได้</div>
                <a class="nav-link {{ request()->is('commission-rates/'.auth()->id().'/detail*') ? 'active' : '' }}" href="{{ url('/commission-rates/'.auth()->id().'/detail') }}">
                    <i class="bi bi-wallet2"></i>
                    <span>รายได้ของฉัน</span>
                </a>
            </div>
            @endif

            {{-- Admin/Manager Menu --}}
            @if(auth()->check() && (auth()->user()->username === 'admin' || (auth()->user()->role && in_array(auth()->user()->role->name, ['Admin', 'Manager']))))
            <div class="sidebar-divider"></div>
            <div class="nav-section">
                <div class="nav-section-title">จัดการธุรกิจ</div>
                <a class="nav-link {{ request()->is('reports*') ? 'active' : '' }}" href="{{ url('/reports/pnl') }}">
                    <i class="bi bi-graph-up-arrow"></i>
                    <span>รายงาน P&L</span>
                </a>
                <a class="nav-link {{ request()->is('expenses*') ? 'active' : '' }}" href="{{ url('/expenses') }}">
                    <i class="bi bi-receipt-cutoff"></i>
                    <span>รายจ่าย</span>
                </a>
                <a class="nav-link {{ request()->is('stock*') ? 'active' : '' }}" href="{{ url('/stock') }}">
                    <i class="bi bi-box-seam-fill"></i>
                    <span>จัดการสต็อก</span>
                </a>
                <a class="nav-link {{ request()->is('equipment*') ? 'active' : '' }}" href="{{ url('/equipment') }}">
                    <i class="bi bi-tools"></i>
                    <span>อุปกรณ์</span>
                </a>
                <a class="nav-link {{ request()->is('crm') ? 'active' : '' }}" href="{{ url('/crm') }}">
                    <i class="bi bi-telephone-inbound-fill"></i>
                    <span>CRM</span>
                </a>
            </div>
            @endif

            {{-- Admin Only: System Settings --}}
            @if(auth()->check() && auth()->user()->username === 'admin')
            <div class="sidebar-divider"></div>
            <div class="nav-section">
                <div class="nav-section-title">ตั้งค่าระบบ</div>
                <a class="nav-link" href="#" data-bs-toggle="collapse" data-bs-target="#settingsSubmenu" aria-expanded="false">
                    <i class="bi bi-gear-fill"></i>
                    <span>การตั้งค่า</span>
                    <i class="bi bi-chevron-down ms-auto" style="font-size: 0.8rem;"></i>
                </a>
                <div class="collapse nav-submenu" id="settingsSubmenu">
                    <a class="nav-link {{ request()->is('services*') ? 'active' : '' }}" href="{{ url('/services') }}">
                        <i class="bi bi-clipboard2-pulse"></i>
                        <span>บริการ</span>
                    </a>
                    <a class="nav-link {{ request()->is('course-packages*') ? 'active' : '' }}" href="{{ url('/course-packages') }}">
                        <i class="bi bi-box-seam"></i>
                        <span>คอร์ส</span>
                    </a>
                    <a class="nav-link {{ request()->is('commission-rates*') && !request()->is('commission-rates/'.auth()->id().'/detail*') ? 'active' : '' }}" href="{{ url('/commission-rates') }}">
                        <i class="bi bi-cash-coin"></i>
                        <span>ค่าตอบแทน</span>
                    </a>
                    <a class="nav-link {{ request()->is('users*') ? 'active' : '' }}" href="{{ url('/users') }}">
                        <i class="bi bi-person-badge"></i>
                        <span>พนักงาน</span>
                    </a>
                    <a class="nav-link {{ request()->is('branches*') ? 'active' : '' }}" href="{{ url('/branches') }}">
                        <i class="bi bi-building"></i>
                        <span>สาขา</span>
                    </a>
                    <a class="nav-link {{ request()->is('roles*') || request()->is('permissions*') ? 'active' : '' }}" href="{{ url('/roles') }}">
                        <i class="bi bi-shield-lock"></i>
                        <span>สิทธิ์การใช้งาน</span>
                    </a>
                </div>
            </div>
            @endif
        </nav>
    </div>

    <!-- Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-top">
        <div class="container-fluid">
            <button class="btn btn-link d-lg-none p-2" type="button" id="sidebarToggle">
                <i class="bi bi-list fs-4"></i>
            </button>

            <span class="navbar-brand-mobile">CNG Clinic</span>

            <div class="ms-auto d-flex align-items-center gap-3">
                {{-- Branch Switcher (Admin/Manager Only) --}}
                @if(auth()->check() && (auth()->user()->username === 'admin' || (auth()->user()->role && in_array(auth()->user()->role->name, ['Admin', 'Manager']))))
                    @if(auth()->user()->username === 'admin')
                    <a href="{{ route('branch.selector') }}" class="branch-switcher" title="สลับสาขา">
                        <i class="bi bi-building"></i>
                        <span>{{ session('selected_branch_id') ? (\App\Models\Branch::find(session('selected_branch_id'))?->name ?? 'เลือกสาขา') : 'เลือกสาขา' }}</span>
                        <i class="bi bi-chevron-down" style="font-size: 0.7rem;"></i>
                    </a>
                    @else
                    <div class="branch-switcher" style="cursor: default;">
                        <i class="bi bi-building"></i>
                        <span>{{ auth()->user()->branch->name ?? 'สาขาหลัก' }}</span>
                    </div>
                    @endif
                @elseif(auth()->check() && auth()->user()->role && auth()->user()->role->name === 'PT')
                <div class="branch-switcher" style="cursor: default;">
                    <i class="bi bi-building"></i>
                    <span>{{ auth()->user()->branch->name ?? 'สาขาหลัก' }}</span>
                </div>
                @endif

                {{-- User Dropdown --}}
                <div class="dropdown">
                    <a class="user-dropdown dropdown-toggle" href="#" role="button" data-bs-toggle="dropdown" aria-expanded="false">
                        <div class="user-avatar">
                            <i class="bi bi-person-fill"></i>
                        </div>
                        <div class="user-info d-none d-md-flex">
                            <span class="user-name">{{ auth()->check() ? auth()->user()->name : 'ผู้เยี่ยมชม' }}</span>
                            <span class="user-role">{{ auth()->check() && auth()->user()->role ? auth()->user()->role->name : 'Guest' }}</span>
                        </div>
                    </a>
                    <ul class="dropdown-menu dropdown-menu-end">
                        <li><a class="dropdown-item" href="#"><i class="bi bi-person"></i>โปรไฟล์</a></li>
                        <li><a class="dropdown-item" href="#"><i class="bi bi-gear"></i>ตั้งค่า</a></li>
                        <li><hr class="dropdown-divider"></li>
                        <li>
                            <form method="POST" action="{{ route('logout') }}" id="logoutForm">
                                @csrf
                                <button type="submit" class="dropdown-item text-danger">
                                    <i class="bi bi-box-arrow-right"></i>ออกจากระบบ
                                </button>
                            </form>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="main-content">
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i>
                <span>{{ session('success') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if(session('error'))
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <span>{{ session('error') }}</span>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @if($errors->any())
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-triangle-fill"></i>
                <div>
                    <strong>ข้อผิดพลาด:</strong>
                    <ul class="mb-0 mt-2">
                        @foreach($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                </div>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        @endif

        @yield('content')
    </main>

    <!-- Mobile Bottom Navigation -->
    <nav class="mobile-bottom-nav d-lg-none">
        <a href="{{ route('dashboard') }}" class="{{ request()->is('dashboard') ? 'active' : '' }}">
            <i class="bi bi-grid-1x2{{ request()->is('dashboard') ? '-fill' : '' }}"></i>
            <span>หน้าแรก</span>
        </a>
        <a href="{{ route('patients.index') }}" class="{{ request()->is('patients*') ? 'active' : '' }}">
            <i class="bi bi-people{{ request()->is('patients*') ? '-fill' : '' }}"></i>
            <span>ลูกค้า</span>
        </a>
        <a href="{{ route('appointments.index') }}" class="{{ request()->is('appointments*') ? 'active' : '' }}">
            <i class="bi bi-calendar-check{{ request()->is('appointments*') ? '-fill' : '' }}"></i>
            <span>นัดหมาย</span>
        </a>
        <a href="{{ route('queue.index') }}" class="{{ request()->is('queue*') ? 'active' : '' }}">
            <i class="bi bi-card-list"></i>
            <span>คิว</span>
        </a>
    </nav>

    <!-- Bootstrap 5 JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

    <script>
        // Sidebar toggle for mobile
        const sidebarToggle = document.getElementById('sidebarToggle');
        const sidebar = document.getElementById('sidebar');
        const sidebarOverlay = document.getElementById('sidebarOverlay');

        function openSidebar() {
            sidebar.classList.add('show');
            sidebarOverlay.classList.add('show');
            document.body.style.overflow = 'hidden';
        }

        function closeSidebar() {
            sidebar.classList.remove('show');
            sidebarOverlay.classList.remove('show');
            document.body.style.overflow = '';
        }

        if (sidebarToggle && sidebar) {
            sidebarToggle.addEventListener('click', function() {
                if (sidebar.classList.contains('show')) {
                    closeSidebar();
                } else {
                    openSidebar();
                }
            });
        }

        if (sidebarOverlay) {
            sidebarOverlay.addEventListener('click', closeSidebar);
        }

        // Close sidebar when clicking a link (mobile)
        document.querySelectorAll('.sidebar .nav-link:not([data-bs-toggle])').forEach(link => {
            link.addEventListener('click', function() {
                if (window.innerWidth < 992) {
                    closeSidebar();
                }
            });
        });

        // Auto-hide alerts after 5 seconds
        setTimeout(function() {
            document.querySelectorAll('.alert').forEach(alert => {
                const bsAlert = new bootstrap.Alert(alert);
                bsAlert.close();
            });
        }, 5000);
    </script>

    @stack('scripts')
</body>
</html>
