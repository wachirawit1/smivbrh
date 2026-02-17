<!DOCTYPE html>
<html lang="th">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>SMI-V BURIRAM - ระบบติดตามผู้ป่วยจิตเวช</title>
    <link href="https://fonts.googleapis.com/css2?family=Prompt:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        :root {
            --primary-blue: #007bff;
            --gradient-blue: linear-gradient(135deg, #00c6ff 0%, #0072ff 100%);
            --purple-alert: #8e44ad;
            --blue-light: #00c6ff;
            --shadow-3d: 0 10px 25px rgba(0, 114, 255, 0.15), inset 0 0 0 1px rgba(255, 255, 255, 0.8);
        }

        body {
            font-family: 'Prompt', sans-serif;
            background-color: #f0f8ff;
            color: #333;
            margin: 0;
            padding: 0;
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }

        /* 3D UI Effects */
        .card-3d {
            background: rgba(255, 255, 255, 0.95);
            border: none;
            border-radius: 15px;
            box-shadow: var(--shadow-3d);
            transition: transform 0.3s ease;
        }

        .card-3d:hover {
            transform: translateY(-5px);
            box-shadow: 0 15px 35px rgba(0, 114, 255, 0.2);
        }

        .btn-3d {
            background: var(--gradient-blue);
            color: white;
            border: none;
            border-radius: 8px;
            box-shadow: 0 4px 10px rgba(0, 194, 255, 0.4);
            transition: all 0.2s;
            text-decoration: none;
            display: inline-block;
            padding: 8px 20px;
        }

        .btn-3d:hover {
            transform: scale(1.02);
            color: white;
            box-shadow: 0 6px 15px rgba(0, 194, 255, 0.6);
        }

        .navbar-custom {
            background: var(--gradient-blue);
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
        }

        .modal-content {
            border-radius: 20px;
            border: none;
            box-shadow: 0 20px 40px rgba(0, 0, 0, 0.25);
        }

        .modal-header-custom {
            background: var(--gradient-blue);
            color: white;
            border-radius: 20px 20px 0 0;
        }

        /* OAS & Status Colors matching Template */
        .bg-oas-3 {
            background-color: #ff4d4d !important;
            color: white;
        }

        .bg-oas-2 {
            background-color: #ff9800 !important;
            color: white;
        }

        .bg-oas-1 {
            background-color: #ffeb3b !important;
            color: black;
        }

        .bg-oas-0 {
            background-color: #4caf50 !important;
            color: white;
        }

        .border-purple {
            border-color: #6f42c1 !important;
        }

        .text-purple {
            color: #6f42c1 !important;
        }

        .bg-purple {
            background-color: var(--purple-alert) !important;
            color: white;
        }

        /* Ticker Alert */
        .alert-ticker {
            background: white;
            border-left: 5px solid var(--primary-blue);
            border-radius: 10px;
            padding: 10px 20px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
            cursor: pointer;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .section-title {
            border-left: 5px solid var(--primary-blue);
            padding-left: 10px;
            font-weight: 600;
            margin-top: 20px;
            margin-bottom: 15px;
            color: var(--primary-blue);
        }

        .main-content {
            flex: 1;
        }

        .footer-text {
            font-size: 0.85rem;
            color: #666;
            text-align: center;
            margin-top: auto;
            padding: 20px;
            border-top: 1px solid #ddd;
            background: white;
        }

        @keyframes pulse-red {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(255, 77, 77, 0.7);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(255, 77, 77, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(255, 77, 77, 0);
            }
        }

        @keyframes pulse-purple {
            0% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(142, 68, 173, 0.7);
            }

            70% {
                transform: scale(1);
                box-shadow: 0 0 0 10px rgba(142, 68, 173, 0);
            }

            100% {
                transform: scale(0.95);
                box-shadow: 0 0 0 0 rgba(142, 68, 173, 0);
            }
        }

        .pulse-red {
            animation: pulse-red 2s infinite;
        }

        .pulse-purple {
            animation: pulse-purple 2s infinite;
        }

        .blink {
            animation: blinker 1.5s linear infinite;
        }

        @keyframes blinker {
            50% {
                opacity: 0;
            }
        }
    </style>
</head>

<body>

    <!-- NAV (Exact as code) -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-custom p-3 sticky-top">
        <div class="container-fluid">
            <a class="navbar-brand fw-bold d-flex align-items-center" href="{{ route('dashboard') }}">
                <img src="https://cdn-icons-png.flaticon.com/512/3774/3774299.png"
                    style="height: 40px; width: auto; margin-right: 12px; border-radius: 5px;">
                SMI-V BURIRAM
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link fw-bold {{ Request::is('dashboard') || Request::is('/') ? 'active' : '' }}"
                            href="{{ route('dashboard') }}">
                            <i class="fas fa-chart-line me-1"></i> Dashboard & Map
                        </a>
                    </li>
                    @auth
                        <li class="nav-item">
                            <a class="nav-link fw-bold {{ Request::is('patients*') ? 'active' : '' }}"
                                href="{{ route('patients.index') }}">
                                <i class="fas fa-users me-1"></i> ข้อมูลพื้นที่ของตนเอง
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link fw-bold {{ Request::is('map') ? 'active' : '' }}" href="{{ route('map') }}">
                                <i class="fas fa-map-marked-alt me-1"></i> แผนที่จุดเสี่ยง
                            </a>
                        </li>
                        @if (auth()->user()->role === 'admin')
                            <li class="nav-item">
                                <a class="nav-link fw-bold {{ Request::is('admin/users*') ? 'active' : '' }}"
                                    href="{{ route('admin.users') }}">
                                    <i class="fas fa-user-shield me-1"></i> จัดการผู้ใช้ (Admin)
                                </a>
                            </li>
                        @endif
                    @endauth
                </ul>
                <div class="d-flex align-items-center">
                    @auth
                        <!-- Notification Bell -->
                        <div class="dropdown me-3">
                            <button class="btn btn-link text-white position-relative p-0" type="button" id="noti-dropdown"
                                data-bs-toggle="dropdown">
                                <i class="fas fa-bell fs-4"></i>
                                @if (count($global_notifications) > 0)
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                        style="font-size: 0.6rem;">
                                        {{ count($global_notifications) }}
                                    </span>
                                @endif
                            </button>
                            <ul class="dropdown-menu dropdown-menu-end card-3d border-0 shadow-lg mt-2"
                                style="width: 320px; max-height: 400px; overflow-y: auto;">
                                <li class="p-3 border-bottom">
                                    <h6 class="mb-0 fw-bold"><i class="fas fa-bell me-2 mt-1"></i>การแจ้งเตือนทั้งหมด</h6>
                                </li>
                                @forelse($global_notifications as $noti)
                                    <li>
                                        <a class="dropdown-item p-3 border-bottom d-flex align-items-start gap-3"
                                            href="{{ $noti['url'] }}">
                                            <div class="rounded-circle p-2 bg-light text-primary"><i
                                                    class="fas {{ $noti['icon'] }}"></i></div>
                                            <div style="white-space: normal;">
                                                <div class="fw-bold"
                                                    style="color: {{ $noti['color'] }}; font-size: 0.9rem;">
                                                    {{ $noti['text'] }}</div>
                                                <small class="text-muted">คลิกเพื่อดูรายละเอียด</small>
                                            </div>
                                        </a>
                                    </li>
                                @empty
                                    <li class="p-4 text-center text-muted">ไม่มีการแจ้งเตือนใหม่</li>
                                @endforelse
                            </ul>
                        </div>

                        <span class="text-white me-3 fw-bold">👤 {{ auth()->user()->name }}</span>
                        <form action="{{ route('logout') }}" method="POST" style="display: inline;">
                            @csrf
                            <button type="submit" class="btn btn-outline-light btn-sm rounded-pill px-3">Logout</button>
                        </form>
                    @else
                        <a href="{{ route('register') }}"
                            class="btn btn-outline-light btn-sm rounded-pill px-4 fw-bold me-2"><i
                                class="fas fa-user-plus"></i>
                            ลงทะเบียน</a>
                        <a href="{{ route('login') }}"
                            class="btn btn-light btn-sm rounded-pill px-4 fw-bold text-primary"><i class="fas fa-key"></i>
                            เข้าสู่ระบบ</a>
                    @endauth
                </div>
            </div>
        </div>
    </nav>

    <!-- Global Alert Ticker (Integrated with Laravel dynamic data) -->
    @if (isset($global_notifications) && count($global_notifications) > 0)
        <div class="container mt-3">
            <div class="alert-ticker" onclick="document.getElementById('noti-dropdown').click()">
                <div>
                    <span class="fw-bold text-danger"><i class="fas fa-bell blink"></i> การแจ้งเตือน:</span>
                    <span>มีรายการต้องติดตาม {{ count($global_notifications) }} รายการ</span>
                </div>
                <span class="badge bg-primary rounded-pill">กดดูที่กระดิ่ง</span>
            </div>
        </div>
    @endif

    <div class="container mt-4 main-content">
        @if (session('success'))
            <div class="alert alert-success card-3d border-0">{{ session('success') }}</div>
        @endif
        @if (session('error'))
            <div class="alert alert-danger card-3d border-0">{{ session('error') }}</div>
        @endif

        @yield('content')
    </div>

    <div class="footer-text">
        พัฒนาระบบโดย พว.รุ่งทิพย์ เบญจพันเลิศ และ น.ส.วิสสุชา รัตนจริยา <br>
        กลุ่มงานจิตเวชและยาเสพติด โรงพยาบาลบุรีรัมย์ “ระบบติดตามผู้ป่วยจิตเวชและยาเสพติด จังหวัดบุรีรัมย์”
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    @stack('scripts')
</body>

</html>
