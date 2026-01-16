@extends('layouts.app')

@section('title', 'ระบบเก็บเงิน - GCMS')

@push('styles')
<style>
    /* BILLING PAGE STYLES */

    /* Header */
    .page-header {
        background: linear-gradient(135deg, #7c3aed, #8b5cf6);
        border-radius: 12px;
        padding: 1.25rem 1.5rem;
        color: white;
        margin-bottom: 1.5rem;
    }

    .page-header h2 {
        font-size: 1.25rem;
        font-weight: 600;
        margin-bottom: 0.25rem;
    }

    .page-header p {
        font-size: 0.85rem;
        opacity: 0.9;
        margin: 0;
    }

    /* Stats Cards */
    .stat-card {
        background: #fff;
        border-radius: 10px;
        padding: 1rem;
        border: 1px solid #e2e8f0;
        text-align: center;
    }

    .stat-card .stat-value {
        font-size: 1.5rem;
        font-weight: 700;
        color: #1e293b;
    }

    .stat-card .stat-label {
        font-size: 0.75rem;
        color: #64748b;
    }

    /* Queue Card */
    .billing-card {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 1rem;
        margin-bottom: 0.75rem;
        transition: all 0.2s;
    }

    .billing-card:hover {
        border-color: #8b5cf6;
        box-shadow: 0 2px 8px rgba(139, 92, 246, 0.15);
    }

    .billing-card .patient-name {
        font-size: 1rem;
        font-weight: 600;
        color: #1e293b;
    }

    .billing-card .patient-info {
        font-size: 0.8rem;
        color: #64748b;
    }

    .billing-card .queue-number {
        background: linear-gradient(135deg, #7c3aed, #8b5cf6);
        color: white;
        padding: 0.25rem 0.75rem;
        border-radius: 20px;
        font-size: 0.8rem;
        font-weight: 600;
    }

    .billing-card .service-badge {
        background: #f1f5f9;
        color: #475569;
        padding: 0.2rem 0.5rem;
        border-radius: 4px;
        font-size: 0.7rem;
    }

    /* Payment Modal */
    .payment-summary {
        background: #f8fafc;
        border-radius: 8px;
        padding: 1rem;
    }

    .payment-summary .total-row {
        font-size: 1.25rem;
        font-weight: 700;
        color: #7c3aed;
        border-top: 2px solid #e2e8f0;
        padding-top: 0.75rem;
        margin-top: 0.75rem;
    }

    .payment-method-btn {
        border: 2px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.75rem;
        text-align: center;
        cursor: pointer;
        transition: all 0.2s;
    }

    .payment-method-btn:hover {
        border-color: #8b5cf6;
        background: #faf5ff;
    }

    .payment-method-btn.active {
        border-color: #7c3aed;
        background: #f5f3ff;
    }

    .payment-method-btn i {
        font-size: 1.5rem;
        margin-bottom: 0.25rem;
        display: block;
    }

    /* Course Selection */
    .course-option {
        border: 1px solid #e2e8f0;
        border-radius: 8px;
        padding: 0.75rem;
        margin-bottom: 0.5rem;
        cursor: pointer;
        transition: all 0.2s;
    }

    .course-option:hover {
        border-color: #8b5cf6;
        background: #faf5ff;
    }

    .course-option.selected {
        border-color: #7c3aed;
        background: #f5f3ff;
    }

    /* Quick Purchase Section */
    .quick-purchase-section {
        background: #fff;
        border: 1px solid #e2e8f0;
        border-radius: 10px;
        padding: 1.25rem;
    }

    .quick-purchase-section h5 {
        color: #7c3aed;
        font-weight: 600;
        margin-bottom: 1rem;
    }

    /* Empty State */
    .empty-state {
        text-align: center;
        padding: 3rem 1rem;
        color: #94a3b8;
    }

    .empty-state i {
        font-size: 3rem;
        margin-bottom: 1rem;
    }
</style>
@endpush

@section('content')
<div class="container-fluid">
    <!-- Page Header -->
    <div class="page-header d-flex justify-content-between align-items-center">
        <div>
            <h2><i class="bi bi-cash-coin me-2"></i>ระบบเก็บเงิน</h2>
            <p>จัดการการชำระเงินและออกใบเสร็จ</p>
        </div>
        <div class="text-end">
            <span class="badge bg-white text-dark">
                <i class="bi bi-calendar3 me-1"></i>{{ now()->locale('th')->translatedFormat('j F Y') }}
            </span>
        </div>
    </div>

    <!-- Stats Row -->
    <div class="row g-3 mb-4">
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-value text-warning">{{ $waitingQueues->count() }}</div>
                <div class="stat-label">รอชำระเงิน</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-value text-success">{{ number_format($waitingQueues->sum(fn($q) => $q->appointment->treatments->sum('total_price') ?? 0)) }}</div>
                <div class="stat-label">ยอดรอเก็บ (บาท)</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-value text-primary">{{ $services->count() }}</div>
                <div class="stat-label">บริการ</div>
            </div>
        </div>
        <div class="col-6 col-md-3">
            <div class="stat-card">
                <div class="stat-value text-purple">{{ $packages->count() }}</div>
                <div class="stat-label">แพ็คเกจคอร์ส</div>
            </div>
        </div>
    </div>

    <div class="row">
        <!-- Waiting Queue List -->
        <div class="col-lg-7 mb-4">
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <h5 class="mb-0"><i class="bi bi-hourglass-split me-2"></i>รายการรอชำระเงิน</h5>
                    <span class="badge bg-warning text-dark">{{ $waitingQueues->count() }} รายการ</span>
                </div>
                <div class="card-body">
                    @forelse($waitingQueues as $queue)
                    <div class="billing-card" data-queue-id="{{ $queue->id }}" data-patient-id="{{ $queue->patient_id }}">
                        <div class="d-flex justify-content-between align-items-start mb-2">
                            <div>
                                <span class="queue-number">Q{{ str_pad($queue->queue_number, 3, '0', STR_PAD_LEFT) }}</span>
                            </div>
                            <small class="text-muted">
                                <i class="bi bi-clock me-1"></i>{{ $queue->completed_at ? $queue->completed_at->format('H:i') : '-' }}
                            </small>
                        </div>
                        <div class="patient-name">{{ $queue->patient->name ?? $queue->patient->first_name . ' ' . $queue->patient->last_name }}</div>
                        <div class="patient-info mb-2">
                            <i class="bi bi-telephone me-1"></i>{{ $queue->patient->phone ?? '-' }}
                            @if($queue->pt)
                            <span class="ms-2"><i class="bi bi-person me-1"></i>PT: {{ $queue->pt->name }}</span>
                            @endif
                        </div>

                        @if($queue->appointment && $queue->appointment->purpose)
                        <div class="mb-2">
                            <span class="service-badge">{{ $queue->appointment->purpose }}</span>
                        </div>
                        @endif

                        <div class="d-flex justify-content-between align-items-center mt-3">
                            <div class="text-muted small">
                                @if($queue->appointment && $queue->appointment->treatments->count() > 0)
                                    {{ $queue->appointment->treatments->count() }} รายการรักษา
                                @else
                                    ยังไม่มีรายการ
                                @endif
                            </div>
                            <button class="btn btn-sm btn-primary" onclick="openPaymentModal('{{ $queue->id }}', '{{ $queue->patient_id }}')">
                                <i class="bi bi-cash me-1"></i>ชำระเงิน
                            </button>
                        </div>
                    </div>
                    @empty
                    <div class="empty-state">
                        <i class="bi bi-inbox"></i>
                        <h5>ไม่มีรายการรอชำระเงิน</h5>
                        <p class="mb-0">รายการจะแสดงเมื่อคนไข้รักษาเสร็จ</p>
                    </div>
                    @endforelse
                </div>
            </div>
        </div>

        <!-- Quick Course Purchase -->
        <div class="col-lg-5">
            <div class="quick-purchase-section">
                <h5><i class="bi bi-lightning me-2"></i>ซื้อคอร์สด่วน</h5>

                @if($preloadPatient)
                <div class="alert alert-info mb-3">
                    <i class="bi bi-person-check me-2"></i>
                    <strong>{{ $preloadPatient->name ?? $preloadPatient->first_name . ' ' . $preloadPatient->last_name }}</strong>
                    <br><small>{{ $preloadPatient->phone }}</small>
                </div>
                @endif

                <!-- Patient Search -->
                <div class="mb-3">
                    <label class="form-label">ค้นหาลูกค้า</label>
                    <input type="text" class="form-control" id="patientSearch" placeholder="ชื่อ, เบอร์โทร, HN..." value="{{ $preloadPatient ? ($preloadPatient->name ?? $preloadPatient->first_name) : '' }}">
                    <input type="hidden" id="selectedPatientId" value="{{ $preloadPatient->id ?? '' }}">
                </div>

                <!-- Package Selection -->
                <div class="mb-3">
                    <label class="form-label">เลือกคอร์ส</label>
                    <select class="form-select" id="packageSelect">
                        <option value="">-- เลือกคอร์ส --</option>
                        @foreach($packages as $package)
                        <option value="{{ $package->id }}" data-price="{{ $package->price }}" data-sessions="{{ $package->total_sessions }}">
                            {{ $package->name }} - {{ number_format($package->price) }} บาท ({{ $package->total_sessions }} ครั้ง)
                        </option>
                        @endforeach
                    </select>
                </div>

                <!-- Purchase Pattern -->
                <div class="mb-3">
                    <label class="form-label">รูปแบบการซื้อ</label>
                    <div class="row g-2">
                        <div class="col-4">
                            <div class="course-option selected" data-pattern="buy_and_use">
                                <div class="text-center">
                                    <i class="bi bi-play-circle text-success"></i>
                                    <div class="small">ซื้อ+ใช้เลย</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="course-option" data-pattern="buy_for_later">
                                <div class="text-center">
                                    <i class="bi bi-clock-history text-warning"></i>
                                    <div class="small">ซื้อเก็บไว้</div>
                                </div>
                            </div>
                        </div>
                        <div class="col-4">
                            <div class="course-option" data-pattern="retroactive">
                                <div class="text-center">
                                    <i class="bi bi-arrow-counterclockwise text-info"></i>
                                    <div class="small">ซื้อย้อนหลัง</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="mb-3">
                    <label class="form-label">วิธีชำระเงิน</label>
                    <div class="row g-2">
                        <div class="col-3">
                            <div class="payment-method-btn active" data-method="cash">
                                <i class="bi bi-cash-stack text-success"></i>
                                <small>เงินสด</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="payment-method-btn" data-method="card">
                                <i class="bi bi-credit-card text-primary"></i>
                                <small>บัตร</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="payment-method-btn" data-method="bank_transfer">
                                <i class="bi bi-bank text-info"></i>
                                <small>โอน</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="payment-method-btn" data-method="qr_code">
                                <i class="bi bi-qr-code text-dark"></i>
                                <small>QR</small>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Summary -->
                <div class="payment-summary mb-3">
                    <div class="d-flex justify-content-between mb-2">
                        <span>ราคาคอร์ส</span>
                        <span id="packagePrice">0 บาท</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>ส่วนลด</span>
                        <span class="text-danger">-0 บาท</span>
                    </div>
                    <div class="d-flex justify-content-between total-row">
                        <span>รวมทั้งสิ้น</span>
                        <span id="totalAmount">0 บาท</span>
                    </div>
                </div>

                <button class="btn btn-primary w-100" id="quickPurchaseBtn" disabled>
                    <i class="bi bi-cart-check me-2"></i>ยืนยันการซื้อคอร์ส
                </button>
            </div>

            <!-- Services List -->
            <div class="card mt-4">
                <div class="card-header">
                    <h5 class="mb-0"><i class="bi bi-clipboard-pulse me-2"></i>บริการรายครั้ง</h5>
                </div>
                <div class="card-body" style="max-height: 300px; overflow-y: auto;">
                    @foreach($services as $service)
                    <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                        <div>
                            <div class="fw-medium">{{ $service->name }}</div>
                            <small class="text-muted">{{ $service->category->name ?? 'ทั่วไป' }}</small>
                        </div>
                        <div class="text-end">
                            <div class="fw-bold text-primary">{{ number_format($service->default_price) }} ฿</div>
                        </div>
                    </div>
                    @endforeach
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Payment Modal -->
<div class="modal fade" id="paymentModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header" style="background: linear-gradient(135deg, #7c3aed, #8b5cf6); color: white;">
                <h5 class="modal-title"><i class="bi bi-cash-coin me-2"></i>ชำระเงิน</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <input type="hidden" id="modalQueueId">
                <input type="hidden" id="modalPatientId">

                <!-- Patient Info -->
                <div class="alert alert-light mb-3">
                    <div class="d-flex justify-content-between">
                        <div>
                            <strong id="modalPatientName">-</strong>
                            <div class="small text-muted" id="modalPatientPhone">-</div>
                        </div>
                        <div class="text-end">
                            <span class="badge bg-primary" id="modalQueueNumber">-</span>
                        </div>
                    </div>
                </div>

                <!-- Items List -->
                <div class="mb-3">
                    <label class="form-label fw-bold">รายการ</label>
                    <div id="paymentItems">
                        <!-- Items will be loaded here -->
                    </div>
                </div>

                <!-- Add Item -->
                <div class="mb-3">
                    <div class="row g-2">
                        <div class="col-8">
                            <select class="form-select form-select-sm" id="addItemSelect">
                                <option value="">-- เพิ่มรายการ --</option>
                                <optgroup label="บริการ">
                                    @foreach($services as $service)
                                    <option value="service-{{ $service->id }}" data-type="service" data-price="{{ $service->default_price }}">{{ $service->name }} - {{ number_format($service->default_price) }}฿</option>
                                    @endforeach
                                </optgroup>
                                <optgroup label="คอร์ส">
                                    @foreach($packages as $package)
                                    <option value="course-{{ $package->id }}" data-type="course" data-price="{{ $package->price }}">{{ $package->name }} - {{ number_format($package->price) }}฿</option>
                                    @endforeach
                                </optgroup>
                            </select>
                        </div>
                        <div class="col-4">
                            <button class="btn btn-sm btn-outline-primary w-100" onclick="addPaymentItem()">
                                <i class="bi bi-plus"></i> เพิ่ม
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Payment Summary -->
                <div class="payment-summary">
                    <div class="d-flex justify-content-between mb-2">
                        <span>ยอดรวม</span>
                        <span id="modalSubtotal">0 บาท</span>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>ส่วนลด</span>
                        <input type="number" class="form-control form-control-sm w-25 text-end" id="modalDiscount" value="0" min="0">
                    </div>
                    <div class="d-flex justify-content-between total-row">
                        <span>รวมทั้งสิ้น</span>
                        <span id="modalTotal">0 บาท</span>
                    </div>
                </div>

                <!-- Payment Method -->
                <div class="mt-3">
                    <label class="form-label fw-bold">วิธีชำระเงิน</label>
                    <div class="row g-2">
                        <div class="col-3">
                            <div class="payment-method-btn active" data-method="cash" onclick="selectPaymentMethod(this)">
                                <i class="bi bi-cash-stack text-success"></i>
                                <small>เงินสด</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="payment-method-btn" data-method="card" onclick="selectPaymentMethod(this)">
                                <i class="bi bi-credit-card text-primary"></i>
                                <small>บัตร</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="payment-method-btn" data-method="bank_transfer" onclick="selectPaymentMethod(this)">
                                <i class="bi bi-bank text-info"></i>
                                <small>โอน</small>
                            </div>
                        </div>
                        <div class="col-3">
                            <div class="payment-method-btn" data-method="qr_code" onclick="selectPaymentMethod(this)">
                                <i class="bi bi-qr-code text-dark"></i>
                                <small>QR</small>
                            </div>
                        </div>
                    </div>
                    <input type="hidden" id="selectedPaymentMethod" value="cash">
                </div>

                <!-- Amount Paid -->
                <div class="mt-3">
                    <label class="form-label fw-bold">รับเงิน</label>
                    <input type="number" class="form-control" id="amountPaid" placeholder="จำนวนเงินที่รับ">
                    <div class="d-flex justify-content-between mt-2">
                        <span>เงินทอน</span>
                        <span class="fw-bold text-success" id="changeAmount">0 บาท</span>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">ยกเลิก</button>
                <button type="button" class="btn btn-success" onclick="processPayment()">
                    <i class="bi bi-check-circle me-2"></i>ยืนยันการชำระเงิน
                </button>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    let paymentItems = [];
    let selectedPattern = 'buy_and_use';
    let selectedMethod = 'cash';

    // Course option selection
    document.querySelectorAll('.course-option').forEach(el => {
        el.addEventListener('click', function() {
            document.querySelectorAll('.course-option').forEach(o => o.classList.remove('selected'));
            this.classList.add('selected');
            selectedPattern = this.dataset.pattern;
        });
    });

    // Payment method selection (quick purchase)
    document.querySelectorAll('.quick-purchase-section .payment-method-btn').forEach(el => {
        el.addEventListener('click', function() {
            document.querySelectorAll('.quick-purchase-section .payment-method-btn').forEach(o => o.classList.remove('active'));
            this.classList.add('active');
            selectedMethod = this.dataset.method;
        });
    });

    // Package selection
    document.getElementById('packageSelect').addEventListener('change', function() {
        const option = this.options[this.selectedIndex];
        const price = option.dataset.price || 0;
        document.getElementById('packagePrice').textContent = Number(price).toLocaleString() + ' บาท';
        document.getElementById('totalAmount').textContent = Number(price).toLocaleString() + ' บาท';

        const patientId = document.getElementById('selectedPatientId').value;
        document.getElementById('quickPurchaseBtn').disabled = !(this.value && patientId);
    });

    // Patient search
    let searchTimeout;
    document.getElementById('patientSearch').addEventListener('input', function() {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => {
            // Implement patient search autocomplete
            // For now, just basic functionality
        }, 300);
    });

    // Quick purchase button
    document.getElementById('quickPurchaseBtn').addEventListener('click', function() {
        const patientId = document.getElementById('selectedPatientId').value;
        const packageId = document.getElementById('packageSelect').value;

        if (!patientId || !packageId) {
            alert('กรุณาเลือกลูกค้าและคอร์ส');
            return;
        }

        // Submit quick purchase
        alert('ฟีเจอร์ซื้อคอร์สด่วนกำลังพัฒนา');
    });

    // Open payment modal
    function openPaymentModal(queueId, patientId) {
        document.getElementById('modalQueueId').value = queueId;
        document.getElementById('modalPatientId').value = patientId;

        // Load queue/patient data via AJAX
        // For now, show modal with placeholder data
        const modal = new bootstrap.Modal(document.getElementById('paymentModal'));
        modal.show();

        // Reset items
        paymentItems = [];
        updatePaymentSummary();
    }

    // Select payment method in modal
    function selectPaymentMethod(el) {
        document.querySelectorAll('#paymentModal .payment-method-btn').forEach(o => o.classList.remove('active'));
        el.classList.add('active');
        document.getElementById('selectedPaymentMethod').value = el.dataset.method;
    }

    // Add payment item
    function addPaymentItem() {
        const select = document.getElementById('addItemSelect');
        const option = select.options[select.selectedIndex];

        if (!select.value) return;

        const [type, id] = select.value.split('-');
        const price = parseFloat(option.dataset.price) || 0;
        const name = option.text.split(' - ')[0];

        paymentItems.push({
            id: id,
            type: type,
            name: name,
            price: price,
            quantity: 1,
            total: price
        });

        select.value = '';
        renderPaymentItems();
        updatePaymentSummary();
    }

    // Render payment items
    function renderPaymentItems() {
        const container = document.getElementById('paymentItems');
        container.innerHTML = paymentItems.map((item, index) => `
            <div class="d-flex justify-content-between align-items-center py-2 border-bottom">
                <div>
                    <div class="fw-medium">${item.name}</div>
                    <small class="text-muted">${item.type === 'course' ? 'คอร์ส' : 'บริการ'}</small>
                </div>
                <div class="d-flex align-items-center gap-2">
                    <span class="fw-bold">${item.price.toLocaleString()} ฿</span>
                    <button class="btn btn-sm btn-outline-danger" onclick="removePaymentItem(${index})">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>
        `).join('');
    }

    // Remove payment item
    function removePaymentItem(index) {
        paymentItems.splice(index, 1);
        renderPaymentItems();
        updatePaymentSummary();
    }

    // Update payment summary
    function updatePaymentSummary() {
        const subtotal = paymentItems.reduce((sum, item) => sum + item.total, 0);
        const discount = parseFloat(document.getElementById('modalDiscount').value) || 0;
        const total = subtotal - discount;

        document.getElementById('modalSubtotal').textContent = subtotal.toLocaleString() + ' บาท';
        document.getElementById('modalTotal').textContent = total.toLocaleString() + ' บาท';
    }

    // Discount change
    document.getElementById('modalDiscount')?.addEventListener('input', updatePaymentSummary);

    // Amount paid change - calculate change
    document.getElementById('amountPaid')?.addEventListener('input', function() {
        const total = paymentItems.reduce((sum, item) => sum + item.total, 0) - (parseFloat(document.getElementById('modalDiscount').value) || 0);
        const paid = parseFloat(this.value) || 0;
        const change = Math.max(0, paid - total);
        document.getElementById('changeAmount').textContent = change.toLocaleString() + ' บาท';
    });

    // Process payment
    function processPayment() {
        const queueId = document.getElementById('modalQueueId').value;
        const patientId = document.getElementById('modalPatientId').value;
        const subtotal = paymentItems.reduce((sum, item) => sum + item.total, 0);
        const discount = parseFloat(document.getElementById('modalDiscount').value) || 0;
        const total = subtotal - discount;
        const paymentMethod = document.getElementById('selectedPaymentMethod').value;
        const amountPaid = parseFloat(document.getElementById('amountPaid').value) || total;

        if (paymentItems.length === 0) {
            alert('กรุณาเพิ่มรายการ');
            return;
        }

        if (amountPaid < total) {
            if (!confirm('จำนวนเงินที่รับน้อยกว่ายอดรวม ต้องการบันทึกเป็นชำระบางส่วนหรือไม่?')) {
                return;
            }
        }

        // Submit payment
        fetch('/billing/process-payment', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
            },
            body: JSON.stringify({
                queue_id: queueId,
                patient_id: patientId,
                items: paymentItems,
                subtotal: subtotal,
                discount: discount,
                total: total,
                payment_method: paymentMethod,
                amount_paid: amountPaid
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('บันทึกการชำระเงินเรียบร้อย\nเลขที่ใบเสร็จ: ' + data.invoice_number);
                location.reload();
            } else {
                alert('เกิดข้อผิดพลาด: ' + data.message);
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('เกิดข้อผิดพลาดในการเชื่อมต่อ');
        });
    }
</script>
@endpush
