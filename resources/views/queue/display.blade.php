<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>จอแสดงคิว - กายสิริ คลินิก</title>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700;800;900&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Kanit', sans-serif;
            background: #0f172a;
            min-height: 100vh;
            overflow: hidden;
        }

        .container {
            height: 100vh;
            display: flex;
            flex-direction: column;
        }

        /* Header */
        .header {
            background: linear-gradient(135deg, #0284c7, #0ea5e9);
            color: white;
            padding: 1rem 3rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .clinic-name {
            font-size: 2rem;
            font-weight: 700;
        }

        .clock {
            font-size: 2.5rem;
            font-weight: 400;
        }

        /* Main */
        .main {
            flex: 1;
            padding: 1.5rem 2rem;
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1.5rem;
        }

        /* Calling Section - Big and Prominent */
        .calling-section {
            background: linear-gradient(135deg, #dc2626, #ef4444);
            border-radius: 24px;
            padding: 2rem;
            display: flex;
            flex-direction: column;
            align-items: center;
            justify-content: center;
            box-shadow: 0 10px 40px rgba(220, 38, 38, 0.4);
            animation: calling-pulse 2s infinite;
        }

        .calling-section.empty {
            background: #1e293b;
            animation: none;
            box-shadow: none;
        }

        @keyframes calling-pulse {
            0%, 100% { transform: scale(1); box-shadow: 0 10px 40px rgba(220, 38, 38, 0.4); }
            50% { transform: scale(1.02); box-shadow: 0 15px 60px rgba(220, 38, 38, 0.6); }
        }

        .calling-label {
            font-size: 2rem;
            color: rgba(255, 255, 255, 0.9);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        .calling-number {
            font-size: 12rem;
            font-weight: 900;
            color: white;
            line-height: 1;
            text-shadow: 0 8px 30px rgba(0, 0, 0, 0.3);
        }

        .calling-name {
            font-size: 3.5rem;
            font-weight: 700;
            color: white;
            margin-top: 1rem;
        }

        .calling-empty {
            color: #64748b;
            font-size: 2rem;
        }

        /* Right Side - Waiting Queue */
        .right-side {
            display: flex;
            flex-direction: column;
        }

        .section-header {
            font-size: 1.5rem;
            color: #94a3b8;
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }

        /* Waiting Section */
        .waiting-section {
            flex: 1;
            background: #1e293b;
            border-radius: 16px;
            padding: 1.5rem;
            display: flex;
            flex-direction: column;
            border-left: 6px solid #f59e0b;
        }

        .waiting-list {
            flex: 1;
            display: flex;
            flex-direction: column;
            gap: 0.5rem;
        }

        .waiting-item {
            background: #0f172a;
            border-radius: 10px;
            padding: 0.75rem 1.25rem;
            display: flex;
            align-items: center;
            gap: 1rem;
        }

        .waiting-item:first-child {
            background: linear-gradient(135deg, #1e3a5f, #1e293b);
            border: 1px solid #0ea5e9;
        }

        .waiting-num {
            font-size: 2rem;
            font-weight: 700;
            color: #f59e0b;
            width: 80px;
        }

        .waiting-name {
            flex: 1;
            font-size: 1.25rem;
            font-weight: 500;
            color: white;
        }

        .waiting-time {
            font-size: 1rem;
            color: #64748b;
        }

        .empty-state {
            flex: 1;
            display: flex;
            align-items: center;
            justify-content: center;
            color: #475569;
            font-size: 1.25rem;
        }

        /* Footer */
        .footer {
            background: #0ea5e9;
            color: white;
            padding: 1rem 3rem;
            text-align: center;
            font-size: 1.5rem;
            font-weight: 500;
        }

        /* Responsive */
        @media (max-width: 1200px) {
            .main {
                grid-template-columns: 1fr;
            }

            .calling-number {
                font-size: 8rem;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Header -->
        <div class="header">
            <div class="clinic-name">
                <i class="bi bi-heart-pulse me-2"></i>กายสิริ คลินิก
            </div>
            <div class="clock" id="clock">{{ now()->format('H:i') }}</div>
        </div>

        <!-- Main -->
        <div class="main">
            @php
                $allItems = collect();

                if ($queues->isNotEmpty()) {
                    foreach ($queues as $q) {
                        $allItems->push([
                            'number' => $q->queue_number,
                            'name' => $q->patient->name ?? 'ไม่ระบุ',
                            'time' => $q->appointment ? substr($q->appointment->appointment_time, 0, 5) : '-',
                            'status' => $q->status,
                            'pt_name' => $q->pt->name ?? null,
                            'started_at' => $q->started_at
                        ]);
                    }
                } else {
                    $counter = 1;
                    foreach ($appointments as $apt) {
                        if (in_array($apt->status, ['cancelled', 'rescheduled', 'no_show'])) {
                            continue;
                        }

                        $status = match($apt->status) {
                            'pending' => 'waiting',
                            'calling' => 'calling',
                            'confirmed' => 'in_treatment',
                            'awaiting_payment' => 'awaiting_payment',
                            'completed' => 'completed',
                            default => $apt->status
                        };

                        $allItems->push([
                            'number' => $counter,
                            'name' => $apt->patient->name ?? 'ไม่ระบุ',
                            'time' => substr($apt->appointment_time, 0, 5),
                            'status' => $status,
                            'pt_name' => $apt->pt->name ?? null,
                            'started_at' => $apt->status === 'confirmed' ? $apt->updated_at : null
                        ]);
                        $counter++;
                    }
                }

                $calling = $allItems->where('status', 'calling')->first();
                $waiting = $allItems->where('status', 'waiting')->take(8);
            @endphp

            <!-- Calling Section - Left Side -->
            <div class="calling-section {{ !$calling ? 'empty' : '' }}">
                @if($calling)
                    <div class="calling-label">
                        <i class="bi bi-megaphone-fill"></i>
                        กำลังเรียก
                    </div>
                    <div class="calling-name" style="font-size: 5rem;">{{ $calling['name'] }}</div>
                @else
                    <div class="calling-label" style="color: #64748b;">
                        <i class="bi bi-megaphone"></i>
                        กำลังเรียก
                    </div>
                    <div class="calling-number" style="color: #475569;">--</div>
                    <div class="calling-empty">รอเรียกคิว</div>
                @endif
            </div>

            <!-- Right Side - Waiting Queue Only -->
            <div class="right-side">
                <div class="waiting-section">
                    <div class="section-header">
                        <i class="bi bi-clock"></i>
                        คิวถัดไป
                    </div>
                    @if($waiting->count() > 0)
                        <div class="waiting-list">
                            @foreach($waiting as $item)
                                <div class="waiting-item">
                                    <div class="waiting-name" style="flex: 1;">{{ $item['name'] }}</div>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="empty-state">
                            <i class="bi bi-check-circle me-2"></i>ไม่มีคิวรอ
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <i class="bi bi-volume-up me-2"></i>
            กรุณารอฟังเรียกชื่อ และเข้าห้องตรวจตามลำดับ
        </div>
    </div>

    <script>
        // Update clock
        function updateClock() {
            const now = new Date();
            const hours = String(now.getHours()).padStart(2, '0');
            const minutes = String(now.getMinutes()).padStart(2, '0');
            document.getElementById('clock').textContent = `${hours}:${minutes}`;
        }
        setInterval(updateClock, 1000);

        // Auto refresh every 10 seconds
        setInterval(function() {
            location.reload();
        }, 10000);
    </script>
</body>
</html>
