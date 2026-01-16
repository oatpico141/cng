<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>บิลเงินสด - {{ $appointment->patient->name ?? 'N/A' }}</title>
    <style>
        /* CI Colors - Sky/Ocean/Navy Theme */
        :root {
            --sky-50: #f0f9ff;
            --sky-100: #e0f2fe;
            --sky-200: #bae6fd;
            --sky-300: #7dd3fc;
            --sky-500: #0ea5e9;
            --sky-600: #0284c7;
            --ocean-500: #3b82f6;
            --ocean-600: #2563eb;
            --ocean-700: #1d4ed8;
            --navy-600: #1e3a5f;
            --navy-700: #172554;
            --green-500: #22c55e;
        }

        @page {
            size: A4;
            margin: 12mm;
        }
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Sarabun', 'TH SarabunPSK', 'Segoe UI', sans-serif;
            font-size: 18px;
            padding: 15px;
            background: var(--sky-50);
        }
        .bill-container {
            background: white;
            padding: 30px 35px;
            max-width: 210mm;
            margin: 0 auto;
            min-height: 280mm;
            border: 3px solid var(--ocean-600);
            position: relative;
        }

        /* Top Border Accent */
        .bill-container::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 8px;
            background: linear-gradient(90deg, var(--sky-500), var(--ocean-600), var(--navy-600));
        }

        /* Header */
        .header {
            display: flex;
            justify-content: space-between;
            padding-top: 15px;
            padding-bottom: 20px;
            border-bottom: 2px solid var(--sky-200);
            margin-bottom: 20px;
        }
        .header-left {
            flex: 1;
        }
        .logo-section {
            display: flex;
            align-items: center;
            margin-bottom: 10px;
        }
        .logo {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, var(--sky-500), var(--ocean-600));
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-weight: bold;
            font-size: 18px;
            margin-right: 12px;
        }
        .clinic-name-th {
            font-size: 24px;
            font-weight: bold;
            color: var(--navy-600);
        }
        .clinic-name-en {
            font-size: 16px;
            color: var(--ocean-600);
            font-weight: 500;
        }
        .clinic-info {
            font-size: 14px;
            color: #475569;
            line-height: 1.7;
            margin-top: 5px;
        }
        .header-right {
            text-align: right;
        }
        .doc-number {
            font-size: 15px;
            margin-bottom: 6px;
            color: #475569;
        }
        .doc-number strong {
            color: var(--navy-600);
            font-size: 17px;
        }

        /* Bill Title */
        .bill-title {
            text-align: center;
            margin: 15px 0 20px;
            padding: 15px;
            background: linear-gradient(135deg, var(--sky-50), var(--sky-100));
            border: 2px solid var(--sky-200);
            border-radius: 8px;
        }
        .bill-title h1 {
            font-size: 32px;
            font-weight: bold;
            color: var(--navy-600);
            margin-bottom: 3px;
        }
        .bill-title h2 {
            font-size: 18px;
            font-weight: 500;
            color: var(--ocean-600);
        }

        /* Customer Info */
        .customer-section {
            border: 2px solid var(--sky-200);
            border-radius: 8px;
            padding: 18px;
            margin-bottom: 20px;
            background: var(--sky-50);
        }
        .customer-row {
            display: flex;
            margin-bottom: 10px;
            font-size: 16px;
        }
        .customer-row:last-child {
            margin-bottom: 0;
        }
        .customer-label {
            min-width: 160px;
            font-weight: 600;
            color: var(--navy-600);
        }
        .customer-value {
            flex: 1;
            color: #334155;
        }

        /* Items Table */
        .items-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .items-table th {
            background: linear-gradient(180deg, var(--sky-100), var(--sky-50));
            color: var(--navy-700);
            padding: 14px 12px;
            text-align: center;
            font-size: 15px;
            font-weight: 600;
            border: 1px solid var(--sky-200);
            border-bottom: 2px solid var(--ocean-600);
        }
        .items-table td {
            padding: 14px 12px;
            border: 1px solid var(--sky-200);
            font-size: 16px;
        }
        .items-table .col-no {
            width: 45px;
            text-align: center;
        }
        .items-table .col-qty {
            width: 65px;
            text-align: center;
        }
        .items-table .col-desc {
            text-align: left;
        }
        .items-table .col-price {
            width: 100px;
            text-align: right;
        }
        .items-table .col-amount {
            width: 100px;
            text-align: right;
            font-weight: 600;
            color: var(--navy-600);
        }
        .items-table tbody tr:nth-child(even) {
            background: var(--sky-50);
        }
        .empty-row {
            height: 35px;
        }

        /* Payment & Total Section */
        .footer-section {
            display: flex;
            justify-content: space-between;
            align-items: flex-start;
            margin-top: 20px;
            padding: 15px;
            background: var(--sky-50);
            border: 2px solid var(--sky-200);
            border-radius: 8px;
        }
        .payment-method {
            flex: 1;
        }
        .payment-method label {
            font-weight: 600;
            display: block;
            margin-bottom: 10px;
            color: var(--navy-600);
            font-size: 16px;
        }
        .payment-options {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
        }
        .payment-option {
            display: flex;
            align-items: center;
            gap: 6px;
            font-size: 15px;
        }
        .checkbox {
            width: 16px;
            height: 16px;
            border: 2px solid var(--ocean-600);
            border-radius: 3px;
            display: inline-block;
            position: relative;
        }
        .checkbox.checked {
            background: var(--ocean-600);
        }
        .checkbox.checked::after {
            content: '✓';
            color: white;
            position: absolute;
            top: -3px;
            left: 2px;
            font-size: 11px;
            font-weight: bold;
        }

        .total-section {
            text-align: right;
        }
        .total-label {
            font-size: 15px;
            color: var(--navy-600);
            margin-bottom: 6px;
            font-weight: 500;
        }
        .total-amount {
            font-size: 32px;
            font-weight: bold;
            color: var(--navy-600);
            background: white;
            border: 3px solid var(--ocean-600);
            border-radius: 8px;
            padding: 12px 25px;
            display: inline-block;
            min-width: 180px;
        }

        /* Signature Section */
        .signature-section {
            display: flex;
            justify-content: space-between;
            margin-top: 35px;
            padding-top: 15px;
        }
        .signature-box {
            text-align: center;
            width: 180px;
        }
        .signature-line {
            border-bottom: 2px dotted var(--ocean-600);
            height: 45px;
            margin-bottom: 6px;
        }
        .signature-label {
            font-size: 14px;
            color: var(--navy-600);
            font-weight: 500;
        }
        .signature-date {
            font-size: 13px;
            color: #64748b;
            margin-top: 3px;
        }

        /* Thank you */
        .thank-you {
            text-align: center;
            margin-top: 25px;
            padding-top: 15px;
            border-top: 2px dashed var(--sky-200);
            color: #64748b;
            font-size: 14px;
        }
        .thank-you strong {
            color: var(--ocean-600);
            font-size: 16px;
        }

        /* Print styles */
        @media print {
            body {
                padding: 0;
                background: white;
            }
            .no-print {
                display: none !important;
            }
            .bill-container {
                border: 1px solid var(--ocean-600);
            }
        }

        .print-btn {
            display: block;
            width: 100%;
            max-width: 210mm;
            margin: 0 auto 15px;
            padding: 14px;
            background: linear-gradient(135deg, var(--sky-500), var(--ocean-600));
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            font-weight: 600;
        }
        .print-btn:hover {
            background: linear-gradient(135deg, var(--sky-600), var(--ocean-700));
        }
    </style>
</head>
<body>
    <button class="print-btn no-print" onclick="window.print()">
        🖨️ พิมพ์บิลเงินสด
    </button>

    <div class="bill-container">
        <!-- Header -->
        <div class="header">
            <div class="header-left">
                <div class="logo-section">
                    <div class="logo">GS</div>
                    <div>
                        <div class="clinic-name-th">กายสิริ คลินิกกายภาพบำบัด</div>
                        <div class="clinic-name-en">GUYSIRI CLINIC</div>
                    </div>
                </div>
                <div class="clinic-info">
                    39/4 หมู่ 18 ต.บึงคำพร้อย อ.ลำลูกกา จ.ปทุมธานี 12150<br>
                    โทร: 093-745-4444<br>
                    เลขประจำตัวผู้เสียภาษี: 0135568012821
                </div>
            </div>
            <div class="header-right">
                <div class="doc-number">
                    เลขที่ (Bill No.): <strong>{{ $invoice->invoice_number ?? 'N/A' }}</strong>
                </div>
                <div class="doc-number">
                    วันที่ (Date): <strong>{{ $appointment->appointment_date ? $appointment->appointment_date->format('d/m/Y') : now()->format('d/m/Y') }}</strong>
                </div>
            </div>
        </div>

        <!-- Bill Title -->
        <div class="bill-title">
            <h1>บิลเงินสด</h1>
            <h2>BILL CASH</h2>
        </div>

        <!-- Customer Info -->
        <div class="customer-section">
            <div class="customer-row">
                <span class="customer-label">ชื่อลูกค้า (Name):</span>
                <span class="customer-value">คุณ {{ $appointment->patient->name ?? 'N/A' }}</span>
            </div>
            <div class="customer-row">
                <span class="customer-label">ที่อยู่ (Address):</span>
                <span class="customer-value">{{ $appointment->patient->address ?? '-' }}</span>
            </div>
            <div class="customer-row">
                <span class="customer-label">เลขประจำตัว:</span>
                <span class="customer-value">{{ $appointment->patient->id_card ?? ($appointment->patient->hn_number ?? '-') }}</span>
            </div>
        </div>

        <!-- Items Table -->
        <table class="items-table">
            <thead>
                <tr>
                    <th class="col-no">ลำดับ<br><small>No.</small></th>
                    <th class="col-qty">จำนวน<br><small>Qty</small></th>
                    <th class="col-desc">รายการ<br><small>Description</small></th>
                    <th class="col-price">หน่วยละ<br><small>Unit Price</small></th>
                    <th class="col-amount">จำนวนเงิน<br><small>Amount</small></th>
                </tr>
            </thead>
            <tbody>
                @php $itemNo = 1; @endphp
                @if($invoice && $invoice->items->count() > 0)
                    @foreach($invoice->items as $item)
                    <tr>
                        <td class="col-no">{{ $itemNo++ }}</td>
                        <td class="col-qty">{{ $item->quantity }}</td>
                        <td class="col-desc">{{ $item->description }}</td>
                        <td class="col-price">{{ number_format($item->unit_price, 2) }}</td>
                        <td class="col-amount">{{ number_format($item->total_amount, 2) }}</td>
                    </tr>
                    @endforeach
                @elseif($treatment && $treatment->service)
                    <tr>
                        <td class="col-no">1</td>
                        <td class="col-qty">1</td>
                        <td class="col-desc">{{ $treatment->service->name }}</td>
                        <td class="col-price">{{ number_format($treatment->service->default_price, 2) }}</td>
                        <td class="col-amount">{{ number_format($treatment->service->default_price, 2) }}</td>
                    </tr>
                @else
                    <tr>
                        <td class="col-no">1</td>
                        <td class="col-qty">1</td>
                        <td class="col-desc">บริการทั่วไป</td>
                        <td class="col-price">0.00</td>
                        <td class="col-amount">0.00</td>
                    </tr>
                @endif
                <!-- Empty rows -->
                @for($i = $itemNo; $i <= 5; $i++)
                <tr class="empty-row">
                    <td class="col-no"></td>
                    <td class="col-qty"></td>
                    <td class="col-desc"></td>
                    <td class="col-price"></td>
                    <td class="col-amount"></td>
                </tr>
                @endfor
            </tbody>
        </table>

        <!-- Payment & Total -->
        <div class="footer-section">
            <div class="payment-method">
                <label>การชำระเงิน (Payment):</label>
                <div class="payment-options">
                    <div class="payment-option">
                        <span class="checkbox {{ ($invoice->payment_method ?? '') == 'cash' ? 'checked' : '' }}"></span>
                        <span>เงินสด</span>
                    </div>
                    <div class="payment-option">
                        <span class="checkbox {{ in_array($invoice->payment_method ?? '', ['qr', 'transfer']) ? 'checked' : '' }}"></span>
                        <span>สแกน QR Code</span>
                    </div>
                    <div class="payment-option">
                        <span class="checkbox {{ ($invoice->payment_method ?? '') == 'card' ? 'checked' : '' }}"></span>
                        <span>บัตร</span>
                    </div>
                </div>
            </div>
            <div class="total-section">
                <div class="total-label">รวมจำนวนเงิน (Total Amount)</div>
                <div class="total-amount">{{ number_format($invoice->total ?? 0, 2) }}</div>
            </div>
        </div>

        <!-- Signature -->
        <div class="signature-section">
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">ผู้รับบริการ (Customer)</div>
                <div class="signature-date">วันที่ ....../....../......</div>
            </div>
            <div class="signature-box">
                <div class="signature-line"></div>
                <div class="signature-label">ผู้รับเงิน (Cashier)</div>
                <div class="signature-date">วันที่ {{ now()->format('d/m/Y') }}</div>
            </div>
        </div>

        <!-- Thank you -->
        <div class="thank-you">
            <strong>ขอบคุณที่ใช้บริการ</strong><br>
            Thank you for your visit<br>
            <small>GUYSIRI CLINIC - กายสิริ คลินิกกายภาพบำบัด</small>
        </div>
    </div>
</body>
</html>
