@extends('layouts.app')

@section('title', 'จัดการสาขา - GCMS')

@push('styles')
<style>
    .bg-gradient-blue {
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
    .stat-card.gray { border-left-color: #6b7280; }
    .btn-calm-blue {
        background-color: var(--calm-blue-500, #3b82f6);
        border-color: var(--calm-blue-500, #3b82f6);
        color: white;
    }
    .btn-calm-blue:hover {
        background-color: var(--calm-blue-600, #2563eb);
        border-color: var(--calm-blue-600, #2563eb);
        color: white;
    }
    .text-calm-blue {
        color: var(--calm-blue-500, #3b82f6);
    }
    .bg-calm-blue-light {
        background-color: var(--calm-blue-50, #eff6ff);
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="row mb-4">
        <div class="col-12">
            <div class="bg-gradient-blue text-white rounded-3 p-4 shadow-sm">
                <div class="d-flex justify-content-between align-items-center flex-wrap">
                    <div>
                        <h2 class="mb-2"><i class="bi bi-building me-2"></i>จัดการสาขา</h2>
                        <p class="mb-0 opacity-90">จัดการข้อมูลสาขาทั้งหมดของคลินิก</p>
                    </div>
                    <div class="mt-3 mt-md-0">
                        <button class="btn btn-light" data-bs-toggle="modal" data-bs-target="#branchModal" onclick="openCreateModal()">
                            <i class="bi bi-plus-circle me-2"></i>เพิ่มสาขา
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
                            <h6 class="text-muted mb-2">สาขาทั้งหมด</h6>
                            <h3 class="mb-0 text-calm-blue">{{ $branches->total() }}</h3>
                        </div>
                        <div class="text-calm-blue opacity-25">
                            <i class="bi bi-building fs-1"></i>
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
                            <h6 class="text-muted mb-2">เปิดใช้งาน</h6>
                            <h3 class="mb-0 text-success">{{ $branches->where('is_active', true)->count() }}</h3>
                        </div>
                        <div class="text-success opacity-25">
                            <i class="bi bi-check-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 col-sm-6 mb-3">
            <div class="card stat-card gray h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">ปิดใช้งาน</h6>
                            <h3 class="mb-0 text-secondary">{{ $branches->where('is_active', false)->count() }}</h3>
                        </div>
                        <div class="text-secondary opacity-25">
                            <i class="bi bi-x-circle fs-1"></i>
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

    <!-- Branches Table -->
    <div class="card shadow-sm">
        <div class="card-header bg-white border-bottom">
            <div class="d-flex justify-content-between align-items-center">
                <h5 class="mb-0"><i class="bi bi-list-ul me-2 text-calm-blue"></i>รายการสาขา</h5>
                <div class="input-group" style="width: 250px;">
                    <span class="input-group-text"><i class="bi bi-search"></i></span>
                    <input type="text" class="form-control" id="searchInput" placeholder="ค้นหาสาขา...">
                </div>
            </div>
        </div>
        <div class="card-body p-0">
            <div class="table-responsive">
                <table class="table table-hover mb-0">
                    <thead class="table-light">
                        <tr>
                            <th>รหัสสาขา</th>
                            <th>ชื่อสาขา</th>
                            <th>ที่อยู่</th>
                            <th>เบอร์โทร</th>
                            <th class="text-center">สถานะ</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($branches as $branch)
                        <tr>
                            <td><span class="badge bg-secondary">{{ $branch->code ?? '-' }}</span></td>
                            <td>
                                <div class="fw-medium">{{ $branch->name }}</div>
                                @if($branch->email)
                                <small class="text-muted"><i class="bi bi-envelope me-1"></i>{{ $branch->email }}</small>
                                @endif
                            </td>
                            <td>{{ Str::limit($branch->address, 50) ?? '-' }}</td>
                            <td>{{ $branch->phone ?? '-' }}</td>
                            <td class="text-center">
                                @if($branch->is_active)
                                <span class="badge bg-success">เปิดใช้งาน</span>
                                @else
                                <span class="badge bg-secondary">ปิดใช้งาน</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button class="btn btn-sm btn-outline-primary me-1" onclick="openEditModal('{{ $branch->id }}')" title="แก้ไข">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button class="btn btn-sm btn-outline-danger" onclick="confirmDelete('{{ $branch->id }}', '{{ $branch->name }}')" title="ลบ">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="text-center py-4 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                ไม่พบรายการสาขา
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
        @if($branches->hasPages())
        <div class="card-footer bg-white">
            {{ $branches->links() }}
        </div>
        @endif
    </div>
</div>

<!-- Branch Modal (Create/Edit) -->
<div class="modal fade" id="branchModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="branchForm" autocomplete="off">
                @csrf
                <input type="hidden" id="branchId" name="branch_id">
                <div class="modal-header bg-gradient-blue text-white">
                    <h5 class="modal-title" id="modalTitle"><i class="bi bi-plus-circle me-2"></i>เพิ่มสาขาใหม่</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label">ชื่อสาขา <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">รหัสสาขา</label>
                            <input type="text" class="form-control" id="code" name="code">
                        </div>
                        <div class="col-12">
                            <label class="form-label">ที่อยู่</label>
                            <textarea class="form-control" id="address" name="address" rows="2"></textarea>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">เบอร์โทร</label>
                            <input type="text" class="form-control" id="phone" name="phone">
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">อีเมล</label>
                            <input type="email" class="form-control" id="email" name="email">
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
                    <button type="submit" class="btn btn-calm-blue">
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
                <p>คุณต้องการลบสาขา "<strong id="deleteBranchName"></strong>" ใช่หรือไม่?</p>
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
const BASE_URL = '{{ url('/') }}';

document.addEventListener('DOMContentLoaded', function() {
    const branchModal = new bootstrap.Modal(document.getElementById('branchModal'));
    const deleteModal = new bootstrap.Modal(document.getElementById('deleteModal'));
    let deleteBranchId = null;

    // Open create modal
    window.openCreateModal = function() {
        document.getElementById('branchForm').reset();
        document.getElementById('branchId').value = '';
        document.getElementById('modalTitle').innerHTML = '<i class="bi bi-plus-circle me-2"></i>เพิ่มสาขาใหม่';
        document.getElementById('is_active').checked = true;
    };

    // Open edit modal
    window.openEditModal = function(id) {
        fetch(`${BASE_URL}/branches/${id}/edit`, {
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        })
        .then(response => response.json())
        .then(data => {
            document.getElementById('branchId').value = data.id;
            document.getElementById('name').value = data.name;
            document.getElementById('code').value = data.code || '';
            document.getElementById('address').value = data.address || '';
            document.getElementById('phone').value = data.phone || '';
            document.getElementById('email').value = data.email || '';
            document.getElementById('is_active').checked = data.is_active;

            document.getElementById('modalTitle').innerHTML = '<i class="bi bi-pencil me-2"></i>แก้ไขสาขา';
            branchModal.show();
        })
        .catch(error => {
            showAlert('เกิดข้อผิดพลาดในการโหลดข้อมูล', 'danger');
        });
    };

    // Form submit
    document.getElementById('branchForm').addEventListener('submit', function(e) {
        e.preventDefault();

        const branchId = document.getElementById('branchId').value;
        const formData = new FormData(this);

        // Convert is_active checkbox to boolean (1 or 0)
        const isActive = document.getElementById('is_active').checked ? 1 : 0;
        formData.set('is_active', isActive);

        let url = `${BASE_URL}/branches`;
        let method = 'POST';

        if (branchId) {
            url = `${BASE_URL}/branches/${branchId}`;
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
                branchModal.hide();
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
    window.confirmDelete = function(id, name) {
        deleteBranchId = id;
        document.getElementById('deleteBranchName').textContent = name;
        deleteModal.show();
    };

    // Delete branch
    document.getElementById('confirmDeleteBtn').addEventListener('click', function() {
        if (!deleteBranchId) return;

        fetch(`${BASE_URL}/branches/${deleteBranchId}`, {
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
            <i class="bi bi-${type === 'success' ? 'check-circle' : 'exclamation-circle'} me-2"></i>
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
