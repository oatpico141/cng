@extends('layouts.app')

@section('title', 'Super Admin - จัดการระบบ')

@section('content')
<div class="container-fluid py-4">
    <div class="row mb-4">
        <div class="col-12">
            <div class="card border-0 shadow-sm" style="background: linear-gradient(135deg, #dc2626, #ef4444); color: white;">
                <div class="card-body">
                    <h2 class="mb-1"><i class="bi bi-shield-lock-fill me-2"></i>Super Admin Control Panel</h2>
                    <p class="mb-0 opacity-75">ระบบจัดการผู้ใช้งานระดับสูงสุด - เฉพาะ Super Admin เท่านั้น</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Users Management -->
    <div class="row">
        <div class="col-12">
            <div class="card border-0 shadow-sm">
                <div class="card-header bg-white border-bottom d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-people-fill me-2"></i>จัดการผู้ใช้งานทั้งหมด</h5>
                    <button class="btn btn-primary btn-sm" onclick="showAddUserModal()">
                        <i class="bi bi-plus-lg me-1"></i>เพิ่มผู้ใช้ใหม่
                    </button>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th width="5%">#</th>
                                    <th width="20%">ชื่อผู้ใช้</th>
                                    <th width="20%">ชื่อ-นามสกุล</th>
                                    <th width="15%">อีเมล</th>
                                    <th width="10%">Role</th>
                                    <th width="10%">สาขา</th>
                                    <th width="10%">สถานะ</th>
                                    <th width="10%" class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody id="usersTable">
                                <tr>
                                    <td colspan="8" class="text-center py-4">
                                        <div class="spinner-border text-primary" role="status">
                                            <span class="visually-hidden">Loading...</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add/Edit User Modal -->
<div class="modal fade" id="userModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="userModalTitle">เพิ่มผู้ใช้ใหม่</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <form id="userForm">
                    <input type="hidden" id="user_id" name="user_id">

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ชื่อผู้ใช้ (Username) <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="username" name="username" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">รหัสผ่าน <span class="text-danger">*</span></label>
                            <input type="password" class="form-control" id="password" name="password">
                            <small class="text-muted">เว้นว่างไว้หากไม่ต้องการเปลี่ยน</small>
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">ชื่อ <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">อีเมล</label>
                            <input type="email" class="form-control" id="email" name="email">
                        </div>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Role <span class="text-danger">*</span></label>
                            <select class="form-select" id="role_id" name="role_id" required>
                                <option value="">-- เลือก Role --</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">สาขา <span class="text-danger">*</span></label>
                            <select class="form-select" id="branch_id" name="branch_id" required>
                                <option value="">-- เลือกสาขา --</option>
                            </select>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">เบอร์โทร</label>
                        <input type="text" class="form-control" id="phone" name="phone">
                    </div>

                    <div class="form-check form-switch mb-3">
                        <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                        <label class="form-check-label" for="is_active">เปิดใช้งาน</label>
                    </div>
                </form>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-primary" onclick="saveUser()">
                    <i class="bi bi-save me-1"></i>บันทึก
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const BASE_URL = '{{ url('/') }}';

let roles = [];
let branches = [];

// Load initial data
$(document).ready(function() {
    loadRoles();
    loadBranches();
    loadUsers();
});

// Load Roles
function loadRoles() {
    fetch(BASE_URL + '/api/roles')
        .then(res => res.json())
        .then(data => {
            roles = data;
            const select = $('#role_id');
            select.empty().append('<option value="">-- เลือก Role --</option>');
            data.forEach(role => {
                select.append(`<option value="${role.id}">${role.name}</option>`);
            });
        });
}

// Load Branches
function loadBranches() {
    fetch(BASE_URL + '/api/branches')
        .then(res => res.json())
        .then(data => {
            branches = data;
            const select = $('#branch_id');
            select.empty().append('<option value="">-- เลือกสาขา --</option>');
            data.forEach(branch => {
                select.append(`<option value="${branch.id}">${branch.name}</option>`);
            });
        });
}

// Load Users
function loadUsers() {
    fetch(BASE_URL + '/api/super-admin/users')
        .then(res => res.json())
        .then(data => {
            renderUsers(data);
        })
        .catch(err => {
            $('#usersTable').html('<tr><td colspan="8" class="text-center text-danger">เกิดข้อผิดพลาด: ' + err.message + '</td></tr>');
        });
}

// Render Users Table
function renderUsers(users) {
    const tbody = $('#usersTable');
    tbody.empty();

    if (users.length === 0) {
        tbody.append('<tr><td colspan="8" class="text-center text-muted">ไม่มีข้อมูล</td></tr>');
        return;
    }

    users.forEach((user, index) => {
        const statusBadge = user.is_active
            ? '<span class="badge bg-success">ใช้งาน</span>'
            : '<span class="badge bg-danger">ปิดใช้งาน</span>';

        tbody.append(`
            <tr>
                <td>${index + 1}</td>
                <td><strong>${user.username}</strong></td>
                <td>${user.name}</td>
                <td>${user.email || '-'}</td>
                <td><span class="badge bg-primary">${user.role?.name || '-'}</span></td>
                <td>${user.branch?.name || '-'}</td>
                <td>${statusBadge}</td>
                <td class="text-center">
                    <button class="btn btn-sm btn-warning" onclick="editUser('${user.id}')" title="แก้ไข">
                        <i class="bi bi-pencil"></i>
                    </button>
                    <button class="btn btn-sm btn-danger" onclick="deleteUser('${user.id}', '${user.username}')" title="ลบ">
                        <i class="bi bi-trash"></i>
                    </button>
                </td>
            </tr>
        `);
    });
}

// Show Add User Modal
function showAddUserModal() {
    $('#userModalTitle').text('เพิ่มผู้ใช้ใหม่');
    $('#userForm')[0].reset();
    $('#user_id').val('');
    $('#password').prop('required', true);
    $('#is_active').prop('checked', true);
    new bootstrap.Modal($('#userModal')).show();
}

// Edit User
function editUser(userId) {
    fetch(BASE_URL + `/api/super-admin/users/${userId}`)
        .then(res => res.json())
        .then(user => {
            $('#userModalTitle').text('แก้ไขผู้ใช้');
            $('#user_id').val(user.id);
            $('#username').val(user.username);
            $('#name').val(user.name);
            $('#email').val(user.email);
            $('#role_id').val(user.role_id);
            $('#branch_id').val(user.branch_id);
            $('#phone').val(user.phone);
            $('#is_active').prop('checked', user.is_active == 1);
            $('#password').prop('required', false).val('');

            new bootstrap.Modal($('#userModal')).show();
        });
}

// Save User
function saveUser() {
    const userId = $('#user_id').val();
    const formData = {
        username: $('#username').val(),
        name: $('#name').val(),
        email: $('#email').val(),
        role_id: $('#role_id').val(),
        branch_id: $('#branch_id').val(),
        phone: $('#phone').val(),
        is_active: $('#is_active').is(':checked') ? 1 : 0
    };

    const password = $('#password').val();
    if (password) {
        formData.password = password;
    }

    const url = userId
        ? BASE_URL + `/api/super-admin/users/${userId}`
        : BASE_URL + '/api/super-admin/users';

    const method = userId ? 'PUT' : 'POST';

    fetch(url, {
        method: method,
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        },
        body: JSON.stringify(formData)
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(userId ? 'แก้ไขผู้ใช้สำเร็จ' : 'เพิ่มผู้ใช้สำเร็จ');
            bootstrap.Modal.getInstance($('#userModal')).hide();
            loadUsers();
        } else {
            alert('เกิดข้อผิดพลาด: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(err => alert('เกิดข้อผิดพลาด: ' + err.message));
}

// Delete User
function deleteUser(userId, username) {
    if (!confirm(`ต้องการลบผู้ใช้ "${username}" ใช่หรือไม่?`)) return;

    fetch(BASE_URL + `/api/super-admin/users/${userId}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert('ลบผู้ใช้สำเร็จ');
            loadUsers();
        } else {
            alert('เกิดข้อผิดพลาด: ' + (data.message || 'Unknown error'));
        }
    })
    .catch(err => alert('เกิดข้อผิดพลาด: ' + err.message));
}
</script>
@endpush
