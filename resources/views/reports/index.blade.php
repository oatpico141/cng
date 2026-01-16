@extends('layouts.app')

@section('title', 'รายงาน - GCMS')

@push('styles')
<style>
    /* ==================== MODERN REPORTS PAGE 2024 ==================== */

    /* Page Header */
    .page-header {
        background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 50%, #6366f1 100%);
        border-radius: 20px;
        padding: 2rem 2.5rem;
        color: white;
        margin-bottom: 2rem;
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

    .page-header p {
        margin: 0;
        opacity: 0.95;
        font-size: 0.95rem;
    }

    .btn-dashboard {
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

    .btn-dashboard:hover {
        background: white;
        color: #0ea5e9;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    /* Section Title */
    .section-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 1.25rem;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        padding-bottom: 0.75rem;
        border-bottom: 2px solid #f1f5f9;
    }

    .section-title i {
        font-size: 1.25rem;
    }

    .section-title .icon-finance { color: #10b981; }
    .section-title .icon-ops { color: #3b82f6; }
    .section-title .icon-stock { color: #f59e0b; }
    .section-title .icon-course { color: #06b6d4; }

    /* Report Cards */
    .report-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1.25rem;
        margin-bottom: 2.5rem;
    }

    .report-card {
        background: white;
        border-radius: 16px;
        padding: 1.75rem;
        border: 1px solid #f1f5f9;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        transition: all 0.3s ease;
        cursor: pointer;
        text-decoration: none;
        display: block;
        position: relative;
        overflow: hidden;
    }

    .report-card::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        height: 4px;
        border-radius: 16px 16px 0 0;
        opacity: 0;
        transition: opacity 0.3s ease;
    }

    .report-card:hover {
        transform: translateY(-6px);
        box-shadow: 0 20px 50px rgba(0,0,0,0.1);
        border-color: transparent;
    }

    .report-card:hover::before {
        opacity: 1;
    }

    .report-card.blue::before { background: linear-gradient(90deg, #0ea5e9, #3b82f6); }
    .report-card.green::before { background: linear-gradient(90deg, #10b981, #34d399); }
    .report-card.orange::before { background: linear-gradient(90deg, #f59e0b, #fbbf24); }
    .report-card.purple::before { background: linear-gradient(90deg, #8b5cf6, #a78bfa); }
    .report-card.cyan::before { background: linear-gradient(90deg, #06b6d4, #22d3ee); }
    .report-card.pink::before { background: linear-gradient(90deg, #ec4899, #f472b6); }

    .report-icon {
        width: 64px;
        height: 64px;
        border-radius: 16px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.75rem;
        margin-bottom: 1.25rem;
        transition: transform 0.3s ease;
    }

    .report-card:hover .report-icon {
        transform: scale(1.1);
    }

    .report-icon.blue {
        background: linear-gradient(135deg, #e0f2fe, #bae6fd);
        color: #0369a1;
    }

    .report-icon.green {
        background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        color: #166534;
    }

    .report-icon.orange {
        background: linear-gradient(135deg, #fef3c7, #fde68a);
        color: #92400e;
    }

    .report-icon.purple {
        background: linear-gradient(135deg, #ede9fe, #ddd6fe);
        color: #6d28d9;
    }

    .report-icon.cyan {
        background: linear-gradient(135deg, #cffafe, #a5f3fc);
        color: #0e7490;
    }

    .report-icon.pink {
        background: linear-gradient(135deg, #fce7f3, #fbcfe8);
        color: #be185d;
    }

    .report-title {
        font-size: 1.1rem;
        font-weight: 700;
        color: #1e293b;
        margin-bottom: 0.5rem;
    }

    .report-desc {
        font-size: 0.875rem;
        color: #64748b;
        line-height: 1.6;
    }

    .report-arrow {
        position: absolute;
        bottom: 1.5rem;
        right: 1.5rem;
        width: 36px;
        height: 36px;
        border-radius: 50%;
        background: #f1f5f9;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #64748b;
        opacity: 0;
        transform: translateX(-10px);
        transition: all 0.3s ease;
    }

    .report-card:hover .report-arrow {
        opacity: 1;
        transform: translateX(0);
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .report-grid {
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

        .btn-dashboard {
            width: 100%;
            justify-content: center;
        }

        .report-grid {
            grid-template-columns: 1fr;
        }

        .report-card {
            padding: 1.25rem;
        }

        .report-icon {
            width: 56px;
            height: 56px;
            font-size: 1.5rem;
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
                <h2><i class="bi bi-bar-chart-line-fill"></i>รายงาน</h2>
                <p>เลือกรายงานที่ต้องการดู</p>
            </div>
            <a href="{{ route('reports.dashboard') }}" class="btn-dashboard">
                <i class="bi bi-speedometer2"></i>
                Dashboard
            </a>
        </div>
    </div>

    <!-- Financial Reports -->
    <div class="section-title">
        <i class="bi bi-cash-coin icon-finance"></i>
        รายงานการเงิน
    </div>
    <div class="report-grid">
        <a href="{{ route('reports.pnl') }}" class="report-card green">
            <div class="report-icon green">
                <i class="bi bi-graph-up-arrow"></i>
            </div>
            <div class="report-title">รายงานกำไรขาดทุน (P&L)</div>
            <div class="report-desc">ดูรายได้ ค่าใช้จ่าย และกำไรสุทธิ แยกตามช่วงเวลาและสาขา</div>
            <div class="report-arrow">
                <i class="bi bi-arrow-right"></i>
            </div>
        </a>

        <a href="{{ route('invoices.index') }}" class="report-card blue">
            <div class="report-icon blue">
                <i class="bi bi-receipt"></i>
            </div>
            <div class="report-title">รายงานใบเสร็จ/การขาย</div>
            <div class="report-desc">ดูรายการใบเสร็จทั้งหมด สถานะการชำระเงิน และยอดขายรายวัน</div>
            <div class="report-arrow">
                <i class="bi bi-arrow-right"></i>
            </div>
        </a>

        <a href="{{ route('expenses.index') }}" class="report-card orange">
            <div class="report-icon orange">
                <i class="bi bi-wallet2"></i>
            </div>
            <div class="report-title">รายงานค่าใช้จ่าย</div>
            <div class="report-desc">บันทึกและติดตามค่าใช้จ่ายประจำวันของคลินิก</div>
            <div class="report-arrow">
                <i class="bi bi-arrow-right"></i>
            </div>
        </a>
    </div>

    <!-- Operations Reports -->
    <div class="section-title">
        <i class="bi bi-clipboard-data icon-ops"></i>
        รายงานการดำเนินงาน
    </div>
    <div class="report-grid">
        <a href="{{ route('patients.index') }}" class="report-card cyan">
            <div class="report-icon cyan">
                <i class="bi bi-people"></i>
            </div>
            <div class="report-title">รายงานลูกค้า</div>
            <div class="report-desc">ข้อมูลลูกค้าทั้งหมด ลูกค้าใหม่ และสถิติการมาใช้บริการ</div>
            <div class="report-arrow">
                <i class="bi bi-arrow-right"></i>
            </div>
        </a>

        <a href="{{ route('appointments.index') }}" class="report-card purple">
            <div class="report-icon purple">
                <i class="bi bi-calendar-check"></i>
            </div>
            <div class="report-title">รายงานการนัดหมาย</div>
            <div class="report-desc">ดูสถิติการนัดหมาย อัตราการมาตามนัด และการยกเลิก</div>
            <div class="report-arrow">
                <i class="bi bi-arrow-right"></i>
            </div>
        </a>

        <a href="{{ route('queue.index') }}" class="report-card pink">
            <div class="report-icon pink">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="report-title">รายงานคิว</div>
            <div class="report-desc">สถิติการรอคิว เวลาเฉลี่ยในการให้บริการ</div>
            <div class="report-arrow">
                <i class="bi bi-arrow-right"></i>
            </div>
        </a>
    </div>

    <!-- Inventory & Staff Reports -->
    <div class="section-title">
        <i class="bi bi-box-seam icon-stock"></i>
        รายงานสต็อกและพนักงาน
    </div>
    <div class="report-grid">
        <a href="{{ route('stock.index') }}" class="report-card orange">
            <div class="report-icon orange">
                <i class="bi bi-box-seam"></i>
            </div>
            <div class="report-title">รายงานสต็อกสินค้า</div>
            <div class="report-desc">ดูจำนวนคงเหลือ สินค้าใกล้หมด และประวัติการเคลื่อนไหว</div>
            <div class="report-arrow">
                <i class="bi bi-arrow-right"></i>
            </div>
        </a>

        <a href="{{ route('commissions.index') }}" class="report-card green">
            <div class="report-icon green">
                <i class="bi bi-currency-dollar"></i>
            </div>
            <div class="report-title">รายงานค่าคอมมิชชัน</div>
            <div class="report-desc">ดูค่าคอมมิชชันของพนักงานแยกตามช่วงเวลา</div>
            <div class="report-arrow">
                <i class="bi bi-arrow-right"></i>
            </div>
        </a>

        <a href="{{ route('df-payments.index') }}" class="report-card blue">
            <div class="report-icon blue">
                <i class="bi bi-cash-stack"></i>
            </div>
            <div class="report-title">รายงานค่ามือ (DF)</div>
            <div class="report-desc">ดูค่ามือของ PT/หมอ แยกตามการทำหัตถการ</div>
            <div class="report-arrow">
                <i class="bi bi-arrow-right"></i>
            </div>
        </a>
    </div>

    <!-- Course Reports -->
    <div class="section-title">
        <i class="bi bi-journal-bookmark icon-course"></i>
        รายงานคอร์ส
    </div>
    <div class="report-grid">
        <a href="{{ route('course-purchases.index') }}" class="report-card cyan">
            <div class="report-icon cyan">
                <i class="bi bi-journal-check"></i>
            </div>
            <div class="report-title">รายงานการซื้อคอร์ส</div>
            <div class="report-desc">ดูรายการซื้อคอร์ส สถานะการใช้งาน และคอร์สที่ใกล้หมดอายุ</div>
            <div class="report-arrow">
                <i class="bi bi-arrow-right"></i>
            </div>
        </a>

        <a href="{{ route('course-packages.index') }}" class="report-card purple">
            <div class="report-icon purple">
                <i class="bi bi-box2-heart"></i>
            </div>
            <div class="report-title">รายงานแพ็คเกจคอร์ส</div>
            <div class="report-desc">ดูแพ็คเกจคอร์สทั้งหมด ยอดขาย และความนิยม</div>
            <div class="report-arrow">
                <i class="bi bi-arrow-right"></i>
            </div>
        </a>
    </div>
</div>
@endsection
