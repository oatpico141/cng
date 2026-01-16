@extends('layouts.app')

@section('title', 'รายจ่าย - GCMS')

@push('styles')
<style>
    /* CLEAN & SIMPLE EXPENSE PAGE */

    .page-header {
        background: linear-gradient(135deg, #dc2626, #b91c1c);
        border-radius: 12px;
        padding: 1.5rem;
        color: white;
        margin-bottom: 1.25rem;
    }

    /* Summary Card */
    .summary-card {
        background: white;
        border-radius: 10px;
        padding: 1.25rem;
        border: 1px solid #e2e8f0;
        height: 100%;
    }

    .summary-icon {
        width: 48px;
        height: 48px;
        border-radius: 10px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.25rem;
        background: #fee2e2;
        color: #dc2626;
    }

    /* Filter Card */
    .filter-card {
        background: white;
        border-radius: 10px;
        padding: 1rem 1.25rem;
        border: 1px solid #e2e8f0;
        margin-bottom: 1.25rem;
    }

    /* Category Pills */
    .category-pill {
        display: inline-flex;
        align-items: center;
        padding: 0.5rem 0.875rem;
        border-radius: 8px;
        background: #f8fafc;
        margin: 0.25rem;
        font-size: 0.8rem;
    }

    .category-dot {
        width: 8px;
        height: 8px;
        border-radius: 50%;
        margin-right: 0.5rem;
    }

    /* Table */
    .expense-table {
        background: white;
        border-radius: 10px;
        border: 1px solid #e2e8f0;
        overflow: hidden;
    }

    .expense-table thead th {
        background: #f8fafc;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        color: #64748b;
        padding: 0.875rem 1rem;
        border: none;
    }

    .expense-table tbody td {
        padding: 0.875rem 1rem;
        vertical-align: middle;
        border-color: #f1f5f9;
    }

    .expense-table tbody tr:hover {
        background: #f8fafc;
    }

    /* Category Badges */
    .category-badge {
        display: inline-flex;
        align-items: center;
        padding: 0.25rem 0.625rem;
        border-radius: 6px;
        font-size: 0.7rem;
        font-weight: 500;
    }

    .category-badge.rent { background: #fef3c7; color: #92400e; }
    .category-badge.utilities { background: #dbeafe; color: #1e40af; }
    .category-badge.salary { background: #dcfce7; color: #166534; }
    .category-badge.supplies { background: #f3e8ff; color: #7e22ce; }
    .category-badge.maintenance { background: #fed7aa; color: #9a3412; }
    .category-badge.marketing { background: #fce7f3; color: #9d174d; }
    .category-badge.equipment { background: #e0e7ff; color: #3730a3; }
    .category-badge.insurance { background: #ccfbf1; color: #115e59; }
    .category-badge.transport { background: #fef9c3; color: #854d0e; }
    .category-badge.other { background: #f1f5f9; color: #475569; }

    /* Amount */
    .amount-text {
        font-size: 1rem;
        font-weight: 700;
        color: #dc2626;
    }

    /* Empty State */
    .empty-state {
        padding: 3rem 2rem;
        text-align: center;
    }

    .empty-state i {
        font-size: 3.5rem;
        color: #e2e8f0;
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h4 class="mb-1 fw-bold"><i class="bi bi-receipt me-2"></i>รายจ่าย</h4>
                <p class="mb-0 opacity-90 small">{{ $currentBranch->name ?? 'ไม่ระบุสาขา' }}</p>
            </div>
            <button class="btn btn-light btn-sm" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                <i class="bi bi-plus-circle me-1"></i>เพิ่มรายจ่าย
            </button>
        </div>
    </div>

    <!-- Filter -->
    <div class="filter-card">
        <form method="GET" class="row g-2 align-items-end">
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">หมวดหมู่</label>
                <select name="category" class="form-select form-select-sm">
                    <option value="">ทั้งหมด</option>
                    @foreach($categories as $key => $label)
                        <option value="{{ $key }}" {{ $category == $key ? 'selected' : '' }}>{{ $label }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">วันที่เริ่ม</label>
                <input type="date" name="start_date" class="form-control form-control-sm" value="{{ $startDate }}">
            </div>
            <div class="col-md-3">
                <label class="form-label small text-muted mb-1">วันที่สิ้นสุด</label>
                <input type="date" name="end_date" class="form-control form-control-sm" value="{{ $endDate }}">
            </div>
            <div class="col-md-1">
                <button type="submit" class="btn btn-primary btn-sm w-100">
                    <i class="bi bi-search"></i>
                </button>
            </div>
            <div class="col-md-2">
                <a href="{{ route('expenses.index') }}" class="btn btn-outline-secondary btn-sm w-100">
                    <i class="bi bi-arrow-clockwise me-1"></i>รีเซ็ต
                </a>
            </div>
        </form>
    </div>

    <!-- Summary -->
    <div class="row g-3 mb-3">
        <div class="col-md-4">
            <div class="summary-card">
                <div class="d-flex align-items-center">
                    <div class="summary-icon me-3">
                        <i class="bi bi-cash-stack"></i>
                    </div>
                    <div>
                        <div class="text-muted small">รายจ่ายรวม</div>
                        <div class="h3 mb-0 fw-bold text-danger">฿{{ number_format($totalExpenses, 0) }}</div>
                        <div class="small text-muted">{{ $expenses->count() }} รายการ</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="summary-card">
                <div class="text-muted small mb-2"><i class="bi bi-pie-chart me-1"></i>สรุปตามหมวดหมู่</div>
                <div class="d-flex flex-wrap">
                    @forelse($summaryByCategory as $cat => $amount)
                    <div class="category-pill">
                        <span class="category-dot" style="background: {{
                            $cat == 'rent' ? '#f59e0b' :
                            ($cat == 'utilities' ? '#3b82f6' :
                            ($cat == 'salary' ? '#22c55e' :
                            ($cat == 'supplies' ? '#a855f7' :
                            ($cat == 'maintenance' ? '#f97316' :
                            ($cat == 'marketing' ? '#ec4899' :
                            ($cat == 'equipment' ? '#6366f1' :
                            ($cat == 'insurance' ? '#14b8a6' :
                            ($cat == 'transport' ? '#eab308' : '#64748b'))))))))
                        }};"></span>
                        <span class="me-2">{{ $categories[$cat] ?? $cat }}</span>
                        <strong class="text-danger">฿{{ number_format($amount, 0) }}</strong>
                    </div>
                    @empty
                    <small class="text-muted">ไม่มีข้อมูล</small>
                    @endforelse
                </div>
            </div>
        </div>
    </div>

    <!-- Table -->
    <div class="expense-table">
        <table class="table mb-0">
            <thead>
                <tr>
                    <th width="100">วันที่</th>
                    <th width="120">หมวดหมู่</th>
                    <th>รายละเอียด</th>
                    <th width="120">วิธีชำระ</th>
                    <th width="120" class="text-end">จำนวนเงิน</th>
                    <th width="80" class="text-center">จัดการ</th>
                </tr>
            </thead>
            <tbody>
                @forelse($expenses as $expense)
                <tr>
                    <td>
                        <div class="fw-medium">{{ $expense->expense_date->locale('th')->isoFormat('D MMM') }}</div>
                        <small class="text-muted">{{ $expense->expense_date->year + 543 }}</small>
                    </td>
                    <td>
                        <span class="category-badge {{ $expense->category }}">
                            {{ $expense->category_label }}
                        </span>
                    </td>
                    <td>
                        <div class="fw-medium">{{ $expense->description }}</div>
                        @if($expense->receipt_number)
                            <small class="text-muted"><i class="bi bi-receipt me-1"></i>{{ $expense->receipt_number }}</small>
                        @endif
                        @if($expense->notes)
                            <small class="text-muted d-block"><i class="bi bi-chat-left-text me-1"></i>{{ $expense->notes }}</small>
                        @endif
                    </td>
                    <td>
                        @if($expense->payment_method)
                            <span class="text-muted small">
                                <i class="bi bi-{{ $expense->payment_method == 'cash' ? 'cash' : ($expense->payment_method == 'transfer' ? 'bank' : ($expense->payment_method == 'credit_card' ? 'credit-card' : 'file-text')) }}"></i>
                                {{ $expense->payment_method_label }}
                            </span>
                        @else
                            <span class="text-muted">-</span>
                        @endif
                    </td>
                    <td class="text-end">
                        <span class="amount-text">฿{{ number_format($expense->amount, 0) }}</span>
                    </td>
                    <td class="text-center">
                        <button class="btn btn-sm btn-outline-primary me-1" onclick="editExpense('{{ $expense->id }}')" title="แก้ไข" style="padding: 0.25rem 0.5rem;">
                            <i class="bi bi-pencil"></i>
                        </button>
                        <button class="btn btn-sm btn-outline-danger" onclick="deleteExpense('{{ $expense->id }}')" title="ลบ" style="padding: 0.25rem 0.5rem;">
                            <i class="bi bi-trash"></i>
                        </button>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="bi bi-inbox d-block"></i>
                            <h6 class="text-muted">ไม่มีรายจ่ายในช่วงเวลานี้</h6>
                            <p class="text-muted small mb-3">ลองเปลี่ยนตัวกรองหรือเพิ่มรายจ่ายใหม่</p>
                            <button class="btn btn-danger btn-sm" data-bs-toggle="modal" data-bs-target="#addExpenseModal">
                                <i class="bi bi-plus-circle me-1"></i>เพิ่มรายจ่าย
                            </button>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

<!-- Add Modal -->
<div class="modal fade" id="addExpenseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header" style="background: linear-gradient(135deg, #dc2626, #b91c1c); border-radius: 12px 12px 0 0;">
                <h5 class="modal-title text-white"><i class="bi bi-plus-circle me-2"></i>เพิ่มรายจ่าย</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="addExpenseForm">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">วันที่ <span class="text-danger">*</span></label>
                        <input type="date" name="expense_date" class="form-control" value="{{ date('Y-m-d') }}" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">หมวดหมู่ <span class="text-danger">*</span></label>
                            <select name="category" class="form-select" required>
                                <option value="">เลือก</option>
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">จำนวนเงิน <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">฿</span>
                                <input type="number" name="amount" class="form-control" step="0.01" min="0" required placeholder="0.00">
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">รายละเอียด <span class="text-danger">*</span></label>
                        <input type="text" name="description" class="form-control" placeholder="เช่น ค่าไฟประจำเดือน พ.ย. 2567" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">วิธีชำระเงิน</label>
                            <select name="payment_method" class="form-select">
                                <option value="">ไม่ระบุ</option>
                                <option value="cash">เงินสด</option>
                                <option value="transfer">โอนเงิน</option>
                                <option value="credit_card">บัตรเครดิต</option>
                                <option value="cheque">เช็ค</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">เลขที่ใบเสร็จ</label>
                            <input type="text" name="receipt_number" class="form-control" placeholder="ถ้ามี">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">หมายเหตุ</label>
                        <textarea name="notes" class="form-control" rows="2" placeholder="รายละเอียดเพิ่มเติม..."></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-check2 me-1"></i>บันทึก
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Modal -->
<div class="modal fade" id="editExpenseModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content" style="border-radius: 12px; border: none;">
            <div class="modal-header" style="background: linear-gradient(135deg, #0ea5e9, #0284c7); border-radius: 12px 12px 0 0;">
                <h5 class="modal-title text-white"><i class="bi bi-pencil me-2"></i>แก้ไขรายจ่าย</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form id="editExpenseForm">
                <input type="hidden" name="expense_id" id="editExpenseId">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-medium">วันที่ <span class="text-danger">*</span></label>
                        <input type="date" name="expense_date" id="editExpenseDate" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">หมวดหมู่ <span class="text-danger">*</span></label>
                            <select name="category" id="editCategory" class="form-select" required>
                                @foreach($categories as $key => $label)
                                    <option value="{{ $key }}">{{ $label }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">จำนวนเงิน <span class="text-danger">*</span></label>
                            <div class="input-group">
                                <span class="input-group-text">฿</span>
                                <input type="number" name="amount" id="editAmount" class="form-control" step="0.01" min="0" required>
                            </div>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">รายละเอียด <span class="text-danger">*</span></label>
                        <input type="text" name="description" id="editDescription" class="form-control" required>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">วิธีชำระเงิน</label>
                            <select name="payment_method" id="editPaymentMethod" class="form-select">
                                <option value="">ไม่ระบุ</option>
                                <option value="cash">เงินสด</option>
                                <option value="transfer">โอนเงิน</option>
                                <option value="credit_card">บัตรเครดิต</option>
                                <option value="cheque">เช็ค</option>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label fw-medium">เลขที่ใบเสร็จ</label>
                            <input type="text" name="receipt_number" id="editReceiptNumber" class="form-control">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label fw-medium">หมายเหตุ</label>
                        <textarea name="notes" id="editNotes" class="form-control" rows="2"></textarea>
                    </div>
                </div>
                <div class="modal-footer border-0">
                    <button type="button" class="btn btn-light" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check2 me-1"></i>อัปเดต
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
const BASE_URL = '{{ url('/') }}';
const expensesData = @json($expenses->keyBy('id'));

// Add expense
document.getElementById('addExpenseForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const formData = new FormData(this);
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>กำลังบันทึก...';

    fetch(`${BASE_URL}/expenses`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('เกิดข้อผิดพลาด: ' + (data.message || 'ไม่สามารถบันทึกได้'));
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check2 me-1"></i>บันทึก';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('เกิดข้อผิดพลาดในการบันทึก');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check2 me-1"></i>บันทึก';
    });
});

// Edit expense
function editExpense(id) {
    const expense = expensesData[id];
    if (!expense) return;

    document.getElementById('editExpenseId').value = id;
    document.getElementById('editExpenseDate').value = expense.expense_date.split('T')[0];
    document.getElementById('editCategory').value = expense.category;
    document.getElementById('editAmount').value = expense.amount;
    document.getElementById('editDescription').value = expense.description;
    document.getElementById('editPaymentMethod').value = expense.payment_method || '';
    document.getElementById('editReceiptNumber').value = expense.receipt_number || '';
    document.getElementById('editNotes').value = expense.notes || '';

    new bootstrap.Modal(document.getElementById('editExpenseModal')).show();
}

// Update expense
document.getElementById('editExpenseForm').addEventListener('submit', function(e) {
    e.preventDefault();
    const id = document.getElementById('editExpenseId').value;
    const formData = new FormData(this);
    const btn = this.querySelector('button[type="submit"]');
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span>กำลังอัปเดต...';

    fetch(`${BASE_URL}/expenses/${id}`, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
            'X-HTTP-Method-Override': 'PUT'
        },
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('เกิดข้อผิดพลาด: ' + (data.message || 'ไม่สามารถอัปเดตได้'));
            btn.disabled = false;
            btn.innerHTML = '<i class="bi bi-check2 me-1"></i>อัปเดต';
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('เกิดข้อผิดพลาดในการอัปเดต');
        btn.disabled = false;
        btn.innerHTML = '<i class="bi bi-check2 me-1"></i>อัปเดต';
    });
});

// Delete expense
function deleteExpense(id) {
    if (!confirm('ต้องการลบรายจ่ายนี้ใช่หรือไม่?')) return;

    fetch(`${BASE_URL}/expenses/${id}`, {
        method: 'DELETE',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json',
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('เกิดข้อผิดพลาด: ' + (data.message || 'ไม่สามารถลบได้'));
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('เกิดข้อผิดพลาดในการลบ');
    });
}
</script>
@endpush
