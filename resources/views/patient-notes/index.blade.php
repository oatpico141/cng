@extends('layouts.app')

@section('title', 'Patient Notes - All Notes')

@section('content')
<div class="container-fluid py-4">
    <div class="row">
        <div class="col-12">
            <!-- Page Header -->
            <div class="d-flex justify-content-between align-items-center mb-4">
                <div>
                    <h2 class="fw-bold mb-1"><i class="bi bi-journal-text text-primary me-2"></i>Patient Notes</h2>
                    <p class="text-muted mb-0">ระบบจัดการบันทึกโน้ตคนไข้</p>
                </div>
            </div>

            <!-- Search and Filter Card -->
            <div class="card mb-4 shadow-sm">
                <div class="card-body">
                    <form method="GET" action="{{ route('patient-notes.index') }}" class="row g-3">
                        <div class="col-md-4">
                            <label class="form-label fw-bold">ค้นหาคนไข้</label>
                            <input type="text" class="form-control" name="search"
                                   placeholder="ชื่อ, เบอร์โทร, HN"
                                   value="{{ request('search') }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">ประเภทโน้ต</label>
                            <select class="form-select" name="note_type">
                                <option value="">ทั้งหมด</option>
                                <option value="general" {{ request('note_type') == 'general' ? 'selected' : '' }}>ทั่วไป</option>
                                <option value="medical" {{ request('note_type') == 'medical' ? 'selected' : '' }}>ทางการแพทย์</option>
                                <option value="billing" {{ request('note_type') == 'billing' ? 'selected' : '' }}>การเงิน</option>
                                <option value="complaint" {{ request('note_type') == 'complaint' ? 'selected' : '' }}>ข้อร้องเรียน</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">สถานะ</label>
                            <select class="form-select" name="is_important">
                                <option value="">ทั้งหมด</option>
                                <option value="1" {{ request('is_important') == '1' ? 'selected' : '' }}>สำคัญ</option>
                                <option value="0" {{ request('is_important') == '0' ? 'selected' : '' }}>ธรรมดา</option>
                            </select>
                        </div>
                        <div class="col-md-2 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="bi bi-search me-1"></i>ค้นหา
                            </button>
                        </div>
                    </form>
                </div>
            </div>

            <!-- Notes List Card -->
            <div class="card shadow-sm">
                <div class="card-body">
                    @if(session('success'))
                        <div class="alert alert-success alert-dismissible fade show">
                            <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    @if(session('error'))
                        <div class="alert alert-danger alert-dismissible fade show">
                            <i class="bi bi-exclamation-triangle me-2"></i>{{ session('error') }}
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    @endif

                    <div class="table-responsive">
                        <table class="table table-hover align-middle">
                            <thead class="table-light">
                                <tr>
                                    <th style="width: 5%">
                                        <i class="bi bi-exclamation-circle text-warning"></i>
                                    </th>
                                    <th style="width: 15%">คนไข้</th>
                                    <th style="width: 10%">ประเภท</th>
                                    <th style="width: 35%">โน้ต</th>
                                    <th style="width: 15%">ผู้บันทึก</th>
                                    <th style="width: 12%">วันที่</th>
                                    <th style="width: 8%" class="text-center">จัดการ</th>
                                </tr>
                            </thead>
                            <tbody>
                                @forelse($notes as $note)
                                <tr class="{{ $note->is_important ? 'table-warning' : '' }}">
                                    <td class="text-center">
                                        @if($note->is_important)
                                            <i class="bi bi-star-fill text-warning" title="สำคัญ"></i>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('patients.show', $note->patient_id) }}"
                                           class="text-decoration-none fw-bold">
                                            {{ $note->patient->name ?? 'N/A' }}
                                        </a>
                                        <br>
                                        <small class="text-muted">
                                            HN: {{ $note->patient->hn ?? 'N/A' }}
                                        </small>
                                    </td>
                                    <td>
                                        @if($note->note_type == 'general')
                                            <span class="badge bg-secondary">ทั่วไป</span>
                                        @elseif($note->note_type == 'medical')
                                            <span class="badge bg-danger">ทางการแพทย์</span>
                                        @elseif($note->note_type == 'billing')
                                            <span class="badge bg-success">การเงิน</span>
                                        @elseif($note->note_type == 'complaint')
                                            <span class="badge bg-warning text-dark">ข้อร้องเรียน</span>
                                        @else
                                            <span class="badge bg-secondary">{{ $note->note_type }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="text-truncate" style="max-width: 400px;" title="{{ $note->note }}">
                                            {{ $note->note }}
                                        </div>
                                    </td>
                                    <td>
                                        <small>
                                            {{ $note->createdBy->name ?? 'System' }}
                                        </small>
                                    </td>
                                    <td>
                                        <small class="text-muted">
                                            {{ $note->created_at->locale('th')->isoFormat('D MMM YYYY') }}
                                            <br>
                                            {{ $note->created_at->format('H:i') }} น.
                                        </small>
                                    </td>
                                    <td class="text-center">
                                        <div class="btn-group btn-group-sm">
                                            <button type="button"
                                                    class="btn btn-outline-info"
                                                    data-bs-toggle="modal"
                                                    data-bs-target="#viewNoteModal"
                                                    data-note-id="{{ $note->id }}"
                                                    data-note="{{ $note->note }}"
                                                    data-patient="{{ $note->patient->name ?? 'N/A' }}"
                                                    data-type="{{ $note->note_type }}"
                                                    data-important="{{ $note->is_important }}"
                                                    title="ดูรายละเอียด">
                                                <i class="bi bi-eye"></i>
                                            </button>
                                            <form action="{{ route('patient-notes.destroy', $note->id) }}"
                                                  method="POST"
                                                  class="d-inline"
                                                  onsubmit="return confirm('ต้องการลบโน้ตนี้?')">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="btn btn-outline-danger btn-sm" title="ลบ">
                                                    <i class="bi bi-trash"></i>
                                                </button>
                                            </form>
                                        </div>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="7" class="text-center py-5 text-muted">
                                        <i class="bi bi-inbox fs-1 d-block mb-3"></i>
                                        <p class="mb-0">ไม่พบข้อมูลโน้ต</p>
                                        <small>ลองค้นหาด้วยคำค้นอื่น หรือกลับไปที่หน้า Patient Profile เพื่อเพิ่มโน้ต</small>
                                    </td>
                                </tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    @if($notes->hasPages())
                        <div class="d-flex justify-content-center mt-4">
                            {{ $notes->links() }}
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<!-- View Note Modal -->
<div class="modal fade" id="viewNoteModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title"><i class="bi bi-journal-text me-2"></i>รายละเอียดโน้ต</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted">คนไข้</label>
                    <p id="modalPatientName" class="fw-bold"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted">ประเภท</label>
                    <p id="modalNoteType"></p>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold text-muted">โน้ต</label>
                    <div class="p-3 bg-light rounded">
                        <p id="modalNoteContent" class="mb-0"></p>
                    </div>
                </div>
                <div class="mb-0">
                    <label class="form-label fw-bold text-muted">สถานะ</label>
                    <p id="modalImportant"></p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ปิด</button>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
// View Note Modal
const viewNoteModal = document.getElementById('viewNoteModal');
if (viewNoteModal) {
    viewNoteModal.addEventListener('show.bs.modal', function (event) {
        const button = event.relatedTarget;

        document.getElementById('modalPatientName').textContent = button.getAttribute('data-patient');
        document.getElementById('modalNoteContent').textContent = button.getAttribute('data-note');

        // Note Type
        const noteType = button.getAttribute('data-type');
        let typeHtml = '';
        switch(noteType) {
            case 'general':
                typeHtml = '<span class="badge bg-secondary">ทั่วไป</span>';
                break;
            case 'medical':
                typeHtml = '<span class="badge bg-danger">ทางการแพทย์</span>';
                break;
            case 'billing':
                typeHtml = '<span class="badge bg-success">การเงิน</span>';
                break;
            case 'complaint':
                typeHtml = '<span class="badge bg-warning text-dark">ข้อร้องเรียน</span>';
                break;
            default:
                typeHtml = '<span class="badge bg-secondary">' + noteType + '</span>';
        }
        document.getElementById('modalNoteType').innerHTML = typeHtml;

        // Important Status
        const isImportant = button.getAttribute('data-important') === '1';
        document.getElementById('modalImportant').innerHTML = isImportant
            ? '<span class="badge bg-warning text-dark"><i class="bi bi-star-fill me-1"></i>สำคัญ</span>'
            : '<span class="badge bg-secondary">ธรรมดา</span>';
    });
}
</script>
@endpush
@endsection
