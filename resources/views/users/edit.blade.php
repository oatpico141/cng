@extends('layouts.app')

@section('title', 'แก้ไขผู้ใช้ - GCMS')

@push('styles')
<style>
    .page-header {
        background: linear-gradient(135deg, #3b82f6, #2563eb);
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        color: white;
        margin-bottom: 1.5rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h4 class="mb-1"><i class="bi bi-pencil me-2"></i>แก้ไขผู้ใช้</h4>
            <p class="mb-0 opacity-75">{{ $user->name }}</p>
        </div>
        <a href="{{ route('users.index') }}" class="btn btn-light">
            <i class="bi bi-arrow-left me-1"></i>กลับ
        </a>
    </div>

    @if(session('success'))
    <div class="alert alert-success alert-dismissible fade show" role="alert">
        <i class="bi bi-check-circle me-2"></i>{{ session('success') }}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    </div>
    @endif

    <div class="card">
        <div class="card-body">
            <form action="{{ route('users.update', $user->id) }}" method="POST">
                @csrf
                @method('PUT')

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">ชื่อ-นามสกุล <span class="text-danger">*</span></label>
                        <input type="text" name="name" class="form-control @error('name') is-invalid @enderror"
                               value="{{ old('name', $user->name) }}" required>
                        @error('name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Username <span class="text-danger">*</span></label>
                        <input type="text" name="username" class="form-control @error('username') is-invalid @enderror"
                               value="{{ old('username', $user->username) }}" required>
                        @error('username')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email <span class="text-danger">*</span></label>
                        <input type="email" name="email" class="form-control @error('email') is-invalid @enderror"
                               value="{{ old('email', $user->email) }}" required>
                        @error('email')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">รหัสผ่านใหม่</label>
                        <input type="password" name="password" class="form-control @error('password') is-invalid @enderror"
                               placeholder="เว้นว่างหากไม่ต้องการเปลี่ยน">
                        @error('password')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                        <small class="text-muted">เว้นว่างหากไม่ต้องการเปลี่ยนรหัสผ่าน</small>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">บทบาท (Role) <span class="text-danger">*</span></label>
                        <select name="role_id" class="form-select @error('role_id') is-invalid @enderror" required>
                            <option value="">-- เลือกบทบาท --</option>
                            @foreach(\App\Models\Role::all() as $role)
                            <option value="{{ $role->id }}" {{ old('role_id', $user->role_id) == $role->id ? 'selected' : '' }}>
                                {{ $role->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('role_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">สาขา</label>
                        <select name="branch_id" class="form-select @error('branch_id') is-invalid @enderror">
                            <option value="">-- ทุกสาขา --</option>
                            @foreach(\App\Models\Branch::where('is_active', true)->get() as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id', $user->branch_id) == $branch->id ? 'selected' : '' }}>
                                {{ $branch->name }}
                            </option>
                            @endforeach
                        </select>
                        @error('branch_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="col-12 mb-3">
                        <div class="form-check form-switch">
                            <input class="form-check-input" type="checkbox" name="is_active" id="is_active"
                                   value="1" {{ old('is_active', $user->is_active) ? 'checked' : '' }}>
                            <label class="form-check-label" for="is_active">เปิดใช้งาน</label>
                        </div>
                    </div>
                </div>

                <hr>

                <div class="row">
                    <div class="col-12">
                        <h6 class="text-muted mb-3"><i class="bi bi-info-circle me-1"></i>ข้อมูลเพิ่มเติม</h6>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">วันที่สร้าง</label>
                        <input type="text" class="form-control" value="{{ $user->created_at->format('d/m/Y H:i') }}" disabled>
                    </div>
                    <div class="col-md-6 mb-3">
                        <label class="form-label text-muted">อัปเดตล่าสุด</label>
                        <input type="text" class="form-control" value="{{ $user->updated_at->format('d/m/Y H:i') }}" disabled>
                    </div>
                </div>

                <div class="d-flex justify-content-between">
                    <button type="button" class="btn btn-outline-danger" onclick="confirmDelete()">
                        <i class="bi bi-trash me-1"></i>ลบผู้ใช้
                    </button>
                    <div>
                        <a href="{{ route('users.index') }}" class="btn btn-secondary me-2">ยกเลิก</a>
                        <button type="submit" class="btn btn-primary">
                            <i class="bi bi-check-circle me-1"></i>บันทึก
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Delete Form -->
<form id="deleteForm" action="{{ route('users.destroy', $user->id) }}" method="POST" style="display: none;">
    @csrf
    @method('DELETE')
</form>
@endsection

@push('scripts')
<script>
    function confirmDelete() {
        if (confirm('ต้องการลบผู้ใช้นี้หรือไม่? การดำเนินการนี้ไม่สามารถยกเลิกได้')) {
            document.getElementById('deleteForm').submit();
        }
    }
</script>
@endpush
