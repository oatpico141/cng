@extends('layouts.app')

@section('title', 'CRM - GCMS')

@push('styles')
<style>
    .crm-header {
        background: linear-gradient(135deg, #0ea5e9 0%, #0284c7 100%);
        border-radius: 20px;
        padding: 2rem;
        color: white;
        margin-bottom: 2rem;
    }

    .stat-card {
        background: white;
        border-radius: 16px;
        padding: 1.5rem;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        height: 100%;
    }

    .stat-icon {
        width: 48px;
        height: 48px;
        border-radius: 12px;
        display: flex;
        align-items: center;
        justify-content: center;
        font-size: 1.5rem;
    }

    .stat-icon.confirm { background: #dbeafe; color: #2563eb; }
    .stat-icon.followup { background: #d1fae5; color: #059669; }
    .stat-icon.pending { background: #fef3c7; color: #d97706; }
    .stat-icon.done { background: #e0e7ff; color: #4f46e5; }

    .call-section {
        background: white;
        border-radius: 16px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        margin-bottom: 1.5rem;
    }

    .call-section-header {
        padding: 1.25rem 1.5rem;
        border-bottom: 1px solid #e5e7eb;
        display: flex;
        justify-content: between;
        align-items: center;
    }

    .call-table thead th {
        background: #f8fafc;
        font-weight: 600;
        font-size: 0.75rem;
        text-transform: uppercase;
        padding: 1rem;
        border: none;
    }

    .call-table tbody td {
        padding: 1rem;
        vertical-align: middle;
    }

    .call-table tbody tr:hover {
        background: #f8fafc;
    }

    .status-badge {
        padding: 0.35rem 0.75rem;
        border-radius: 20px;
        font-size: 0.75rem;
        font-weight: 600;
    }

    .modal-content {
        border-radius: 16px;
        border: none;
    }

    .nav-pills .nav-link {
        border-radius: 10px;
        padding: 0.75rem 1.5rem;
        font-weight: 500;
    }

    .nav-pills .nav-link.active {
        background: #0284c7;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="crm-header">
        <div class="row align-items-center">
            <div class="col-md-8">
                <h2 class="mb-2"><i class="bi bi-telephone me-2"></i>CRM - ติดตามคนไข้</h2>
                <p class="mb-0 opacity-90">ยืนยันนัดหมายและติดตามอาการหลังรักษา</p>
            </div>
            <div class="col-md-4 text-md-end mt-3 mt-md-0">
                <button class="btn btn-light" onclick="refreshCalls()">
                    <i class="bi bi-arrow-clockwise me-1"></i> รีเฟรช
                </button>
            </div>
        </div>
    </div>

    <!-- Stats -->
    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="stat-icon confirm me-3">
                        <i class="bi bi-telephone-outbound"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold text-primary">{{ $stats['confirmation_pending'] ?? 0 }}</div>
                        <div class="text-muted small">รอยืนยันนัด</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="stat-icon done me-3">
                        <i class="bi bi-check-circle"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold text-success">{{ $stats['confirmation_done'] ?? 0 }}</div>
                        <div class="text-muted small">ยืนยันแล้ว</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="stat-icon followup me-3">
                        <i class="bi bi-heart-pulse"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold text-warning">{{ $stats['followup_pending'] ?? 0 }}</div>
                        <div class="text-muted small">รอติดตามอาการ</div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-3">
            <div class="stat-card">
                <div class="d-flex align-items-center">
                    <div class="stat-icon pending me-3">
                        <i class="bi bi-clipboard-check"></i>
                    </div>
                    <div>
                        <div class="fs-4 fw-bold">{{ $stats['followup_done'] ?? 0 }}</div>
                        <div class="text-muted small">ติดตามแล้ว</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabs -->
    <ul class="nav nav-pills mb-4" id="crmTabs" role="tablist">
        <li class="nav-item" role="presentation">
            <button class="nav-link active" id="confirm-tab" data-bs-toggle="pill" data-bs-target="#confirm" type="button">
                <i class="bi bi-telephone-outbound me-1"></i> ยืนยันนัดหมาย (พรุ่งนี้)
            </button>
        </li>
        <li class="nav-item" role="presentation">
            <button class="nav-link" id="followup-tab" data-bs-toggle="pill" data-bs-target="#followup" type="button">
                <i class="bi bi-heart-pulse me-1"></i> ติดตามอาการ (เมื่อวาน)
            </button>
        </li>
    </ul>

    <div class="tab-content" id="crmTabsContent">
        <!-- Confirmation Calls Tab -->
        <div class="tab-pane fade show active" id="confirm" role="tabpanel">
            <div class="call-section">
                <div class="call-section-header">
                    <h5 class="mb-0"><i class="bi bi-calendar-check me-2"></i>รายชื่อโทรยืนยันนัด</h5>
                    <small class="text-muted ms-auto">สรุป 17:00 น. - นัดหมายวันพรุ่งนี้</small>
                </div>
                <div class="table-responsive">
                    <table class="table call-table mb-0">
                        <thead>
                            <tr>
                                <th>คนไข้</th>
                                <th>เบอร์โทร</th>
                                <th>วันนัด</th>
                                <th>เวลา</th>
                                <th>สาขา</th>
                                <th class="text-center">สถานะ</th>
                                <th class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($confirmationCalls ?? [] as $call)
                            <tr>
                                <td>
                                    <strong>{{ $call->patient->name ?? '-' }}</strong>
                                    <br><small class="text-muted">HN: {{ $call->patient->hn ?? '-' }}</small>
                                </td>
                                <td>{{ $call->patient->phone ?? '-' }}</td>
                                <td>{{ $call->appointment->appointment_date->format('d/m/Y') ?? '-' }}</td>
                                <td>{{ $call->appointment->appointment_time ?? '-' }}</td>
                                <td>{{ $call->branch->name ?? '-' }}</td>
                                <td class="text-center">
                                    @switch($call->status)
                                        @case('pending')
                                            <span class="status-badge bg-warning text-dark">รอโทร</span>
                                            @break
                                        @case('confirmed')
                                            <span class="status-badge bg-success text-white">ยืนยัน</span>
                                            @break
                                        @case('no_answer')
                                            <span class="status-badge bg-secondary text-white">ไม่รับ</span>
                                            @break
                                        @case('cancelled')
                                            <span class="status-badge bg-danger text-white">ยกเลิก</span>
                                            @break
                                        @case('rescheduled')
                                            <span class="status-badge bg-info text-white">เลื่อน</span>
                                            @break
                                        @default
                                            <span class="status-badge bg-light">{{ $call->status }}</span>
                                    @endswitch
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-primary" onclick="openCallModal('{{ $call->id }}', 'confirmation', '{{ $call->patient->name }}')">
                                        <i class="bi bi-telephone"></i> โทร
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center py-5 text-muted">
                                    <i class="bi bi-telephone-x fs-1 d-block mb-2"></i>
                                    ไม่มีรายชื่อที่ต้องโทรยืนยัน
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Follow-up Calls Tab -->
        <div class="tab-pane fade" id="followup" role="tabpanel">
            <div class="call-section">
                <div class="call-section-header">
                    <h5 class="mb-0"><i class="bi bi-heart-pulse me-2"></i>รายชื่อโทรติดตามอาการ</h5>
                    <small class="text-muted ms-auto">คนไข้ที่ทำเมื่อวาน</small>
                </div>
                <div class="table-responsive">
                    <table class="table call-table mb-0">
                        <thead>
                            <tr>
                                <th>คนไข้</th>
                                <th>เบอร์โทร</th>
                                <th>บริการที่ทำ</th>
                                <th>สาขา</th>
                                <th class="text-center">สถานะ</th>
                                <th class="text-center">จัดการ</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($followUpCalls ?? [] as $call)
                            <tr>
                                <td>
                                    <strong>{{ $call->patient->name ?? '-' }}</strong>
                                    <br><small class="text-muted">HN: {{ $call->patient->hn ?? '-' }}</small>
                                </td>
                                <td>{{ $call->patient->phone ?? '-' }}</td>
                                <td>{{ $call->treatment->service->name ?? '-' }}</td>
                                <td>{{ $call->branch->name ?? '-' }}</td>
                                <td class="text-center">
                                    @switch($call->status)
                                        @case('pending')
                                            <span class="status-badge bg-warning text-dark">รอโทร</span>
                                            @break
                                        @case('called')
                                            <span class="status-badge bg-success text-white">โทรแล้ว</span>
                                            @break
                                        @case('no_answer')
                                            <span class="status-badge bg-secondary text-white">ไม่รับ</span>
                                            @break
                                        @default
                                            <span class="status-badge bg-light">{{ $call->status }}</span>
                                    @endswitch
                                </td>
                                <td class="text-center">
                                    <button class="btn btn-sm btn-success" onclick="openCallModal('{{ $call->id }}', 'follow_up', '{{ $call->patient->name }}')">
                                        <i class="bi bi-telephone"></i> โทร
                                    </button>
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="6" class="text-center py-5 text-muted">
                                    <i class="bi bi-telephone-x fs-1 d-block mb-2"></i>
                                    ไม่มีรายชื่อที่ต้องติดตามอาการ
                                </td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Call Modal -->
<div class="modal fade" id="callModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-telephone me-2"></i>บันทึกการโทร</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form id="callForm">
                <input type="hidden" id="callId">
                <input type="hidden" id="callType">
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label fw-bold">คนไข้</label>
                        <div id="patientName" class="form-control-plaintext"></div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">สถานะ</label>
                        <select id="callStatus" class="form-select" required>
                            <option value="">เลือกสถานะ</option>
                            <option value="confirmed">ยืนยัน</option>
                            <option value="called">โทรแล้ว</option>
                            <option value="no_answer">ไม่รับสาย</option>
                            <option value="cancelled">ยกเลิก</option>
                            <option value="rescheduled">เลื่อนนัด</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">บันทึกการโทร</label>
                        <textarea id="callNotes" class="form-control" rows="3" placeholder="บันทึกรายละเอียด..."></textarea>
                    </div>
                    <div class="mb-3" id="feedbackSection">
                        <label class="form-label">อาการคนไข้ (สำหรับติดตามอาการ)</label>
                        <textarea id="patientFeedback" class="form-control" rows="2" placeholder="อาการเป็นอย่างไรบ้าง..."></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                    <button type="submit" class="btn btn-primary">บันทึก</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
let callModal;

document.addEventListener('DOMContentLoaded', function() {
    callModal = new bootstrap.Modal(document.getElementById('callModal'));
});

function openCallModal(id, type, name) {
    document.getElementById('callId').value = id;
    document.getElementById('callType').value = type;
    document.getElementById('patientName').textContent = name;
    document.getElementById('callStatus').value = '';
    document.getElementById('callNotes').value = '';
    document.getElementById('patientFeedback').value = '';

    // Show/hide feedback section based on call type
    document.getElementById('feedbackSection').style.display = type === 'follow_up' ? 'block' : 'none';

    callModal.show();
}

document.getElementById('callForm').addEventListener('submit', function(e) {
    e.preventDefault();

    const callId = document.getElementById('callId').value;
    const data = {
        status: document.getElementById('callStatus').value,
        notes: document.getElementById('callNotes').value,
        patient_feedback: document.getElementById('patientFeedback').value,
    };

    fetch('/crm/call/' + callId, {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        },
        body: JSON.stringify(data)
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            callModal.hide();
            location.reload();
        } else {
            alert('เกิดข้อผิดพลาด: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('เกิดข้อผิดพลาดในการบันทึก');
    });
});

function refreshCalls() {
    fetch('/crm/refresh', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': '{{ csrf_token() }}',
            'Accept': 'application/json'
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            location.reload();
        } else {
            alert('เกิดข้อผิดพลาด: ' + data.message);
        }
    });
}
</script>
@endpush
