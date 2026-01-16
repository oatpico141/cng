@extends('layouts.app')

@section('title', 'จัดการ Role - GCMS')

@push('styles')
<style>
    .bg-gradient-primary {
        background: linear-gradient(135deg, var(--calm-blue-500, #3b82f6) 0%, var(--calm-blue-600, #2563eb) 100%);
    }
    .stat-card {
        border-left: 4px solid;
        transition: transform 0.2s;
    }
    .stat-card:hover {
        transform: translateY(-2px);
    }
    .stat-card.blue { border-left-color: var(--calm-blue-500, #3b82f6); }
    .stat-card.green { border-left-color: #10b981; }
    .stat-card.purple { border-left-color: #8b5cf6; }

    .permission-checkbox {
        border: 1px solid #e5e7eb;
        border-radius: 8px;
        padding: 12px;
        transition: all 0.2s;
    }
    .permission-checkbox:hover {
        border-color: var(--calm-blue-500, #3b82f6);
        background-color: #f0f7ff;
    }
    .permission-checkbox input:checked + label {
        color: var(--calm-blue-600, #2563eb);
        font-weight: 500;
    }
    .permission-icon {
        width: 32px;
        height: 32px;
        display: flex;
        align-items: center;
        justify-content: center;
        border-radius: 6px;
        background-color: #f3f4f6;
        margin-right: 10px;
    }
    .badge-permission {
        font-size: 0.7rem;
        padding: 0.25em 0.5em;
        margin: 2px;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-gradient-primary text-white rounded-3 p-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h2 class="mb-2"><i class="bi bi-shield-lock me-2"></i>จัดการ Role</h2>
                        <p class="mb-0 opacity-90">จัดการบทบาทและสิทธิ์การเข้าถึงของผู้ใช้งาน</p>
                    </div>
                    <div class="mt-3 mt-md-0">
                        <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#roleModal" onclick="openCreateModal()">
                            <i class="bi bi-plus-circle me-2"></i>เพิ่ม Role
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="card stat-card blue h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Role ทั้งหมด</h6>
                            <h3 class="mb-0 text-primary">{{ $roles->total() }}</h3>
                        </div>
                        <div class="text-primary opacity-25">
                            <i class="bi bi-shield-lock fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="card stat-card green h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">System Role</h6>
                            <h3 class="mb-0 text-success">{{ $roles->where('is_system', true)->count() }}</h3>
                        </div>
                        <div class="text-success opacity-25">
                            <i class="bi bi-gear fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="card stat-card purple h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">Custom Role</h6>
                            <h3 class="mb-0 text-purple">{{ $roles->where('is_system', false)->count() }}</h3>
                        </div>
                        <div class="text-secondary opacity-25">
                            <i class="bi bi-person-badge fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Alert Messages -->
    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    @if(session('error'))
    <div class="alert alert-danger alert-dismissible fade show" role="alert">
        <i class="bi bi-exclamation-circle me-2"></i>{{ session('error') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <!-- Roles Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-list-ul me-2 text-primary"></i>รายการ Role</h5>
                <div class="input-group" style="width: 250px;">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" id="searchInput" placeholder="ค้นหา Role...">
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>ชื่อ Role</th>
                            <th>คำอธิบาย</th>
                            <th class="text-center">จำนวน Users</th>
                            <th class="text-center">สถานะ</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($roles as $role)
                        <tr>
                            <td>
                                <div class="fw-medium">{{ $role->name }}</div>
                                @if($role->permissions->count() > 0)
                                <div class="mt-1">
                                    @foreach($role->permissions->take(3) as $permission)
                                    <span class="badge bg-light text-dark badge-permission">{{ $permission->module }}</span>
                                    @endforeach
                                    @if($role->permissions->count() > 3)
                                    <span class="badge bg-secondary badge-permission">+{{ $role->permissions->count() - 3 }}</span>
                                    @endif
                                </div>
                                @endif
                            </td>
                            <td>
                                <span class="text-muted">{{ $role->description ?? '-' }}</span>
                            </td>
                            <td class="text-center">
                                <span class="badge bg-primary">{{ $role->users_count }} คน</span>
                            </td>
                            <td class="text-center">
                                @if($role->is_system)
                                <span class="badge bg-warning text-dark">System</span>
                                @else
                                <span class="badge bg-info">Custom</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="openEditModal('{{ $role->id }}')" title="แก้ไข">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ $role->id }}', '{{ $role->name }}', {{ $role->users_count }}, {{ $role->is_system ? 'true' : 'false' }})" title="ลบ" @if($role->users_count > 0) disabled @endif>
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                ไม่พบรายการ Role
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($roles->hasPages())
        <div class="card-footer bg-white">
            {{ $roles->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Role Modal (Create/Edit) -->
<div class="modal fade" id="roleModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="roleForm" autocomplete="off">
                @csrf
                <input type="hidden" id="roleId" name="role_id">
                <div class="modal-header bg-gradient-primary text-white">
                    <h5 class="modal-title" id="modalTitle"><i class="bi bi-plus-circle me-2"></i>เพิ่ม Role ใหม่</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">ชื่อ Role <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">คำอธิบาย</label>
                            <input type="text" class="form-control" id="description" name="description">
                        </div>
                        <div class="col-12">
                            <label class="form-label mb-3">สิทธิ์การเข้าถึง</label>
                            <div class="row g-2">
                                <div class="col-md-6 col-lg-4">
                                    <div class="permission-checkbox d-flex align-items-center">
                                        <div class="permission-icon text-primary">
                                            <i class="bi bi-people"></i>
                                        </div>
                                        <div class="form-check mb-0">
                                            <input class="form-check-input" type="checkbox" id="perm_patients" name="permissions[]" value="patients">
                                            <label class="form-check-label" for="perm_patients">จัดการคนไข้</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="permission-checkbox d-flex align-items-center">
                                        <div class="permission-icon text-success">
                                            <i class="bi bi-calendar-check"></i>
                                        </div>
                                        <div class="form-check mb-0">
                                            <input class="form-check-input" type="checkbox" id="perm_appointments" name="permissions[]" value="appointments">
                                            <label class="form-check-label" for="perm_appointments">จัดการนัดหมาย</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="permission-checkbox d-flex align-items-center">
                                        <div class="permission-icon text-warning">
                                            <i class="bi bi-receipt"></i>
                                        </div>
                                        <div class="form-check mb-0">
                                            <input class="form-check-input" type="checkbox" id="perm_billing" name="permissions[]" value="billing">
                                            <label class="form-check-label" for="perm_billing">ระบบ Billing</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="permission-checkbox d-flex align-items-center">
                                        <div class="permission-icon text-info">
                                            <i class="bi bi-clipboard2-pulse"></i>
                                        </div>
                                        <div class="form-check mb-0">
                                            <input class="form-check-input" type="checkbox" id="perm_services" name="permissions[]" value="services">
                                            <label class="form-check-label" for="perm_services">จัดการบริการ</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="permission-checkbox d-flex align-items-center">
                                        <div class="permission-icon text-secondary">
                                            <i class="bi bi-graph-up"></i>
                                        </div>
                                        <div class="form-check mb-0">
                                            <input class="form-check-input" type="checkbox" id="perm_reports" name="permissions[]" value="reports">
                                            <label class="form-check-label" for="perm_reports">ดูรายงาน</label>
                                        </div>
                                    </div>
                                </div>
                                <div class="col-md-6 col-lg-4">
                                    <div class="permission-checkbox d-flex align-items-center">
                                        <div class="permission-icon text-danger">
                                            <i class="bi bi-gear"></i>
                                        </div>
                                        <div class="form-check mb-0">
                                            <input class="form-check-input" type="checkbox" id="perm_settings" name="permissions[]" value="settings">
                                            <label class="form-check-label" for="perm_settings">ตั้งค่าระบบ</label>
                                        </div>
                                    </div>
                                </div>
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
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title"><i class="bi bi-exclamation-triangle me-2"></i>ยืนยันการลบ</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <p>คุณต้องการลบ Role "<strong id="deleteRoleName"></strong>" ใช่หรือไม่?</p>
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
    const roleModal = new bootstrap.Modal(document.getElementById('roleModal'));
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    let deleteRoleId = null;

    // Open create modal
    window.openCreateModal = function() {
        document.getElementById('roleForm').reset();
        document.getElementById('roleId').value = '';
        document.getElementById('modalTitle').innerHTML = '<i class="bi bi-plus-circle me-2"></i>เพิ่ม Role ใหม่';

        // Uncheck all permissions
        document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = false);
    };

    // Open edit modal
    window.openEditModal = function(id) {
        fetch('{{ url('/roles') }}/' + id + '/edit', {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('roleId').value = data.id;
            document.getElementById('name').value = data.name;
            document.getElementById('description').value = data.description || '';

            // Reset all checkboxes first
            document.querySelectorAll('input[name="permissions[]"]').forEach(cb => cb.checked = false);

            // Check the permissions this role has
            if (data.permissions && data.permissions.length > 0) {
                data.permissions.forEach(module => {
                    const checkbox = document.querySelector(`input[name="permissions[]"][value="${module}"]`);
                    if (checkbox) checkbox.checked = true;
                });
            }

            document.getElementById('modalTitle').innerHTML = '<i class="bi bi-pencil me-2"></i>แก้ไข Role';
            roleModal.show();
        })
        .catch(error => {
            showAlert('เกิดข้อผิดพลาดในการโหลดข้อมูล', 'danger');
        });
    };

    // Form submit
    document.getElementById('roleForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const roleId = document.getElementById('roleId').value;
        const formData = new FormData(this);

        let url = '{{ url('/roles') }}';
        let method = 'POST';

        if (roleId) {
            url = '{{ url('/roles') }}/' + roleId;
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
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                roleModal.hide();
                showAlert(data.message, 'success');
                setTimeout(() => location.reload(), 1000);
            } else {
                showAlert(data.message || 'เกิดข้อผิดพลาด', 'danger');
            }
        })
        .catch(error => {
            showAlert('เกิดข้อผิดพลาดในการบันทึกข้อมูล', 'danger');
        });
    });

    // Confirm delete
    window.confirmDelete = function(id, name, usersCount, isSystem) {
        if (usersCount > 0) {
            showAlert('ไม่สามารถลบ Role ที่มีผู้ใช้อยู่ได้', 'warning');
            return;
        }

        deleteRoleId = id;
        document.getElementById('deleteRoleName').textContent = name;
        deleteModal.show();
    };

    // Delete role
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (!deleteRoleId) return;

        fetch('{{ url('/roles') }}/' + deleteRoleId, {
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
                showAlert(data.message || 'เกิดข้อผิดพลาดในการลบ', 'danger');
            }
        })
        .catch(error => {
            showAlert('เกิดข้อผิดพลาดในการลบข้อมูล', 'danger');
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
        alertDiv.className = `alert alert-${type} alert-dismissible fade show position-fixed top-0 start-50 translate-middle-x mt-3`;
        alertDiv.style.zIndex = '9999';
        alertDiv.innerHTML = `
            <i class="bi bi-${type === 'success' ? 'check-circle' : type === 'warning' ? 'exclamation-triangle' : 'exclamation-circle'} me-2"></i>
            ${message}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
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
