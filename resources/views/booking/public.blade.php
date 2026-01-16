<!DOCTYPE html>
<html lang="th">
<head>
<meta charset="UTF-8" />
<meta name="viewport" content="width=device-width, initial-scale=1.0"/>
<meta name="csrf-token" content="{{ csrf_token() }}">
<title>ระบบจองคิว - กายสิริ คลินิกกายภาพบำบัด</title>
<style>
    *{margin:0;padding:0;box-sizing:border-box}
    body{
        font-family:'SF Pro Display','Inter',-apple-system,BlinkMacSystemFont,sans-serif;
        background:linear-gradient(135deg,#f8fafc 0%,#e2e8f0 100%);
        min-height:100vh;display:flex;align-items:center;justify-content:center;padding:24px
    }
    .main-container{
        background:#fff;border-radius:20px;
        box-shadow:0 4px 6px -1px rgba(0,0,0,.1),0 2px 4px -1px rgba(0,0,0,.06),0 0 0 1px rgba(59,130,246,.1);
        max-width:480px;width:100%;border:2px solid #3b82f6;overflow:hidden
    }
    .header-section{
        background:linear-gradient(135deg,#3b82f6 0%,#1e40af 100%);color:#fff;padding:32px 40px;text-align:center;position:relative
    }
    .header-section::before{
        content:"";position:absolute;inset:0;opacity:.3;
        background:url('data:image/svg+xml,<svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 100 100"><defs><pattern id="g" width="10" height="10" patternUnits="userSpaceOnUse"><path d="M10 0L0 0 0 10" fill="none" stroke="rgba(255,255,255,0.05)" stroke-width="1"/></pattern></defs><rect width="100" height="100" fill="url(%23g)"/></svg>')
    }
    .header-content{position:relative;z-index:1}
    .clinic-logo{width:48px;height:48px;background:rgba(255,255,255,.15);border-radius:12px;display:flex;align-items:center;justify-content:center;margin:0 auto 16px;font-size:24px}
    .header-title{font-size:22px;font-weight:700;margin-bottom:8px;letter-spacing:-.025em}
    .header-subtitle{font-size:14px;opacity:.9}
    .form-container{padding:40px}
    .form-group{margin-bottom:24px;position:relative}
    .form-label{display:block;margin-bottom:8px;color:#1f2937;font-weight:600;font-size:13px;letter-spacing:-.01em;text-transform:uppercase}
    .required-asterisk{color:#ef4444}
    .form-input,.form-select,.form-textarea{
        width:100%;padding:16px 20px;border:1.5px solid #d1d5db;border-radius:12px;font-size:15px;background:#fafbfc;color:#1f2937;font-weight:500;transition:.2s cubic-bezier(.4,0,.2,1)
    }
    .form-input:focus,.form-select:focus,.form-textarea:focus{
        outline:none;border-color:#3b82f6;background:#fff;box-shadow:0 0 0 3px rgba(59,130,246,.08),0 1px 2px 0 rgba(0,0,0,.05);transform:translateY(-1px)
    }
    .form-input::placeholder{color:#9ca3af;font-weight:400}
    .form-row{display:flex;gap:16px}
    .form-col{flex:1}
    .input-helper{font-size:12px;color:#64748b;margin-top:6px;display:flex;align-items:center;gap:6px}
    .input-helper.error{color:#ef4444}.input-helper.success{color:#10b981}
    .form-input.error{border-color:#ef4444;box-shadow:0 0 0 3px rgba(239,68,68,.08)}
    .form-input.success{border-color:#10b981;box-shadow:0 0 0 3px rgba(16,185,129,.08)}
    .btn{width:100%;padding:16px 24px;border:none;border-radius:12px;font-size:15px;font-weight:600;cursor:pointer;transition:.2s cubic-bezier(.4,0,.2,1);letter-spacing:-.01em;margin:12px 0;position:relative;overflow:hidden}
    .btn-primary{background:linear-gradient(135deg,#3b82f6 0%,#1e40af 100%);color:#fff;box-shadow:0 4px 14px 0 rgba(59,130,246,.25)}
    .btn-primary:hover{transform:translateY(-2px);box-shadow:0 8px 25px 0 rgba(59,130,246,.35)}
    .btn-primary:disabled{opacity:.6;cursor:not-allowed;transform:none}
    .btn-secondary{background:linear-gradient(135deg,#6b7280 0%,#4b5563 100%);color:#fff;box-shadow:0 4px 14px 0 rgba(107,114,128,.25)}
    /* Modal */
    .modal{display:none;position:fixed;z-index:1000;inset:0;background:rgba(15,23,42,.6);backdrop-filter:blur(12px);animation:modalFadeIn .4s cubic-bezier(.16,1,.3,1)}
    @keyframes modalFadeIn{from{opacity:0}to{opacity:1}}
    .modal-content{
        background:#fff;margin:3% auto;padding:0;border-radius:24px;width:90%;max-width:560px;
        box-shadow:0 25px 50px -12px rgba(0,0,0,.15),0 0 0 1px rgba(255,255,255,.05);
        animation:modalSlideUp .4s cubic-bezier(.16,1,.3,1);overflow:hidden
    }
    @keyframes modalSlideUp{from{transform:translateY(40px) scale(.96);opacity:0}to{transform:translateY(0) scale(1);opacity:1}}
    .modal-header{background:linear-gradient(135deg,#10b981 0%,#059669 100%);color:#fff;padding:28px 32px;text-align:center;position:relative}
    .modal-title{margin:0;font-size:20px;font-weight:700;letter-spacing:-.025em;position:relative;z-index:1}
    .modal-body{padding:32px}
    .message-display{
        background:linear-gradient(135deg,#1a202c 0%,#2d3748 100%);color:#e2e8f0;padding:24px;border-radius:16px;
        font-size:14px;line-height:1.6;white-space:pre-line;text-align:left;font-family:'SF Pro Text',-apple-system,BlinkMacSystemFont,sans-serif;
        margin-bottom:28px;max-height:350px;overflow-y:auto;border:1px solid #4a5568;position:relative;box-shadow:inset 0 2px 8px rgba(0,0,0,.1)
    }
    .modal-actions{padding:0 32px 32px;display:flex;gap:16px;justify-content:center;flex-wrap:wrap}
    .modal-btn{padding:16px 28px;border:none;border-radius:12px;font-size:15px;font-weight:600;cursor:pointer;transition:.3s;display:flex;align-items:center;gap:8px;min-width:160px;justify-content:center}
    .modal-btn-primary{background:linear-gradient(135deg,#3b82f6 0%,#1e40af 100%);color:#fff;box-shadow:0 8px 20px rgba(59,130,246,.25)}
    .modal-btn-secondary{background:linear-gradient(135deg,#6b7280 0%,#4b5563 100%);color:#fff;box-shadow:0 8px 20px rgba(107,114,128,.25)}
    .success-indicator{display:none;background:linear-gradient(135deg,#48bb78 0%,#38a169 100%);color:#fff;padding:12px 24px;border-radius:12px;text-align:center;margin:16px 32px 28px;font-size:14px;font-weight:600;animation:successSlide .4s cubic-bezier(.16,1,.3,1);box-shadow:0 4px 14px rgba(72,187,120,.3)}
    @keyframes successSlide{from{transform:translateY(-15px) scale(.95);opacity:0}to{transform:translateY(0) scale(1);opacity:1}}
    .alert{padding:16px 20px;border-radius:12px;margin-bottom:20px;font-size:14px}
    .alert-error{background:#fef2f2;border:1px solid #fecaca;color:#dc2626}
    .alert-success{background:#f0fdf4;border:1px solid #bbf7d0;color:#16a34a}
    @media(max-width:640px){
        .main-container{margin:16px}
        .form-container{padding:32px 24px}
        .form-row{flex-direction:column}
        .modal-content{width:95%;margin:8% auto;border-radius:20px}
        .modal-body{padding:24px}
        .modal-actions{padding:0 24px 24px;flex-direction:column}
        .modal-btn{min-width:auto;width:100%}
    }
</style>
</head>
<body>
<div class="main-container">
    <div class="header-section">
        <div class="header-content">
            <div class="clinic-logo">📋</div>
            <div class="header-title">ระบบจองคิวออนไลน์</div>
            <div class="header-subtitle">กายสิริ คลินิกกายภาพบำบัด</div>
        </div>
    </div>

    <div class="form-container">
        <div id="alertBox"></div>

        <form id="appointmentForm" novalidate>
            <div class="form-group">
                <label class="form-label" for="fullName">ชื่อ-นามสกุล <span class="required-asterisk">*</span></label>
                <input type="text" id="fullName" name="fullName" class="form-input" required placeholder="กรอกชื่อ-นามสกุลของท่าน" autocomplete="off">
            </div>

            <div class="form-group">
                <label class="form-label" for="phoneNumber">หมายเลขโทรศัพท์ <span class="required-asterisk">*</span></label>
                <input type="tel" id="phoneNumber" name="phoneNumber" class="form-input" required placeholder="0812345678 (10 หลัก)" autocomplete="off" maxlength="10" pattern="[0-9]{10}">
                <div class="input-helper" id="phoneHelper">กรุณาใส่เบอร์โทรศัพท์ 10 หลักเท่านั้น</div>
            </div>

            <div class="form-row">
                <div class="form-col">
                    <div class="form-group">
                        <label class="form-label" for="appointmentDate">วันที่นัดหมาย <span class="required-asterisk">*</span></label>
                        <input type="date" id="appointmentDate" name="appointmentDate" class="form-input" required autocomplete="off" min="{{ date('Y-m-d') }}" value="{{ date('Y-m-d') }}">
                    </div>
                </div>
                <div class="form-col">
                    <div class="form-group">
                        <label class="form-label" for="appointmentTime">เวลานัดหมาย <span class="required-asterisk">*</span></label>
                        <select id="appointmentTime" name="appointmentTime" class="form-select" required autocomplete="off">
                            <option value="">เลือกเวลา</option>
                            <option>09:00</option><option>09:30</option><option>10:00</option><option>10:30</option>
                            <option>11:00</option><option>11:30</option><option>12:00</option><option>12:30</option>
                            <option>13:00</option><option>13:30</option><option>14:00</option><option>14:30</option>
                            <option>15:00</option><option>15:30</option><option>16:00</option><option>16:30</option>
                            <option>17:00</option><option>17:30</option><option>18:00</option><option>18:30</option>
                            <option>19:00</option><option>19:30</option><option>20:00</option>
                        </select>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="form-label" for="leadSource">ช่องทางที่ติดต่อมา <span class="required-asterisk">*</span></label>
                <select id="leadSource" name="leadSource" class="form-select" required autocomplete="off">
                    <option value="">เลือกช่องทาง</option>
                    <option value="Ads">Ads (โฆษณา)</option>
                    <option value="Facebook">Facebook</option>
                    <option value="Call">โทรศัพท์</option>
                    <option value="Line">Line</option>
                    <option value="Co">บริษัทคู่สัญญา</option>
                    <option value="Walk-in">Walk-in</option>
                    <option value="Referral">แนะนำจากคนรู้จัก</option>
                </select>
            </div>

            <div class="form-group">
                <label class="form-label" for="symptoms">อาการหรือปัญหาที่ต้องการรักษา</label>
                <select id="symptoms" name="symptoms" class="form-select" autocomplete="off">
                    <option value="">เลือกอาการ (ไม่บังคับ)</option>
                    <option value="ปวดคอ บ่า ไหล่">ปวดคอ บ่า ไหล่</option>
                    <option value="ปวดหลัง">ปวดหลัง</option>
                    <option value="ปวดเข่า">ปวดเข่า</option>
                    <option value="สลักเพรชจม">สลักเพรชจม</option>
                    <option value="ปวดขา">ปวดขา</option>
                    <option value="ปวดแขน">ปวดแขน</option>
                    <option value="รองช้ำ">รองช้ำ</option>
                    <option value="อื่นๆ">อื่นๆ (ระบุเพิ่มเติม)</option>
                </select>
            </div>

            <div class="form-group" id="customSymptomsGroup" style="display:none;">
                <label class="form-label" for="customSymptoms">ระบุอาการเพิ่มเติม</label>
                <textarea id="customSymptoms" name="customSymptoms" class="form-textarea" rows="3" placeholder="อธิบายอาการหรือปัญหาที่ต้องการรักษา" autocomplete="off"></textarea>
            </div>

            <div class="form-group">
                <label class="form-label" for="serviceType">ประเภทบริการ <span class="required-asterisk">*</span></label>
                <select id="serviceType" name="serviceType" class="form-select" required autocomplete="off">
                    <option value="">เลือกประเภทบริการ</option>
                    <option value="กายภาพบำบัด">กายภาพบำบัด</option>
                    <option value="ปรึกษาและประเมินอาการ">ปรึกษาและประเมินอาการ</option>
                    <option value="นัดพบคุณหมอ">นัดพบคุณหมอ</option>
                    <option value="ตรวจประเมินเข่า">ตรวจประเมินเข่า</option>
                </select>
            </div>

            <button type="submit" class="btn btn-primary" id="submitBtn">ยืนยันการจองคิว</button>
            <button type="button" class="btn btn-secondary" id="clearBtn">เคลียร์ข้อมูลทั้งหมด</button>
        </form>
    </div>
</div>

<!-- Modal -->
<div id="confirmationModal" class="modal" aria-hidden="true">
    <div class="modal-content" role="dialog" aria-modal="true" aria-labelledby="modalTitle">
        <div class="modal-header">
            <h3 id="modalTitle" class="modal-title">✅ จองคิวสำเร็จ</h3>
        </div>
        <div class="modal-body">
            <p style="text-align:center;margin-bottom:20px;color:#374151;">ข้อความด้านล่างนี้สามารถคัดลอกส่งให้ลูกค้าได้เลย</p>
            <div class="message-display" id="confirmationMessage"></div>
        </div>
        <div class="modal-actions">
            <button type="button" class="modal-btn modal-btn-primary" id="copyBtn">📋 คัดลอกข้อความ</button>
            <button type="button" class="modal-btn modal-btn-secondary" id="closeBtn">✕ ปิดหน้าต่าง</button>
        </div>
        <div class="success-indicator" id="copySuccess">✅ คัดลอกข้อความสำเร็จแล้ว</div>
    </div>
</div>

<script>
(function(){
    // Config
    const CLINIC_PHONE = '093-745-4444';
    const CLINIC_MAP = 'https://cutt.ly/Guysiri-map';

    const $ = (id) => document.getElementById(id);

    // ตั้งค่าวันที่เริ่มต้นเป็นวันนี้
    const dateEl = $('appointmentDate');
    if (dateEl && !dateEl.value) {
        const today = new Date();
        dateEl.value = today.toISOString().split('T')[0];
        dateEl.min = today.toISOString().split('T')[0]; // ไม่ให้เลือกวันที่ผ่านมา
    }

    // Validate เบอร์โทรแบบ real-time
    const phoneInput = $('phoneNumber');
    const phoneHelper = $('phoneHelper');
    phoneInput.addEventListener('input', (e) => {
        let v = e.target.value.replace(/\D/g, '');
        if (v.length > 10) v = v.slice(0, 10);
        e.target.value = v;

        if (!v.length) {
            phoneHelper.textContent = 'กรุณาใส่เบอร์โทรศัพท์ 10 หลักเท่านั้น';
            phoneHelper.className = 'input-helper';
            phoneInput.className = 'form-input';
        } else if (v.length < 10) {
            phoneHelper.textContent = `ใส่เบอร์อีก ${10 - v.length} หลัก`;
            phoneHelper.className = 'input-helper error';
            phoneInput.className = 'form-input error';
        } else {
            phoneHelper.textContent = '✓ เบอร์โทรศัพท์ถูกต้อง';
            phoneHelper.className = 'input-helper success';
            phoneInput.className = 'form-input success';
        }
    });

    // Toggle ช่องอาการเพิ่มเติม
    $('symptoms').addEventListener('change', () => {
        const on = $('symptoms').value === 'อื่นๆ';
        $('customSymptomsGroup').style.display = on ? 'block' : 'none';
        if (!on) $('customSymptoms').value = '';
    });

    // สร้างข้อความยืนยัน
    function buildConfirmationMessage(data) {
        const dt = new Date(data.date + "T00:00:00");
        const thaiDays = ['อาทิตย์', 'จันทร์', 'อังคาร', 'พุธ', 'พฤหัสบดี', 'ศุกร์', 'เสาร์'];
        const thaiMonths = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน',
                           'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];

        const dayName = thaiDays[dt.getDay()];
        const day = dt.getDate();
        const month = thaiMonths[dt.getMonth()];
        const year = dt.getFullYear() + 543;
        const time = data.time.replace(':', '.');

        const symptomText = data.symptoms === 'อื่นๆ' && data.customSymptoms
            ? data.customSymptoms
            : (data.symptoms && data.symptoms !== 'อื่นๆ') ? data.symptoms : '';

        const nameForMsg = data.fullName.startsWith('คุณ') ? data.fullName : `คุณ${data.fullName}`;

        let msg = `✅ ยืนยันการจองเรียบร้อยแล้วค่ะ
👤 ชื่อ-นามสกุล: ${nameForMsg}
📞 เบอร์โทร: ${data.phone}
📅 วัน-เวลาเข้ารับบริการ: วัน${dayName}ที่ ${day} ${month} ${year} เวลา ${time} น.
🏥 บริการที่จอง: ${data.service}`;

        if (symptomText) {
            msg += `\n🩺 อาการที่เข้ามารักษา: ${symptomText}`;
        }

        msg += `
📞 เบอร์คลินิก: ${CLINIC_PHONE}
📍 แผนที่คลินิก: ${CLINIC_MAP}

ขอบคุณคุณลูกค้าที่ไว้วางใจเลือกใช้บริการกับทางคลินิก 🙏
เพื่อความสะดวกในการเข้ารับบริการ
หากต้องการเปลี่ยนแปลงเวลานัดหรือติดธุระด่วน
สามารถแจ้งล่วงหน้าได้เลยนะคะ
เพื่อให้ทางคลินิกสามารถจัดคิวให้ลูกค้าท่านอื่นได้สะดวก ❤️`;

        return msg;
    }

    // Modal functions
    function openModal(msg) {
        $('confirmationMessage').textContent = msg || '';
        $('confirmationModal').style.display = 'block';
        $('confirmationModal').setAttribute('aria-hidden', 'false');
    }

    function closeModal() {
        $('confirmationModal').style.display = 'none';
        $('confirmationModal').setAttribute('aria-hidden', 'true');
    }

    $('closeBtn').addEventListener('click', closeModal);
    window.addEventListener('click', (e) => {
        if (e.target === $('confirmationModal')) closeModal();
    });

    // Copy
    $('copyBtn').addEventListener('click', () => {
        const txt = $('confirmationMessage').textContent || '';
        if (navigator.clipboard) {
            navigator.clipboard.writeText(txt).then(() => showCopyOk()).catch(() => fallbackCopy(txt));
        } else {
            fallbackCopy(txt);
        }
    });

    function fallbackCopy(text) {
        const ta = document.createElement('textarea');
        ta.value = text;
        document.body.appendChild(ta);
        ta.select();
        try {
            document.execCommand('copy');
            showCopyOk();
        } catch (_) {
            alert('ไม่สามารถคัดลอกได้ กรุณาเลือกข้อความแล้วกด Ctrl+C');
        }
        document.body.removeChild(ta);
    }

    function showCopyOk() {
        const el = $('copySuccess');
        el.style.display = 'block';
        setTimeout(() => el.style.display = 'none', 3000);
    }

    // Show alert
    function showAlert(message, type) {
        $('alertBox').innerHTML = `<div class="alert alert-${type}">${message}</div>`;
        setTimeout(() => $('alertBox').innerHTML = '', 5000);
    }

    // Submit form
    $('appointmentForm').addEventListener('submit', async (e) => {
        e.preventDefault();

        const formData = {
            fullName: $('fullName').value.trim(),
            phone: $('phoneNumber').value.trim(),
            date: $('appointmentDate').value,
            time: $('appointmentTime').value,
            source: $('leadSource').value,
            symptoms: $('symptoms').value,
            customSymptoms: $('customSymptoms').value.trim(),
            service: $('serviceType').value
        };

        // Validate
        if (!formData.fullName || !/^\d{10}$/.test(formData.phone) ||
            !formData.date || !formData.time || !formData.source || !formData.service) {
            showAlert('กรุณากรอกข้อมูลให้ครบถ้วน (ชื่อ, เบอร์โทร 10 หลัก, วันที่, เวลา, ช่องทาง และประเภทบริการ)', 'error');
            return;
        }

        const submitBtn = $('submitBtn');
        submitBtn.disabled = true;
        submitBtn.textContent = 'กำลังบันทึก...';

        try {
            const response = await fetch('{{ route("booking.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content
                },
                body: JSON.stringify(formData)
            });

            const result = await response.json();

            if (result.success) {
                // Show modal with confirmation message
                openModal(buildConfirmationMessage(formData));
                // Reset form
                $('appointmentForm').reset();
                $('appointmentDate').valueAsDate = new Date();
                $('customSymptomsGroup').style.display = 'none';
                phoneHelper.textContent = 'กรุณาใส่เบอร์โทรศัพท์ 10 หลักเท่านั้น';
                phoneHelper.className = 'input-helper';
                phoneInput.className = 'form-input';
            } else {
                showAlert(result.message || 'เกิดข้อผิดพลาด กรุณาลองใหม่อีกครั้ง', 'error');
            }
        } catch (error) {
            console.error('Error:', error);
            showAlert('เกิดข้อผิดพลาดในการเชื่อมต่อ กรุณาลองใหม่อีกครั้ง', 'error');
        } finally {
            submitBtn.disabled = false;
            submitBtn.textContent = 'ยืนยันการจองคิว';
        }
    });

    // Clear form
    $('clearBtn').addEventListener('click', () => {
        if (confirm('ต้องการล้างข้อมูลทั้งหมดใช่หรือไม่?')) {
            $('appointmentForm').reset();
            $('appointmentDate').valueAsDate = new Date();
            $('customSymptomsGroup').style.display = 'none';
            phoneHelper.textContent = 'กรุณาใส่เบอร์โทรศัพท์ 10 หลักเท่านั้น';
            phoneHelper.className = 'input-helper';
            phoneInput.className = 'form-input';
        }
    });
})();
</script>
</body>
</html>
