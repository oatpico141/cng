@extends('layouts.app')

@section('title', 'จัดการแพ็คเกจคอร์ส - GCMS')

@push('styles')
<link href="{{ asset('css/gcms-blue-theme.css') }}" rel="stylesheet">
<style>
    /* Blue Theme for Course Packages */
    .page-header {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        border-radius: 16px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
        box-shadow: 0 4px 16px rgba(37, 99, 235, 0.2);
    }

    .stat-card {
        border-left: 4px solid;
        transition: transform 0.2s;
        border-radius: 12px;
        box-shadow: 0 2px 8px rgba(0, 0, 0, 0.05);
    }

    .stat-card:hover {
        transform: translateY(-3px);
        box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
    }

    .stat-card.blue { border-left-color: #3b82f6; }
    .stat-card.sky { border-left-color: #0ea5e9; }
    .stat-card.navy { border-left-color: #1e3a8a; }

    .badge-blue {
        background: linear-gradient(135deg, #7dd3fc 0%, #3b82f6 100%);
        color: white;
    }

    .table thead th {
        background: #f0f9ff;
        color: #0c4a6e;
        font-weight: 600;
        border-bottom: 2px solid #bae6fd;
    }

    .btn-primary {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        border: none;
        font-weight: 600;
    }

    .btn-primary:hover {
        background: linear-gradient(135deg, #0284c7 0%, #0369a1 100%);
        transform: translateY(-1px);
        box-shadow: 0 4px 12px rgba(14, 165, 233, 0.3);
    }

    .modal-header {
        background: linear-gradient(135deg, #3b82f6 0%, #2563eb 100%);
        color: white;
    }

    .form-label {
        color: #0c4a6e;
        font-weight: 600;
    }

    .form-control:focus, .form-select:focus {
        border-color: #3b82f6;
        box-shadow: 0 0 0 0.2rem rgba(59, 130, 246, 0.25);
    }

    /* Special styling for paid/bonus sessions */
    .session-input-group {
        background: #f0f9ff;
        padding: 1rem;
        border-radius: 10px;
        border: 2px solid #bae6fd;
    }

    .session-input-group label {
        font-size: 0.9rem;
        margin-bottom: 0.5rem;
    }

    .total-display {
        background: #1e3a8a;
        color: white;
        padding: 0.75rem;
        border-radius: 8px;
        text-align: center;
        font-weight: 600;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Blue Theme Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div>
                <h2 class="mb-2"><i class="bi bi-box-seam me-2"></i>จัดการแพ็คเกจคอร์ส</h2>
                <p class="mb-0 opacity-90">จัดการคอร์สการรักษาของคลินิก - ผูกกับบริการหลัก</p>
            </div>
            <div class="mt-3 mt-md-0">
                <button type="button" class="btn btn-light btn-lg" data-bs-toggle="modal" data-bs-target="#packageModal" onclick="resetForm()">
                    <i class="bi bi-plus-circle me-2"></i>สร้างคอร์สใหม่
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards -->
    <div class="row mb-4">
        <div class="col-md-4 mb-3">
            <div class="card stat-card blue h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">คอร์สทั้งหมด</h6>
                            <h3 class="mb-0 text-primary">{{ $packages->total() }}</h3>
                        </div>
                        <div class="text-primary opacity-25">
                            <i class="bi bi-box-seam fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card stat-card sky h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">คอร์สที่ใช้งาน</h6>
                            <h3 class="mb-0" style="color: #0ea5e9;">{{ $packages->where('is_active', true)->count() }}</h3>
                        </div>
                        <div class="opacity-25" style="color: #0ea5e9;">
                            <i class="bi bi-check-circle fs-1"></i>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-4 mb-3">
            <div class="card stat-card navy h-100">
                <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h6 class="text-muted mb-2">บริการที่ผูก</h6>
                            <h3 class="mb-0" style="color: #1e3a8a;">{{ $services->count() }}</h3>
                        </div>
                        <div class="opacity-25" style="color: #1e3a8a;">
                            <i class="bi bi-link-45deg fs-1"></i>
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

    <!-- Main Table -->
    <div class="card">
        <div class="card-body">
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>รหัส</th>
                            <th>ชื่อคอร์ส</th>
                            <th>บริการหลัก</th>
                            <th class="text-center">จ่าย/แถม</th>
                            <th class="text-center">รวมครั้ง</th>
                            <th class="text-end">ราคา</th>
                            <th class="text-center">อายุ (วัน)</th>
                            <th class="text-center">สถานะ</th>
                            <th class="text-center">จัดการ</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($packages as $package)
                        <tr>
                            <td>{{ $package->code ?? '-' }}</td>
                            <td>
                                <strong class="text-primary">{{ $package->name }}</strong>
                                @if($package->description)
                                    <br><small class="text-muted">{{ Str::limit($package->description, 50) }}</small>
                                @endif
                            </td>
                            <td>
                                @if($package->service)
                                    <span class="badge bg-info">{{ $package->service->name }}</span>
                                @else
                                    <span class="text-danger">ไม่ได้ผูกบริการ</span>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    $paid = $package->paid_sessions ?? $package->total_sessions;
                                    $bonus = $package->bonus_sessions ?? 0;
                                @endphp
                                <span class="badge badge-blue">{{ $paid }}/{{ $bonus }}</span>
                            </td>
                            <td class="text-center">
                                <strong>{{ $package->total_sessions }}</strong> ครั้ง
                            </td>
                            <td class="text-end">
                                <strong style="color: #0284c7;">฿{{ number_format($package->price, 0) }}</strong>
                            </td>
                            <td class="text-center">{{ $package->validity_days }}</td>
                            <td class="text-center">
                                @if($package->is_active)
                                    <span class="badge bg-success">ใช้งาน</span>
                                @else
                                    <span class="badge bg-secondary">ปิดใช้งาน</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <button type="button" class="btn btn-sm btn-outline-primary me-1"
                                        onclick="editPackage({{ json_encode($package) }})"
                                        data-bs-toggle="modal" data-bs-target="#packageModal">
                                    <i class="bi bi-pencil"></i>
                                </button>
                                <button type="button" class="btn btn-sm btn-outline-danger"
                                        onclick="confirmDelete('{{ $package->id }}', '{{ $package->name }}')"
                                        data-bs-toggle="modal" data-bs-target="#deleteModal">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="9" class="text-center py-5 text-muted">
                                <i class="bi bi-inbox fs-1 d-block mb-2"></i>
                                ยังไม่มีคอร์สในระบบ
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            <div class="d-flex justify-content-end mt-3">
                {{ $packages->links() }}
            </div>
        </div>
    </div>
</div>

<!-- Enhanced Create/Edit Modal with Blue Theme -->
<div class="modal fade" id="packageModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <form id="packageForm" method="POST" action="/course-packages" autocomplete="off">
                @csrf
                <input type="hidden" name="_method" id="formMethod" value="POST">

                <div class="modal-header">
                    <h5 class="modal-title">
                        <i class="bi bi-box-seam me-2"></i>
                        <span id="modalTitle">สร้างคอร์สใหม่</span>
                    </h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                </div>

                <div class="modal-body">
                    <div class="row g-3">
                        <!-- Course Name & Code -->
                        <div class="col-md-8">
                            <label class="form-label">ชื่อคอร์ส <span class="text-danger">*</span></label>
                            <input type="text" class="form-control" id="name" name="name" required
                                   placeholder="เช่น คอร์สนวดบำบัด 5 ครั้ง แถม 1">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">รหัสคอร์ส</label>
                            <input type="text" class="form-control" id="code" name="code"
                                   placeholder="เช่น PKG001">
                        </div>

                        <!-- CRITICAL: Service Selection (REQUIRED) -->
                        <div class="col-12">
                            <label class="form-label">บริการหลักที่ผูก <span class="text-danger">* (จำเป็น)</span></label>
                            <select class="form-select" id="service_id" name="service_id" required>
                                <option value="">-- เลือกบริการที่จะผูกกับคอร์สนี้ --</option>
                                @foreach($services as $service)
                                    <option value="{{ $service->id }}" data-price="{{ $service->default_price }}">
                                        {{ $service->name }} (฿{{ number_format($service->default_price, 0) }}/ครั้ง)
                                    </option>
                                @endforeach
                            </select>
                            <small class="text-muted">คอร์สต้องผูกกับบริการหลัก 1 รายการเพื่อระบุว่าเป็นคอร์สของบริการใด</small>
                        </div>

                        <!-- CRITICAL: Paid & Bonus Sessions -->
                        <div class="col-12">
                            <div class="session-input-group">
                                <h6 class="mb-3" style="color: #0369a1;"><i class="bi bi-calculator me-2"></i>จำนวนครั้งในคอร์ส</h6>
                                <div class="row g-3">
                                    <div class="col-md-4">
                                        <label class="form-label">จำนวนครั้งที่จ่าย <span class="text-danger">*</span></label>
                                        <input type="number" class="form-control" id="paid_sessions" name="paid_sessions"
                                               min="1" required onchange="calculateTotal()" placeholder="5">
                                        <small class="text-muted">จำนวนที่ลูกค้าจ่ายเงิน</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">จำนวนครั้งแถม</label>
                                        <input type="number" class="form-control" id="bonus_sessions" name="bonus_sessions"
                                               min="0" value="0" onchange="calculateTotal()" placeholder="1">
                                        <small class="text-muted">จำนวนที่แถมฟรี</small>
                                    </div>
                                    <div class="col-md-4">
                                        <label class="form-label">รวมทั้งหมด</label>
                                        <div class="total-display">
                                            <span id="total_sessions_display">0</span> ครั้ง
                                        </div>
                                        <input type="hidden" id="total_sessions" name="total_sessions">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Price & Validity -->
                        <div class="col-md-6">
                            <label class="form-label">ราคาคอร์ส <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">฿</span>
                                <input type="number" class="form-control" id="price" name="price"
                                       min="0" step="0.01" required placeholder="2500">
                            </div>
                            <small class="text-muted">ราคารวมทั้งคอร์ส</small>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label">อายุการใช้งาน (วัน) <span class="text-danger">*</span></label>
                            <input type="number" class="form-control" id="validity_days" name="validity_days"
                                   min="1" required placeholder="90">
                            <small class="text-muted">นับจากวันที่ซื้อคอร์ส</small>
                        </div>

                        <!-- Commission Settings -->
                        <div class="col-12">
                            <hr>
                            <h6 class="text-primary mb-3"><i class="bi bi-cash-coin me-2"></i>ค่าคอมมิชชั่น</h6>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">คอมมิชชั่นรวม (%)</label>
                            <input type="number" class="form-control" id="commission_rate" name="commission_rate"
                                   step="0.01" min="0" max="100">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">คอมมิชชั่นต่อครั้ง (บาท)</label>
                            <input type="number" class="form-control" id="per_session_commission_rate"
                                   name="per_session_commission_rate" step="0.01" min="0">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label">DF Rate (%)</label>
                            <input type="number" class="form-control" id="df_rate" name="df_rate"
                                   step="0.01" min="0" max="100">
                        </div>

                        <!-- Options -->
                        <div class="col-12">
                            <hr>
                            <h6 class="text-primary mb-3"><i class="bi bi-gear me-2"></i>ตัวเลือกเพิ่มเติม</h6>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="is_active" name="is_active" checked>
                                <label class="form-check-label" for="is_active">เปิดใช้งาน</label>
                            </div>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="allow_buy_and_use" name="allow_buy_and_use">
                                <label class="form-check-label" for="allow_buy_and_use">ซื้อและใช้ทันที</label>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="form-check form-switch">
                                <input class="form-check-input" type="checkbox" id="allow_buy_for_later" name="allow_buy_for_later">
                                <label class="form-check-label" for="allow_buy_for_later">ซื้อไว้ใช้ทีหลัง</label>
                            </div>
                            <div class="form-check form-switch mt-2">
                                <input class="form-check-input" type="checkbox" id="allow_retroactive" name="allow_retroactive">
                                <label class="form-check-label" for="allow_retroactive">ใช้ย้อนหลังได้</label>
                            </div>
                        </div>

                        <!-- Description -->
                        <div class="col-12">
                            <label class="form-label">รายละเอียด</label>
                            <textarea class="form-control" id="description" name="description" rows="3"
                                      placeholder="อธิบายรายละเอียดของคอร์ส..."></textarea>
                        </div>
                    </div>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-save me-2"></i>บันทึก
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
                <p>คุณแน่ใจหรือไม่ที่จะลบคอร์ส "<strong id="deletePackageName"></strong>"?</p>
                <p class="text-danger mb-0">การลบนี้ไม่สามารถย้อนกลับได้</p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <form id="deleteForm" method="POST" style="display: inline;">
                    @csrf
                    @method('DELETE')
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-trash me-2"></i>ลบ
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Calculate total sessions
    function calculateTotal() {
        const paid = parseInt(document.getElementById('paid_sessions').value) || 0;
        const bonus = parseInt(document.getElementById('bonus_sessions').value) || 0;
        const total = paid + bonus;

        document.getElementById('total_sessions').value = total;
        document.getElementById('total_sessions_display').textContent = total;
    }

    // Reset form for create
    function resetForm() {
        document.getElementById('packageForm').reset();
        document.getElementById('packageForm').action = "/course-packages";
        document.getElementById('formMethod').value = 'POST';
        document.getElementById('modalTitle').textContent = 'สร้างคอร์สใหม่';
        document.getElementById('is_active').checked = true;
        document.getElementById('total_sessions_display').textContent = '0';
    }

    // Edit package
    function editPackage(package) {
        document.getElementById('packageForm').action = "/course-packages/" + package.id;
        document.getElementById('formMethod').value = 'PUT';
        document.getElementById('modalTitle').textContent = 'แก้ไขคอร์ส';

        // Fill form fields
        document.getElementById('name').value = package.name || '';
        document.getElementById('code').value = package.code || '';
        document.getElementById('description').value = package.description || '';
        document.getElementById('service_id').value = package.service_id || '';
        document.getElementById('price').value = package.price || '';

        // Handle sessions - if paid/bonus not in DB, calculate from total
        if (package.paid_sessions !== undefined) {
            document.getElementById('paid_sessions').value = package.paid_sessions;
            document.getElementById('bonus_sessions').value = package.bonus_sessions || 0;
        } else {
            // Fallback for existing data
            document.getElementById('paid_sessions').value = package.total_sessions || 1;
            document.getElementById('bonus_sessions').value = 0;
        }

        calculateTotal();

        document.getElementById('validity_days').value = package.validity_days || '';
        document.getElementById('commission_rate').value = package.commission_rate || '';
        document.getElementById('per_session_commission_rate').value = package.per_session_commission_rate || '';
        document.getElementById('df_rate').value = package.df_rate || '';
        document.getElementById('is_active').checked = package.is_active;
        document.getElementById('allow_buy_and_use').checked = package.allow_buy_and_use;
        document.getElementById('allow_buy_for_later').checked = package.allow_buy_for_later;
        document.getElementById('allow_retroactive').checked = package.allow_retroactive;
    }

    // Delete confirmation
    function confirmDelete(id, name) {
        document.getElementById('deletePackageName').textContent = name;
        document.getElementById('deleteForm').action = '/course-packages/' + id;
    }
</script>
@endpush