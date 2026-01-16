@extends('layouts.app')

@section('title', 'จัดการผู้ใช้ - GCMS')

@push('styles')
<style>
    /* ==================== MODERN USERS PAGE 2024 ==================== */

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
        cursor: pointer;
    }

    .btn-add-new:hover {
        background: white;
        color: #0ea5e9;
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    }

    /* Stat Cards Grid */
    .stat-grid {
        display: grid;
        grid-template-columns: repeat(3, 1fr);
        gap: 1rem;
        margin-bottom: 1.5rem;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        border: 1px solid #f1f5f9;
        border-left: 4px solid;
        transition: all 0.3s ease;
    }

    .stat-card:hover {
        transform: translateY(-4px);
        box-shadow: 0 12px 40px rgba(0,0,0,0.08);
    }

    .stat-card.blue { border-left-color: #0ea5e9; }
    .stat-card.green { border-left-color: #10b981; }
    .stat-card.gray { border-left-color: #64748b; }

    .stat-content {
        display: flex;
        justify-content: space-between;
        align-items: center;
    }

    .stat-value {
        font-size: 2rem;
        font-weight: 800;
        line-height: 1;
        margin-bottom: 0.25rem;
    }

    .stat-card.blue .stat-value { color: #0369a1; }
    .stat-card.green .stat-value { color: #166534; }
    .stat-card.gray .stat-value { color: #475569; }

    .stat-label {
        font-size: 0.85rem;
        color: #64748b;
        font-weight: 500;
    }

    .stat-icon {
        width: 56px;
        height: 56px;
        border-radius: 14px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .stat-card.blue .stat-icon {
        background: linear-gradient(135deg, #e0f2fe, #bae6fd);
        color: #0369a1;
    }

    .stat-card.green .stat-icon {
        background: linear-gradient(135deg, #dcfce7, #bbf7d0);
        color: #166534;
    }

    .stat-card.gray .stat-icon {
        background: linear-gradient(135deg, #f1f5f9, #e2e8f0);
        color: #475569;
    }

    /* User Table Card */
    .table-card {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 20px rgba(0,0,0,0.04);
        border: 1px solid #f1f5f9;
        overflow: hidden;
    }

    .table-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #f1f5f9;
        display: flex;
        justify-content: space-between;
        align-items: center;
        flex-wrap: wrap;
        gap: 1rem;
        background: linear-gradient(135deg, #f8fafc, #fff);
    }

    .table-title {
        font-size: 1rem;
        font-weight: 700;
        color: #1e293b;
        display: flex;
        align-items: center;
        gap: 0.5rem;
        margin: 0;
    }

    .table-title i {
        color: #0ea5e9;
    }

    .search-box {
        position: relative;
        width: 280px;
    }

    .search-box i {
        position: absolute;
        left: 14px;
        top: 50%;
        transform: translateY(-50%);
        color: #94a3b8;
    }

    .search-input {
        width: 100%;
        padding: 10px 14px 10px 42px;
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .search-input:focus {
        outline: none;
        border-color: #0ea5e9;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
    }

    /* Table Styles */
    .user-table {
        width: 100%;
        border-collapse: collapse;
    }

    .user-table thead tr {
        background: linear-gradient(135deg, #f8fafc, #f1f5f9);
    }

    .user-table thead th {
        padding: 14px 20px;
        font-size: 0.75rem;
        font-weight: 600;
        color: #64748b;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        text-align: left;
        border-bottom: 1px solid #e2e8f0;
    }

    .user-table tbody tr {
        transition: all 0.2s ease;
    }

    .user-table tbody tr:hover {
        background: #f8fafc;
    }

    .user-table tbody td {
        padding: 16px 20px;
        border-bottom: 1px solid #f1f5f9;
        vertical-align: middle;
        font-size: 0.9rem;
        color: #334155;
    }

    .user-table tbody tr:last-child td {
        border-bottom: none;
    }

    /* User Avatar */
    .user-info {
        display: flex;
        align-items: center;
        gap: 12px;
    }

    .user-avatar {
        width: 42px;
        height: 42px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1rem;
        font-weight: 600;
        background: linear-gradient(135deg, #e0f2fe, #bae6fd);
        color: #0369a1;
    }

    .user-name {
        font-weight: 600;
        color: #1e293b;
    }

    /* Badges */
    .badge-username {
        background: #f1f5f9;
        color: #475569;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .badge-role {
        background: linear-gradient(135deg, #e0f2fe, #bae6fd);
        color: #0369a1;
        padding: 6px 12px;
        border-radius: 8px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-status {
        padding: 6px 12px;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .badge-status.active {
        background: #dcfce7;
        color: #166534;
    }

    .badge-status.inactive {
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
    }

    /* Alert */
    .custom-alert {
        position: fixed;
        top: 20px;
        left: 50%;
        transform: translateX(-50%);
        z-index: 9999;
        padding: 1rem 1.5rem;
        border-radius: 12px;
        display: flex;
        align-items: center;
        gap: 0.75rem;
        box-shadow: 0 10px 40px rgba(0,0,0,0.15);
        animation: slideDown 0.3s ease;
    }

    .custom-alert.success {
        background: #dcfce7;
        color: #166534;
        border: 1px solid #bbf7d0;
    }

    .custom-alert.danger {
        background: #fee2e2;
        color: #dc2626;
        border: 1px solid #fecaca;
    }

    @keyframes slideDown {
        from { opacity: 0; transform: translateX(-50%) translateY(-20px); }
        to { opacity: 1; transform: translateX(-50%) translateY(0); }
    }

    /* Modal Styling */
    .modal-header.gradient-header {
        background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 100%);
        color: white;
        border-radius: 12px 12px 0 0;
        padding: 1.25rem 1.5rem;
    }

    .modal-header.danger-header {
        background: linear-gradient(135deg, #ef4444 0%, #dc2626 100%);
        color: white;
        border-radius: 12px 12px 0 0;
    }

    .modal-content {
        border: none;
        border-radius: 16px;
        box-shadow: 0 25px 60px rgba(0,0,0,0.2);
    }

    .modal-title {
        font-weight: 700;
        display: flex;
        align-items: center;
        gap: 0.5rem;
    }

    .form-label {
        font-size: 0.85rem;
        font-weight: 600;
        color: #475569;
        margin-bottom: 0.5rem;
    }

    .form-control, .form-select {
        border: 2px solid #e2e8f0;
        border-radius: 10px;
        padding: 0.75rem 1rem;
        font-size: 0.9rem;
        transition: all 0.2s ease;
    }

    .form-control:focus, .form-select:focus {
        border-color: #0ea5e9;
        box-shadow: 0 0 0 3px rgba(14, 165, 233, 0.1);
    }

    .btn-primary {
        background: linear-gradient(135deg, #0ea5e9, #3b82f6);
        border: none;
        padding: 0.75rem 1.5rem;
        border-radius: 10px;
        font-weight: 600;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #0284c7, #2563eb);
        transform: translateY(-2px);
        box-shadow: 0 8px 25px rgba(14, 165, 233, 0.3);
    }

    /* Responsive */
    @media (max-width: 1200px) {
        .stat-grid {
            grid-template-columns: repeat(3, 1fr);
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

        .stat-grid {
            grid-template-columns: 1fr;
        }

        .table-header {
            flex-direction: column;
            align-items: stretch;
        }

        .search-box {
            width: 100%;
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
                <h2><i class="bi bi-people-fill"></i>จัดการผู้ใช้</h2>
                <p>จัดการบัญชีผู้ใช้งานทั้งหมดในระบบ</p>
            </div>
            <button class="btn-add-new" data-bs-toggle="modal" data-bs-target="#userModal" onclick="openCreateModal()">
                <i class="bi bi-plus-circle-fill"></i>
                เพิ่มผู้ใช้
            </button>
        </div>
    </div>

    <!-- Stats Grid -->
    <div class="stat-grid">
        <div class="stat-card blue">
            <div class="stat-content">
                <div>
                    <div class="stat-value">{{ $users->total() }}</div>
                    <div class="stat-label">ผู้ใช้ทั้งหมด</div>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-people"></i>
                </div>
            </div>
        </div>
        <div class="stat-card green">
            <div class="stat-content">
                <div>
                    <div class="stat-value">{{ $users->where('is_active', true)->count() }}</div>
                    <div class="stat-label">เปิดใช้งาน</div>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-check-circle"></i>
                </div>
            </div>
        </div>
        <div class="stat-card gray">
            <div class="stat-content">
                <div>
                    <div class="stat-value">{{ $users->where('is_active', false)->count() }}</div>
                    <div class="stat-label">ปิดใช้งาน</div>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-x-circle"></i>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Table -->
    <div class="table-card">
        <div class="table-header">
            <h3 class="table-title">
                <i class="bi bi-list-ul"></i>
                รายการผู้ใช้
            </h3>
            <div class="search-box">
                <i class="bi bi-search"></i>
                <input type="text" class="search-input" id="searchInput" placeholder="ค้นหาผู้ใช้...">
            </div>
        </div>

        <table class="user-table">
            <thead>
                <tr>
                    <th>ชื่อผู้ใช้</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>สาขา</th>
                    <th class="text-center">สถานะ</th>
                    <th class="text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $user)
                <tr>
                    <td>
                        <div class="user-info">
                            <div class="user-avatar">
                                {{ mb_substr($user->name, 0, 1) }}
                            </div>
                            <span class="user-name">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td>
                        <span class="badge-username">{{ $user->username }}</span>
                    </td>
                    <td>{{ $user->email }}</td>
                    <td>
                        @if($user->role)
                        <span class="badge-role">{{ $user->role->name }}</span>
                        @else
                        <span style="color: #94a3b8;">-</span>
                        @endif
                    </td>
                    <td>
                        @if($user->branch)
                        {{ $user->branch->name }}
                        @else
                        <span style="color: #94a3b8;">ทุกสาขา</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($user->is_active)
                        <span class="badge-status active">เปิดใช้งาน</span>
                        @else
                        <span class="badge-status inactive">ปิดใช้งาน</span>
                        @endif
                    </td>
                    <td>
                        <div class="action-btns">
                            <button class="btn-action edit" onclick="openEditModal('{{ $user->id }}')" title="แก้ไข">
                                <i class="bi bi-pencil"></i>
                            </button>
                            <button class="btn-action delete" onclick="confirmDelete('{{ $user->id }}', '{{ $user->name }}')" title="ลบ">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="bi bi-inbox"></i>
                            <h4>ไม่พบรายการผู้ใช้</h4>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>

        @if($users->hasPages())
        <div class="pagination-wrapper">
            {{ $users->links() }}
        </div>
        @endif
    </div>
</div>

<!-- User Modal (Create/Edit) -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="userForm" autocomplete="off">
                @csrf
                <input type="hidden" id="userId" name="user_id">
                <div class="modal-header gradient-header">
                    <h5 class="modal-title" id="modalTitle"><i class="bi bi-plus-circle me-2"></i>เพิ่มผู้ใช้ใหม่</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body p-4">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Username <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">รหัสผ่าน <span class="text-danger" id="passwordRequired">*</span></label>
                            <input type="password" class="form-control" id="password" name="password">
                            <small class="text-muted" id="passwordHint" style="display: none;">เว้นว่างหากไม่ต้องการเปลี่ยนรหัสผ่าน</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">บทบาท (Role) <span class="text-danger">*</span></label>
                            <select class="form-select" id="role_id" name="role_id" required>
                                <option value="">-- เลือกบทบาท --</option>
                                @foreach($roles as $role)
                                <option value="{{ $role->id }}">{{ $role->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">สาขา</label>
                            <select class="form-select" id="branch_id" name="branch_id">
                                <option value="">-- ทุกสาขา --</option>
                                @foreach($branches as $branch)
                                <option value="{{ $branch->id }}">{{ $branch->name }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-12">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                <label class="form-check-label" for="is_active">เปิดใช้งาน</label>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle me-2"></i>บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Confirmation Modal -->
<div class="modal fade" id="deleteModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header danger-header">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>ยืนยันการลบ</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-4">
                <p>คุณต้องการลบผู้ใช้ "<strong id="deleteUserName"></strong>" ใช่หรือไม่?</p>
                <p class="text-muted mb-0">การดำเนินการนี้ไม่สามารถย้อนกลับได้</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-danger" id="confirmDeleteBtn">
                    <i class="bi bi-trash me-2"></i>ลบ
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function() {
    const userModal = new bootstrap.Modal(document.getElementById('userModal'));
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    let deleteUserId = null;
    let isEditMode = false;

    // Open create modal
    window.openCreateModal = function() {
        isEditMode = false;
        document.getElementById('userForm').reset();
        document.getElementById('userId').value = '';
        document.getElementById('modalTitle').innerHTML = '<i class="bi bi-plus-circle me-2"></i>เพิ่มผู้ใช้ใหม่';
        document.getElementById('is_active').checked = true;
        document.getElementById('password').required = true;
        document.getElementById('passwordRequired').style.display = '';
        document.getElementById('passwordHint').style.display = 'none';
    };

    // Open edit modal
    window.openEditModal = function(id) {
        isEditMode = true;
        fetch(`{{ url('/users') }}/${id}/edit`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('userId').value = data.id;
            document.getElementById('name').value = data.name;
            document.getElementById('username').value = data.username;
            document.getElementById('email').value = data.email;
            document.getElementById('password').value = '';
            document.getElementById('role_id').value = data.role_id || '';
            document.getElementById('branch_id').value = data.branch_id || '';
            document.getElementById('is_active').checked = data.is_active;

            document.getElementById('modalTitle').innerHTML = '<i class="bi bi-pencil me-2"></i>แก้ไขผู้ใช้';
            document.getElementById('password').required = false;
            document.getElementById('passwordRequired').style.display = 'none';
            document.getElementById('passwordHint').style.display = '';

            userModal.show();
        })
        .catch(error => {
            showAlert('เกิดข้อผิดพลาดในการโหลดข้อมูล', 'danger');
        });
    };

    // Form submit
    document.getElementById('userForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const userId = document.getElementById('userId').value;
        const formData = new FormData(this);

        let url = '{{ url('/users') }}';
        let method = 'POST';

        if (userId) {
            url = `{{ url('/users') }}/${userId}`;
            formData.append('_method', 'PUT');
        }

        const btn = this.querySelector('button[type="submit"]');
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>กำลังบันทึก...';

        fetch(url, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => {
            if (!response.ok) {
                return response.json().then(err => Promise.reject(err));
            }
            return response.json();
        })
        .then(data => {
            if (data.success) {
                userModal.hide();
                showAlert(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                let errorMsg = 'เกิดข้อผิดพลาด';
                if (data.errors) {
                    errorMsg = Object.values(data.errors).flat().join('<br>');
                } else if (data.message) {
                    errorMsg = data.message;
                }
                showAlert(errorMsg, 'danger');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>บันทึก';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            let errorMsg = 'เกิดข้อผิดพลาดในการบันทึกข้อมูล';
            if (error.errors) {
                errorMsg = Object.values(error.errors).flat().join('<br>');
            } else if (error.message) {
                errorMsg = error.message;
            }
            showAlert(errorMsg, 'danger');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check-circle me-2"></i>บันทึก';
        });
    });

    // Confirm delete
    window.confirmDelete = function(id, name) {
        deleteUserId = id;
        document.getElementById('deleteUserName').textContent = name;
        deleteModal.show();
    };

    // Delete user
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (!deleteUserId) return;

        const btn = this;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm me-2"></span>กำลังลบ...';

        const formData = new FormData();
        formData.append('_method', 'DELETE');
        formData.append('_token', '{{ csrf_token() }}');

        fetch(`{{ url('/users') }}/${deleteUserId}`, {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                deleteModal.hide();
                showAlert(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert(data.message || 'เกิดข้อผิดพลาดในการลบ', 'danger');
                btn.disabled = false;
                btn.innerHTML = '<i class="bi bi-trash me-2"></i>ลบ';
            }
        })
        .catch(error => {
            console.error('Error:', error);
            showAlert('เกิดข้อผิดพลาดในการลบข้อมูล', 'danger');
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-trash me-2"></i>ลบ';
        });
    });

    // Search functionality
    document.getElementById('searchInput').addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = document.querySelectorAll('tbody tr');

        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });

    // Show alert
    function showAlert(message, type) {
        const alertDiv = document.createElement('div');
        alertDiv.className = `custom-alert ${type}`;
        alertDiv.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle-fill' : 'exclamation-circle-fill'}"></i>
            <span>${message}</span>
        `;
        document.body.appendChild(alertDiv);

        setTimeout(() => {
            alertDiv.remove();
        }, 3000);
    }
});
</script>
@endpush
@endsection
