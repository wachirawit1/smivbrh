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
                        <div class="me-3">
                            <button class="btn btn-link text-white position-relative p-0" type="button" id="noti-dropdown"
                                data-bs-toggle="modal" data-bs-target="#modalAlertDetails">
                                <i class="fas fa-bell fs-4"></i>
                                @if ($has_alerts)
                                    <span
                                        class="position-absolute top-0 start-100 translate-middle badge rounded-pill bg-danger"
                                        style="font-size: 0.6rem;">
                                        {{ count($global_overdue) + count($global_today) + count($global_upcoming) }}
                                    </span>
                                @endif
                            </button>
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

    <!-- Global Alert Ticker -->
    @if ($has_alerts)
        <div class="container mt-3">
            <div class="alert-ticker" data-bs-toggle="modal" data-bs-target="#modalAlertDetails" style="cursor:pointer;">
                <div>
                    <span class="fw-bold text-danger"><i class="fas fa-bell blink"></i> การแจ้งเตือน:</span>
                    <span>{{ $global_alert_summary }} (คลิกเพื่อดูรายละเอียด)</span>
                </div>
                <span class="badge bg-danger rounded-pill">{{ count($global_overdue) + count($global_today) + count($global_upcoming) }}</span>
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

    <!-- Session Timeout Warning Modal -->
    <div class="modal fade" id="sessionTimeoutModal" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content card-3d border-0 shadow-lg">
                <div class="modal-header modal-header-custom border-0 p-4">
                    <h5 class="modal-title fw-bold"><i class="fas fa-hourglass-half me-2"></i>เซสชันกำลังจะหมดอายุ</h5>
                </div>
                <div class="modal-body p-4 text-center">
                    <p class="fs-5 mb-1">คุณไม่ได้ใช้งานระบบมาสักพักแล้ว</p>
                    <p class="text-muted mb-4">ระบบจะออกจากระบบอัตโนมัติในอีก <span id="sessionTimerCountdown" class="fw-bold text-danger fs-4">--</span> วินาที</p>
                    <div class="d-grid gap-2">
                        <button type="button" class="btn btn-primary btn-lg rounded-pill fw-bold py-3 shadow" onclick="extendSession()">
                            <i class="fas fa-sync-alt me-2"></i> ใช้งานต่อ (Extend Session)
                        </button>
                        <a href="{{ route('logout') }}" onclick="event.preventDefault(); document.getElementById('logout-form-session').submit();" class="btn btn-outline-secondary rounded-pill fw-bold py-2 mt-1">
                            ออกจากระบบเดี๋ยวนี้
                        </a>
                        <form id="logout-form-session" action="{{ route('logout') }}" method="POST" style="display: none;">
                            @csrf
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        @auth
            // Session Lifetime from config (minutes to milliseconds)
            const sessionLifetime = {{ config('session.lifetime') }} * 60 * 1000;
            const warningThreshold = 2 * 60 * 1000; // Warn 2 minutes before expiry
            let sessionTimeoutTimer;
            let countdownInterval;
            let warningModal = new bootstrap.Modal(document.getElementById('sessionTimeoutModal'));

            function startSessionTimer() {
                // Clear existing timers
                clearTimeout(sessionTimeoutTimer);
                clearInterval(countdownInterval);

                // Set timer for the warning threshold
                sessionTimeoutTimer = setTimeout(() => {
                    showSessionWarning();
                }, sessionLifetime - warningThreshold);
            }

            function showSessionWarning() {
                warningModal.show();
                let timeLeft = Math.floor(warningThreshold / 1000);
                const timerDisplay = document.getElementById('sessionTimerCountdown');
                
                timerDisplay.innerText = timeLeft;

                countdownInterval = setInterval(() => {
                    timeLeft--;
                    timerDisplay.innerText = timeLeft;

                    if (timeLeft <= 0) {
                        clearInterval(countdownInterval);
                        window.location.href = "{{ route('login') }}?expired=1";
                    }
                }, 1000);
            }

            function extendSession() {
                fetch("{{ route('session.keep-alive') }}")
                    .then(response => response.json())
                    .then(data => {
                        if (data.status === 'success') {
                            warningModal.hide();
                            startSessionTimer(); // Restart the timer
                        }
                    })
                    .catch(error => {
                        console.error('Error extending session:', error);
                        window.location.reload(); // Fallback
                    });
            }

            // Start the timer on page load
            document.addEventListener('DOMContentLoaded', startSessionTimer);

            // Optional: Reset timer on mouse move or click to be more user-friendly
            // let debounceTimer;
            // document.addEventListener('mousemove', () => {
            //     clearTimeout(debounceTimer);
            //     debounceTimer = setTimeout(extendSession, 10000); // Extend every 10s of activity
            // });
        @endauth
    </script>

    {{-- Global Alert Details Modal --}}
    @auth
        <div class="modal fade" id="modalAlertDetails" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white border-0" style="border-radius: 20px 20px 0 0;">
                        <h5 class="modal-title fw-bold"><i class="fas fa-exclamation-triangle"></i> รายละเอียดการแจ้งเตือน
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body bg-light">
                        <h6 class="text-purple fw-bold">1. ผู้ป่วยเกินกำหนดวันนัด</h6>
                        <ul class="list-group mb-3" id="alert-list-overdue">
                            @forelse($global_overdue as $p)
                                <li class="list-group-item text-danger fw-bold">
                                    {{ $p->prefix }}{{ $p->first_name }} {{ $p->last_name }} ({{ $p->amphoe }})
                                    - โทร: {{ $p->phone ?: 'ไม่มีข้อมูล' }}
                                </li>
                            @empty
                                <li class="list-group-item text-muted text-center">ไม่มีรายการ</li>
                            @endforelse
                        </ul>

                        <h6 class="text-primary fw-bold">2. ผู้ป่วยที่ต้องมาวันนี้</h6>
                        <ul class="list-group mb-3" id="alert-list-today">
                            @forelse($global_today as $p)
                                <li class="list-group-item text-primary fw-bold">
                                    {{ $p->prefix }}{{ $p->first_name }} {{ $p->last_name }} ({{ $p->amphoe }})
                                    - โทร: {{ $p->phone ?: 'ไม่มีข้อมูล' }}
                                </li>
                            @empty
                                <li class="list-group-item text-muted text-center">ไม่มีรายการ</li>
                            @endforelse
                        </ul>

                        <h6 class="fw-bold" style="color:#e67e22;">3. ผู้ป่วยที่ต้องมาตามนัด (ล่วงหน้า 3 วัน)</h6>
                        <ul class="list-group" id="alert-list-upcoming">
                            @forelse($global_upcoming as $p)
                                <li class="list-group-item text-dark">
                                    {{ $p->prefix }}{{ $p->first_name }} {{ $p->last_name }} ({{ $p->amphoe }})
                                    - โทร: {{ $p->phone ?: 'ไม่มีข้อมูล' }}
                                    @php
                                        $diff = now()->diffInDays($p->next_appointment_date, false);
                                    @endphp
                                    @if ($diff > 0)
                                        (อีก {{ (int) $diff }} วัน)
                                    @endif
                                </li>
                            @empty
                                <li class="list-group-item text-muted text-center">ไม่มีรายการ</li>
                            @endforelse
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    @endauth

    @stack('scripts')
</body>

</html>
