@extends('layout')

@section('content')
    <h3 class="fw-bold text-primary mb-4">Dashboard วิเคราะห์ข้อมูลผู้ป่วยระดับจังหวัด</h3>

    {{-- Filter Card --}}
    <div class="card card-3d p-3 mb-4">
        <form action="{{ route('dashboard') }}" method="GET">
            <div class="row g-2 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold">คัดกรองตามอำเภอ</label>
                    <select name="area" class="form-select" onchange="this.form.submit()">
                        <option value="">ดูทั้งหมดทั้งจังหวัด</option>
                        @foreach ($areas as $areaName)
                            <option value="{{ $areaName }}" {{ ($areaFilter ?? '') == $areaName ? 'selected' : '' }}>
                                {{ $areaName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">ประเภท SMI-V</label>
                    <select name="smiv_group" class="form-select" onchange="this.form.submit()">
                        <option value="">ทั้งหมด</option>
                        @foreach ($smivNames as $key => $name)
                            <option value="{{ $key }}" {{ ($smivFilter ?? '') == $key ? 'selected' : '' }}>
                                {{ $key }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold">OAS Score</label>
                    <select name="oas_score" class="form-select" onchange="this.form.submit()">
                        <option value="">ทั้งหมด</option>
                        <option value="3" {{ ($oasFilter ?? '') == '3' ? 'selected' : '' }}>ฉุกเฉิน</option>
                        <option value="2" {{ ($oasFilter ?? '') == '2' ? 'selected' : '' }}>เร่งด่วน</option>
                        <option value="1" {{ ($oasFilter ?? '') == '1' ? 'selected' : '' }}>กึ่งเร่งด่วน</option>
                        <option value="0" {{ ($oasFilter ?? '') == '0' ? 'selected' : '' }}>ดูแลตามอาการ</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <a href="{{ route('dashboard') }}" class="btn btn-secondary w-100">ล้างค่า</a>
                </div>
            </div>
        </form>
    </div>

    {{-- Stats Cards --}}
    <div class="row g-3 mb-4">
        <div class="col-md-3">
            <div class="card card-3d p-3 text-center border-bottom border-primary border-5">
                <h5 class="text-muted">ผู้ป่วยใหม่รวม</h5>
                <h2 class="fw-bold text-primary">
                    {{ array_sum(array_filter($stats, fn($k) => in_array($k, ['0', '1', '2', '3']), ARRAY_FILTER_USE_KEY)) }}
                </h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-3d p-3 text-center border-bottom border-5" style="border-color: #00c6ff !important;">
                <h5 class="fw-bold" style="color:#00c6ff;">มาตามนัด</h5>
                <h2 class="fw-bold" style="color:#00c6ff;">{{ $scheduledCount ?? 0 }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-3d p-3 text-center border-bottom border-5" style="border-color: #8e44ad !important;">
                <h5 class="fw-bold text-purple">ไม่มา/เกินนัด</h5>
                <h2 class="fw-bold text-purple">{{ $overdueCount ?? 0 }}</h2>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card card-3d p-2 text-center border-bottom border-info border-5">
                <h6 class="text-muted mb-1 mt-1">อาการดีขึ้น (จำลองข้อมูล)</h6>
                <div class="d-flex justify-content-around mt-2">
                    <div><small class="text-muted">3 เดือน</small>
                        <h5 class="fw-bold text-info">{{ $improvement3m ?? 0 }}%</h5>
                    </div>
                    <div><small class="text-muted">6 เดือน</small>
                        <h5 class="fw-bold text-info">{{ $improvement6m ?? 0 }}%</h5>
                    </div>
                    <div><small class="text-muted">1 ปี</small>
                        <h5 class="fw-bold text-info">{{ $improvement6m ?? 0 }}%</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Charts --}}
    <div class="row g-3 mb-4 d-flex align-items-stretch">
        <div class="col-md-6">
            <div class="card card-3d p-4 h-100">
                <h6 class="fw-bold text-center mb-3">สัดส่วนประเภท SMI-V</h6>
                <div style="position: relative; height: 320px;">
                    <canvas id="chartSmiv"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card card-3d p-4 h-100">
                <h6 class="fw-bold text-center mb-3">ระดับความรุนแรง OAS Score</h6>
                <div style="position: relative; height: 320px;">
                    <canvas id="chartOas"></canvas>
                </div>
            </div>
        </div>
    </div>

    {{-- Map --}}
    <div class="card card-3d p-4 mb-5 bg-light">
        <h5 class="fw-bold text-primary text-center mb-3">
            <i class="fas fa-map-marked-alt"></i> แผนที่เฝ้าระวังจุดเสี่ยงและพื้นที่รับผิดชอบ (เตือนเชิงรุก)
        </h5>
        <div id="map" style="height: 450px; border-radius: 10px; z-index: 1;"></div>
        <div class="mt-3 d-flex flex-wrap gap-3 justify-content-center bg-white p-2 rounded shadow-sm border">
            <small><i class="fas fa-circle" style="color:#ff4d4d"></i> ฉุกเฉิน</small>
            <small><i class="fas fa-circle" style="color:#ff9800"></i> เร่งด่วน</small>
            <small><i class="fas fa-circle" style="color:#ffeb3b"></i> กึ่งเร่งด่วน</small>
            <small><i class="fas fa-circle" style="color:#4caf50"></i> ดูแลตามอาการ</small>
            <small><i class="fas fa-circle" style="color:#8e44ad"></i> ไม่มา/เกินนัด (กระพริบ)</small>
        </div>
    </div>

@endsection

@push('scripts')
    @php
        $districtCoords = [
            'เมืองบุรีรัมย์' => [14.993, 103.102],
            'กระสัง' => [14.919, 103.256],
            'คูเมือง' => [15.289, 103.016],
            'แคนดง' => [15.302, 103.107],
            'เฉลิมพระเกียรติ' => [14.566, 102.933],
            'ชำนิ' => [14.808, 102.851],
            'นางรอง' => [14.629, 102.791],
            'นาโพธิ์' => [15.631, 102.951],
            'โนนดินแดง' => [14.301, 102.760],
            'โนนสุวรรณ' => [14.583, 102.593],
            'บ้านกรวด' => [14.413, 103.090],
            'บ้านด่าน' => [15.158, 103.175],
            'บ้านใหม่ไชยพจน์' => [15.541, 102.825],
            'ประโคนชัย' => [14.605, 103.125],
            'ปะคำ' => [14.440, 102.727],
            'พลับพลาชัย' => [14.811, 103.181],
            'พุทไธสง' => [15.539, 102.981],
            'ละหานทราย' => [14.417, 102.868],
            'ลำปลายมาศ' => [15.025, 102.839],
            'สตึก' => [15.297, 103.292],
            'หนองกี่' => [14.671, 102.535],
            'หนองหงส์' => [14.851, 102.695],
            'ห้วยราช' => [15.018, 103.208],
        ];

        $patientPoints = [];
        $patients = \App\Models\Patient::select(
            'id',
            'prefix',
            'first_name',
            'last_name',
            'amphoe',
            'oas_score',
            'status',
            'next_appointment_date',
        )
            ->when($areaFilter ?? null, fn($q) => $q->where('amphoe', $areaFilter))
            ->when($smivFilter ?? null, fn($q) => $q->whereJsonContains('smiv_group', $smivFilter))
            ->when($oasFilter ?? null, fn($q) => $q->where('oas_score', $oasFilter))
            ->get();

        foreach ($patients as $patient) {
            $areaName = $patient->amphoe ?: 'เมืองบุรีรัมย์';
            $baseCoord = $districtCoords[$areaName] ?? [14.99, 103.1];
            
            // Prototype logic for overdue: Date in past AND visit_status doesn't mention "มา"
            $isOverdue = $patient->next_appointment_date && 
                         $patient->next_appointment_date->isPast() && 
                         (stripos($patient->visit_status, 'มา') === false);

            $patientPoints[] = [
                'oas' => $patient->oas_score ?? '0',
                'lat' => $baseCoord[0] + mt_rand(-50, 50) / 1000,
                'lng' => $baseCoord[1] + mt_rand(-50, 50) / 1000,
                'url' => route('patients.show', $patient),
                'name' => $patient->prefix . $patient->first_name . ' ' . $patient->last_name,
                'amphoe' => $areaName,
                'overdue' => $isOverdue,
                'next_date' => $patient->next_appointment_date
                    ? $patient->next_appointment_date->format('d/m/Y')
                    : '-',
            ];
        }
    @endphp

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // ------ SMI-V Bar Chart ------
        new Chart(document.getElementById('chartSmiv'), {
            type: 'bar',
            data: {
                labels: @json(array_keys($smivStats)),
                datasets: [{
                    label: 'จำนวนผู้ป่วย',
                    data: @json(array_values($smivStats)),
                    backgroundColor: '#00c6ff',
                    borderWidth: 0,
                    borderRadius: 5
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: true,
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { family: 'Prompt', size: 12 }
                        }
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: { stepSize: 1 }
                    }
                }
            }
        });

        // ------ OAS Doughnut Chart ------
        new Chart(document.getElementById('chartOas'), {
            type: 'doughnut',
            data: {
                labels: ['ดูแลตามอาการ', 'กึ่งเร่งด่วน', 'เร่งด่วน', 'ฉุกเฉิน'],
                datasets: [{
                    data: [
                        {{ $stats['0'] ?? 0 }},
                        {{ $stats['1'] ?? 0 }},
                        {{ $stats['2'] ?? 0 }},
                        {{ $stats['3'] ?? 0 }}
                    ],
                    backgroundColor: ['#4caf50', '#ffeb3b', '#ff9800', '#ff4d4d'],
                    borderWidth: 0,
                    hoverOffset: 15
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'top',
                        labels: {
                            usePointStyle: true,
                            padding: 20,
                            font: { family: 'Prompt', size: 12 }
                        }
                    }
                }
            }
        });

        // ------ Leaflet Map ------
        var map = L.map('map').setView([14.993, 103.102], 9);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
            attribution: '© OpenStreetMap contributors',
            maxZoom: 18
        }).addTo(map);

        var patients = @json($patientPoints);
        var oasColors = {
            '0': '#4caf50',
            '1': '#ffeb3b',
            '2': '#ff9800',
            '3': '#ff4d4d'
        };

        patients.forEach(function(p) {
            var mColor = p.overdue ? '#8e44ad' : (oasColors[p.oas] || '#4caf50');
            var pulseClass = p.overdue ? 'pulse-marker-purple' : (p.oas === '3' ? 'pulse-marker-red' : '');

            var iconHtml =
                `<div style="background-color:${mColor}; width:16px; height:16px; border-radius:50%; border:2px solid white; box-shadow: 0 2px 5px rgba(0,0,0,0.3);" class="${pulseClass}"></div>`;
            var customIcon = L.divIcon({
                html: iconHtml,
                className: '',
                iconSize: [16, 16],
                iconAnchor: [8, 8]
            });

            @auth
            // Logged in: show name and link
            var popupHtml = `
                <div class="text-center p-1">
                    <b class="text-primary d-block mb-1">${p.name}</b>
                    <small class="text-muted d-block mb-1">${p.amphoe}</small>
                    <span class="badge mb-2" style="background-color:${mColor}; color:${p.oas==='1'?'#333':'white'};">
                        ${p.overdue ? 'เกินนัด' : 'OAS-'+p.oas}
                    </span><br>
                    <small class="text-muted">นัดถัดไป: ${p.next_date}</small><br>
                    <a href="${p.url}" class="btn btn-sm btn-outline-primary mt-2 rounded-pill px-3 w-100 fw-bold">ดูรายละเอียด</a>
                </div>`;
            @else
            // Guest: hide name
            var popupHtml = `
                <div class="text-center p-1">
                    <b class="text-primary">ผู้ป่วย (ปกปิดชื่อ)</b><br>
                    <small class="text-muted">${p.amphoe}</small><br>
                    <span class="badge mt-1" style="background-color:${mColor}; color:${p.oas==='1'?'#333':'white'};">
                        ${p.overdue ? 'เกินนัด' : 'OAS-'+p.oas}
                    </span><br>
                    <small class="text-muted mt-2 d-block">(เข้าสู่ระบบเพื่อดูรายละเอียด)</small>
                </div>`;
            @endauth

            L.marker([p.lat, p.lng], {
                    icon: customIcon
                })
                .addTo(map)
                .bindPopup(popupHtml, {
                    maxWidth: 200
                });
        });

        // ------ CSS Pulse Animations for map markers ------
        (function() {
            var style = document.createElement('style');
            style.textContent = `
                @keyframes pulse-red-m { 0%{box-shadow:0 0 0 0 rgba(255,77,77,.7)} 70%{box-shadow:0 0 0 8px rgba(255,77,77,0)} 100%{box-shadow:0 0 0 0 rgba(255,77,77,0)} }
                @keyframes pulse-purple-m { 0%{box-shadow:0 0 0 0 rgba(142,68,173,.7)} 70%{box-shadow:0 0 0 8px rgba(142,68,173,0)} 100%{box-shadow:0 0 0 0 rgba(142,68,173,0)} }
                .pulse-marker-red { animation: pulse-red-m 2s infinite; }
                .pulse-marker-purple { animation: pulse-purple-m 2s infinite; }
            `;
            document.head.appendChild(style);
        })();
    </script>
@endpush
