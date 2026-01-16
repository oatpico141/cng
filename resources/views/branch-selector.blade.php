<!DOCTYPE html>
<html lang="th">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>เลือกสาขา - CNG Clinic</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Kanit:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Kanit', sans-serif;
            min-height: 100vh;
            background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 50%, #1e3a8a 100%);
            background-size: 400% 400%;
            animation: gradientBG 15s ease infinite;
            padding: 30px 20px;
        }

        @keyframes gradientBG {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }

        .container {
            max-width: 900px;
            margin: 0 auto;
        }

        /* Success Alert */
        .alert-success {
            background: rgba(34, 197, 94, 0.95);
            backdrop-filter: blur(10px);
            color: white;
            padding: 16px 20px;
            border-radius: 16px;
            margin-bottom: 24px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 15px;
            box-shadow: 0 4px 20px rgba(34, 197, 94, 0.3);
            animation: slideDown 0.5s ease;
        }

        @keyframes slideDown {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }

        /* User Card */
        .user-card {
            background: rgba(255, 255, 255, 0.15);
            backdrop-filter: blur(20px);
            border-radius: 20px;
            padding: 24px 30px;
            margin-bottom: 30px;
            border: 1px solid rgba(255, 255, 255, 0.2);
            display: flex;
            align-items: center;
            justify-content: space-between;
            flex-wrap: wrap;
            gap: 16px;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 16px;
            color: white;
        }

        .user-avatar {
            width: 56px;
            height: 56px;
            background: linear-gradient(135deg, #ffffff 0%, #e0f2fe 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: #0ea5e9;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        }

        .user-details h3 {
            font-size: 18px;
            font-weight: 600;
            margin-bottom: 4px;
        }

        .user-details p {
            font-size: 14px;
            opacity: 0.9;
            margin: 0;
        }

        .btn-logout {
            background: rgba(255, 255, 255, 0.2);
            color: white;
            border: 2px solid rgba(255, 255, 255, 0.4);
            padding: 10px 20px;
            border-radius: 12px;
            font-size: 14px;
            font-weight: 500;
            font-family: 'Kanit', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            gap: 8px;
            text-decoration: none;
        }

        .btn-logout:hover {
            background: white;
            color: #0ea5e9;
            border-color: white;
            transform: translateY(-2px);
        }

        /* Header */
        .header {
            text-align: center;
            margin-bottom: 40px;
            color: white;
        }

        .header-icon {
            width: 80px;
            height: 80px;
            background: rgba(255, 255, 255, 0.2);
            backdrop-filter: blur(10px);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 36px;
            border: 2px solid rgba(255, 255, 255, 0.3);
        }

        .header h1 {
            font-size: 28px;
            font-weight: 600;
            margin-bottom: 8px;
        }

        .header p {
            font-size: 16px;
            opacity: 0.9;
        }

        /* Branch Grid */
        .branch-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
            gap: 24px;
        }

        /* Branch Card */
        .branch-card {
            background: white;
            border-radius: 20px;
            padding: 28px;
            text-align: center;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.1);
            transition: all 0.4s ease;
            cursor: pointer;
            position: relative;
            overflow: hidden;
        }

        .branch-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #0ea5e9, #3b82f6);
            transform: scaleX(0);
            transition: transform 0.3s ease;
        }

        .branch-card:hover {
            transform: translateY(-8px);
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.15);
        }

        .branch-card:hover::before {
            transform: scaleX(1);
        }

        .branch-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 100%);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 20px;
            font-size: 30px;
            color: white;
            box-shadow: 0 8px 25px rgba(14, 165, 233, 0.4);
            transition: transform 0.3s ease;
        }

        .branch-card:hover .branch-icon {
            transform: scale(1.1);
        }

        .branch-name {
            font-size: 20px;
            font-weight: 600;
            color: #1e293b;
            margin-bottom: 16px;
        }

        .branch-info-list {
            margin-bottom: 20px;
        }

        .branch-info-item {
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 14px;
            color: #64748b;
            margin-bottom: 8px;
        }

        .branch-info-item i {
            color: #0ea5e9;
            font-size: 16px;
        }

        .btn-select {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #0ea5e9 0%, #3b82f6 100%);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 15px;
            font-weight: 600;
            font-family: 'Kanit', sans-serif;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }

        .btn-select:hover {
            transform: translateY(-2px);
            box-shadow: 0 8px 25px rgba(14, 165, 233, 0.4);
        }

        /* Empty State */
        .empty-state {
            background: rgba(255, 255, 255, 0.1);
            backdrop-filter: blur(10px);
            border-radius: 20px;
            padding: 60px 40px;
            text-align: center;
            color: white;
        }

        .empty-state i {
            font-size: 64px;
            margin-bottom: 20px;
            opacity: 0.8;
        }

        .empty-state h3 {
            font-size: 22px;
            margin-bottom: 10px;
        }

        .empty-state p {
            opacity: 0.8;
        }

        /* Responsive */
        @media (max-width: 640px) {
            .user-card {
                flex-direction: column;
                text-align: center;
            }

            .user-info {
                flex-direction: column;
            }

            .header h1 {
                font-size: 22px;
            }

            .branch-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="container">
        <!-- Success Message -->
        @if(session('success'))
        <div class="alert-success">
            <i class="bi bi-check-circle-fill"></i>
            <span>{{ session('success') }}</span>
        </div>
        @endif

        <!-- User Card -->
        <div class="user-card">
            <div class="user-info">
                <div class="user-avatar">
                    <i class="bi bi-person-fill"></i>
                </div>
                <div class="user-details">
                    <h3>{{ auth()->user()->name }}</h3>
                    <p>{{ auth()->user()->role->name ?? 'Administrator' }}</p>
                </div>
            </div>
            <a href="{{ url('/logout') }}" class="btn-logout">
                <i class="bi bi-box-arrow-right"></i>
                ออกจากระบบ
            </a>
        </div>

        <!-- Header -->
        <div class="header">
            <div class="header-icon">
                <i class="bi bi-building"></i>
            </div>
            <h1>เลือกสาขาที่ต้องการจัดการ</h1>
            <p>กรุณาเลือกสาขาเพื่อเข้าสู่ระบบจัดการ</p>
        </div>

        <!-- Branch Grid -->
        @if($branches->count() > 0)
        <div class="branch-grid">
            @foreach($branches as $branch)
            <div class="branch-card" onclick="document.getElementById('form-{{ $branch->id }}').submit()">
                <div class="branch-icon">
                    <i class="bi bi-hospital"></i>
                </div>
                <div class="branch-name">{{ $branch->name }}</div>
                <div class="branch-info-list">
                    @if($branch->address)
                    <div class="branch-info-item">
                        <i class="bi bi-geo-alt-fill"></i>
                        <span>{{ Str::limit($branch->address, 40) }}</span>
                    </div>
                    @endif
                    @if($branch->phone)
                    <div class="branch-info-item">
                        <i class="bi bi-telephone-fill"></i>
                        <span>{{ $branch->phone }}</span>
                    </div>
                    @endif
                    @if(!$branch->address && !$branch->phone)
                    <div class="branch-info-item">
                        <i class="bi bi-info-circle"></i>
                        <span>ไม่มีข้อมูลเพิ่มเติม</span>
                    </div>
                    @endif
                </div>
                <form id="form-{{ $branch->id }}" method="POST" action="{{ route('branch.switch') }}">
                    @csrf
                    <input type="hidden" name="branch_id" value="{{ $branch->id }}">
                    <button type="submit" class="btn-select">
                        <i class="bi bi-arrow-right-circle-fill"></i>
                        เข้าสู่สาขานี้
                    </button>
                </form>
            </div>
            @endforeach
        </div>
        @else
        <div class="empty-state">
            <i class="bi bi-building-slash"></i>
            <h3>ไม่พบสาขา</h3>
            <p>ยังไม่มีสาขาในระบบ กรุณาติดต่อผู้ดูแลระบบ</p>
        </div>
        @endif
    </div>
</body>
</html>
