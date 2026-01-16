@extends('layouts.app')

@section('title', 'โทรยืนยันนัดหมาย - GCMS')

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #7c3aed, #8b5cf6);
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        color: white;
        margin-bottom: 1.25rem;
    }

    .page-header h2 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .confirmation-card {
        background: #fff;
        border-radius: 8px;
        border: 1px solid #e2e8f0;
        padding: 1rem;
        margin-bottom: 0.75rem;
        transition: all 0.2s;
    }

    .confirmation-card:hover {
        box-shadow: 0 4px 12px rgba(0,0,0,0.1);
    }

    .confirmation-card.status-confirmed {
        border-left: 4px solid #10b981;
    }

    .confirmation-card.status-contacted_cancel {
        border-left: 4px solid #ef4444;
    }

    .confirmation-card.status-contacted_reschedule {
        border-left: 4px solid #f59e0b;
    }

    .confirmation-card.status-no_answer {
        border-left: 4px solid #6b7280;
    }

    .confirmation-card.status-pending {
        border-left: 4px solid #3b82f6;
    }

    .patient-name {
        font-weight: 600;
        font-size: 1rem;
        color: #1e293b;
    }

    .patient-phone {
        font-size: 0.9rem;
        color: #0ea5e9;
    }

    .appointment-info {
        font-size: 0.8rem;
        color: #64748b;
    }

    .status-badge {
        font-size: 0.7rem;
        padding: 0.25rem 0.5rem;
        border-radius: 4px;
    }

    .action-btn {
        padding: 0.35rem 0.6rem;
        font-size: 0.75rem;
        border-radius: 6px;
    }

    .call-attempts {
        font-size: 0.7rem;
        color: #94a3b8;
    }

    .stats-row {
        display: flex;
        gap: 0.5rem;
        margin-bottom: 1rem;
    }

    .stat-item {
        flex: 1;
        background: #fff;
        border-radius: 8px;
        padding: 0.75rem;
        text-align: center;
        border: 1px solid #e2e8f0;
    }

    .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
    }

    .stat-label {
        font-size: 0.7rem;
        color: #64748b;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Header -->
    <div class="page-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h2><i class="bi bi-telephone-outbound me-2"></i>โทรยืนยันนัดหมาย</h2>
                <p class="mb-0">รายการโทรคอนเฟิร์มนัดวันที่ {{ \Carbon\Carbon::parse($date)->locale('th')->isoFormat('D MMM') }} {{ \Carbon\Carbon::parse($date)->year + 543 }}</p>
            </div>
            <div>
                <button class="btn btn-light btn-sm" onclick="generateList()">
                    <i class="bi bi-arrow-repeat me-1"></i>สร้างรายการใหม่
                </button>
            </div>
        </div>
    </div>

    <!-- Date Filter -->
    <div class="mb-3">
        <form method="GET" class="d-flex gap-2">
            <input type="date" name="date" class="form-control form-control-sm" value="{{ $date }}" style="width: auto;">
            <button type="submit" class="btn btn-primary btn-sm">
                <i class="bi bi-search me-1"></i>ค้นหา
            </button>
        </form>
    </div>

    <!-- Stats -->
    <div class="stats-row">
        <div class="stat-item">
            <div class="stat-value text-primary">{{ $confirmations->count() }}</div>
            <div class="stat-label">ทั้งหมด</div>
        </div>
        <div class="stat-item">
            <div class="stat-value text-success">{{ $confirmations->where('confirmation_status', 'confirmed')->count() }}</div>
            <div class="stat-label">ยืนยันแล้ว</div>
        </div>
        <div class="stat-item">
            <div class="stat-value text-warning">{{ $confirmations->where('confirmation_status', 'no_answer')->count() }}</div>
            <div class="stat-label">ไม่รับสาย</div>
        </div>
        <div class="stat-item">
            <div class="stat-value text-danger">{{ $confirmations->where('confirmation_status', 'contacted_cancel')->count() }}</div>
            <div class="stat-label">ยกเลิก</div>
        </div>
        <div class="stat-item">
            <div class="stat-value" style="color: #3b82f6;">{{ $confirmations->where('confirmation_status', 'pending')->count() }}</div>
            <div class="stat-label">รอโทร</div>
        </div>
    </div>

    <!-- Confirmation List -->
    @if($confirmations->count() > 0)
        @foreach($confirmations as $item)
            <div class="confirmation-card status-{{ $item->confirmation_status }}" id="card-{{ $item->id }}">
                <div class="d-flex justify-content-between align-items-start">
                    <div class="flex-grow-1">
                        <div class="d-flex align-items-center gap-2 mb-1">
                            <span class="patient-name">{{ $item->patient->name ?? 'ไม่ระบุ' }}</span>
                            @if($item->confirmation_status === 'confirmed')
                                <span class="badge bg-success status-badge">ยืนยันแล้ว</span>
                            @elseif($item->confirmation_status === 'contacted_cancel')
                                <span class="badge bg-danger status-badge">ยกเลิก</span>
                            @elseif($item->confirmation_status === 'contacted_reschedule')
                                <span class="badge bg-warning status-badge">เลื่อนนัด</span>
                            @elseif($item->confirmation_status === 'no_answer')
                                <span class="badge bg-secondary status-badge">ไม่รับสาย</span>
                            @else
                                <span class="badge bg-primary status-badge">รอโทร</span>
                            @endif
                        </div>
                        <div class="patient-phone mb-1">
                            <i class="bi bi-telephone me-1"></i>{{ $item->patient->phone ?? '-' }}
                        </div>
                        <div class="appointment-info">
                            <i class="bi bi-clock me-1"></i>{{ substr($item->appointment_time, 0, 5) }} น.
                            @if($item->call_attempts > 0)
                                <span class="call-attempts ms-2">
                                    <i class="bi bi-telephone-x"></i> โทรแล้ว {{ $item->call_attempts }} ครั้ง
                                </span>
                            @endif
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="d-flex flex-column gap-1">
                        @if($item->confirmation_status === 'pending' || $item->confirmation_status === 'no_answer')
                            <button class="btn btn-success action-btn" onclick="confirmAttendance('{{ $item->id }}')">
                                <i class="bi bi-check-lg"></i> ยืนยัน
                            </button>
                            <button class="btn btn-warning action-btn" onclick="showRescheduleModal('{{ $item->id }}')">
                                <i class="bi bi-calendar-event"></i> เลื่อน
                            </button>
                            <button class="btn btn-danger action-btn" onclick="requestCancel('{{ $item->id }}')">
                                <i class="bi bi-x-lg"></i> ยกเลิก
                            </button>
                            <button class="btn btn-secondary action-btn" onclick="markNoAnswer('{{ $item->id }}')">
                                <i class="bi bi-telephone-x"></i> ไม่รับ
                            </button>
                        @endif
                    </div>
                </div>
            </div>
        @endforeach
    @else
        <div class="text-center py-5 text-muted">
            <i class="bi bi-telephone-x d-block fs-1 mb-2"></i>
            <p>ไม่มีรายการโทรคอนเฟิร์มสำหรับวันนี้</p>
            <button class="btn btn-primary btn-sm" onclick="generateList()">
                <i class="bi bi-plus me-1"></i>สร้างรายการ
            </button>
        </div>
    @endif
</div>

<!-- Reschedule Modal -->
<div class="modal fade" id="rescheduleModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-calendar-event me-2"></i>เลื่อนนัดหมาย</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="reschedule_id">
                <div class="mb-3">
                    <label class="form-label">วันที่ใหม่</label>
                    <input type="date" class="form-control" id="new_date" min="{{ date('Y-m-d') }}">
                </div>
                <div class="mb-3">
                    <label class="form-label">เวลาใหม่</label>
                    <select class="form-select" id="new_time">
                        <option value="">เลือกเวลา</option>
                        @for($h = 9; $h <= 19; $h++)
                            <option value="{{ sprintf('%02d:00', $h) }}">{{ sprintf('%02d:00', $h) }}</option>
                            @if($h < 19)
                                <option value="{{ sprintf('%02d:30', $h) }}">{{ sprintf('%02d:30', $h) }}</option>
                            @endif
                        @endfor
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">หมายเหตุ</label>
                    <textarea class="form-control" id="reschedule_notes" rows="2"></textarea>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-warning btn-sm" onclick="submitReschedule()">
                    <i class="bi bi-check me-1"></i>บันทึกการเลื่อน
                </button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
const csrfToken = '{{ csrf_token() }}';

// Generate confirmation list
function generateList() {
    if (!confirm('ต้องการสร้างรายการโทรคอนเฟิร์มสำหรับวันพรุ่งนี้หรือไม่?')) return;

    fetch('/confirmation-lists/auto-generate', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            alert(data.message);
            location.reload();
        } else {
            alert('เกิดข้อผิดพลาด: ' + data.message);
        }
    })
    .catch(err => {
        alert('เกิดข้อผิดพลาด');
        console.error(err);
    });
}

// Action A: Confirm attendance
function confirmAttendance(id) {
    if (!confirm('ยืนยันว่าลูกค้าจะมาตามนัดหรือไม่?')) return;

    fetch('/confirmation-lists/' + id + '/confirm', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            updateCardStatus(id, 'confirmed', 'ยืนยันแล้ว', 'success');
            alert(data.message);
        } else {
            alert('เกิดข้อผิดพลาด: ' + data.message);
        }
    })
    .catch(err => {
        alert('เกิดข้อผิดพลาด');
        console.error(err);
    });
}

// Action B: Request cancel
function requestCancel(id) {
    if (!confirm('ยืนยันการยกเลิกนัดหมาย? นัดหมายในปฏิทินจะถูกยกเลิกด้วย')) return;

    fetch('/confirmation-lists/' + id + '/cancel', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            updateCardStatus(id, 'contacted_cancel', 'ยกเลิก', 'danger');
            alert(data.message);
        } else {
            alert('เกิดข้อผิดพลาด: ' + data.message);
        }
    })
    .catch(err => {
        alert('เกิดข้อผิดพลาด');
        console.error(err);
    });
}

// Action C: Show reschedule modal
function showRescheduleModal(id) {
    document.getElementById('reschedule_id').value = id;
    document.getElementById('new_date').value = '';
    document.getElementById('new_time').value = '';
    document.getElementById('reschedule_notes').value = '';

    var modal = new bootstrap.Modal(document.getElementById('rescheduleModal'));
    modal.show();
}

// Submit reschedule
function submitReschedule() {
    var id = document.getElementById('reschedule_id').value;
    var newDate = document.getElementById('new_date').value;
    var newTime = document.getElementById('new_time').value;
    var notes = document.getElementById('reschedule_notes').value;

    if (!newDate || !newTime) {
        alert('กรุณาเลือกวันที่และเวลาใหม่');
        return;
    }

    fetch('/confirmation-lists/' + id + '/reschedule', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': csrfToken
        },
        body: JSON.stringify({
            new_date: newDate,
            new_time: newTime,
            notes: notes
        })
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            updateCardStatus(id, 'contacted_reschedule', 'เลื่อนนัด', 'warning');
            bootstrap.Modal.getInstance(document.getElementById('rescheduleModal')).hide();
            alert(data.message);
        } else {
            alert('เกิดข้อผิดพลาด: ' + data.message);
        }
    })
    .catch(err => {
        alert('เกิดข้อผิดพลาด');
        console.error(err);
    });
}

// Action D: Mark no answer
function markNoAnswer(id) {
    fetch('/confirmation-lists/' + id + '/no-answer', {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': csrfToken
        }
    })
    .then(res => res.json())
    .then(data => {
        if (data.success) {
            updateCardStatus(id, 'no_answer', 'ไม่รับสาย', 'secondary');
            // Update call attempts text
            var card = document.getElementById('card-' + id);
            var attemptsEl = card.querySelector('.call-attempts');
            if (attemptsEl) {
                attemptsEl.innerHTML = '<i class="bi bi-telephone-x"></i> โทรแล้ว ' + data.call_attempts + ' ครั้ง';
            }
        } else {
            alert('เกิดข้อผิดพลาด: ' + data.message);
        }
    })
    .catch(err => {
        alert('เกิดข้อผิดพลาด');
        console.error(err);
    });
}

// Update card status UI
function updateCardStatus(id, status, text, color) {
    var card = document.getElementById('card-' + id);
    if (!card) return;

    // Update card border
    card.className = 'confirmation-card status-' + status;

    // Update badge
    var badge = card.querySelector('.status-badge');
    if (badge) {
        badge.className = 'badge bg-' + color + ' status-badge';
        badge.textContent = text;
    }

    // Hide action buttons if status changed
    if (status === 'confirmed' || status === 'contacted_cancel' || status === 'contacted_reschedule') {
        var buttons = card.querySelectorAll('.action-btn');
        buttons.forEach(btn => btn.style.display = 'none');
    }
}
</script>
@endpush
@endsection
