@extends('layout')

@section('content')
    <h3 class="fw-bold text-primary mb-4">Dashboard วิเคราะห์ข้อมูลผู้ป่วยระดับจังหวัด</h3>

    <div class="card-3d p-4 mb-4">
        <form action="{{ route('dashboard') }}" method="GET">
            <div class="row g-3 align-items-end">
                <div class="col-md-3">
                    <label class="form-label fw-bold"><i class="fas fa-map-marker-alt me-1"></i>พื้นที่ (อำเภอ)</label>
                    <select name="area" class="form-select border-primary-subtle" onchange="this.form.submit()">
                        <option value="">-- ทั้งหมดทุกอำเภอ --</option>
                        @foreach ($areas as $areaName)
                            <option value="{{ $areaName }}" {{ ($areaFilter ?? '') == $areaName ? 'selected' : '' }}>
                                {{ $areaName }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold"><i class="fas fa-layer-group me-1"></i>กลุ่ม SMI-V</label>
                    <select name="smiv_group" class="form-select border-primary-subtle" onchange="this.form.submit()">
                        <option value="">-- ทุกกลุ่ม SMI-V --</option>
                        @foreach ($smivNames as $key => $name)
                            <option value="{{ $key }}" {{ ($smivFilter ?? '') == $key ? 'selected' : '' }}>
                                {{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3">
                    <label class="form-label fw-bold"><i class="fas fa-exclamation-triangle me-1"></i>OAS Score
                        (ความรุนแรง)</label>
                    <select name="oas_score" class="form-select border-primary-subtle" onchange="this.form.submit()">
                        <option value="">-- ทุกระดับความรุนแรง --</option>
                        @foreach ($oasNames as $key => $name)
                            @if ($key !== 'purple')
                                <option value="{{ $key }}" {{ ($oasFilter ?? '') == $key ? 'selected' : '' }}>
                                    {{ $name }}</option>
                            @endif
                        @endforeach
                    </select>
                </div>
                <div class="col-md-3 text-end d-flex align-items-center justify-content-end gap-2">
                    <a href="{{ route('dashboard') }}" class="btn btn-outline-secondary btn-sm rounded-pill shadow-sm"><i
                            class="fas fa-undo"></i> ล้างค่า</a>
                    <button type="submit" class="btn btn-primary btn-sm rounded-pill px-3 shadow-sm">กรองข้อมูล</button>
                </div>
            </div>
        </form>
    </div>
    @if (isset($oasFilter) || isset($areaFilter) || isset($smivFilter))
        <div class="alert alert-info py-2 px-3 mb-4 rounded-pill d-inline-block shadow-sm">
            <small class="fw-bold"><i class="fas fa-filter me-1"></i>คัดกรอง: </small>
            <!-- Badges removed temporarily to debug syntax error -->
            @if ($areaFilter)
                <span class="bg-primary rounded-pill p-1 text-white">{{ $areaFilter }}</span>
            @endif
        </div>
    @endif

    <div class="row g-4 mb-4">
        <div class="col-md-3">
            <div class="card-3d p-4 text-center border-bottom border-primary border-5" style="height: 100%;">
                <h5 class="text-muted">ผู้ป่วยรวม</h5>
                <h2 class="fw-bold text-primary">
                    {{ array_sum(array_filter($stats, fn($k) => in_array($k, ['0', '1', '2', '3']), ARRAY_FILTER_USE_KEY)) }}
                </h2>
                <small>ราย</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-3d p-4 text-center border-bottom border-success border-5" style="height: 100%;">
                <h5 class="text-success fw-bold">มาตามนัด</h5>
                <h2 class="fw-bold text-success">{{ $stats['scheduled'] ?? 0 }}</h2>
                <small>ราย</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-3d p-4 text-center border-bottom border-purple border-5" style="height: 100%;"
                id="purple-card">
                <h5 class="fw-bold" style="color: #6f42c1;">ไม่มา/เกินนัด</h5>
                <h2 class="fw-bold" style="color: #6f42c1;">{{ $stats['purple'] ?? 0 }}</h2>
                <small>ราย</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="card-3d p-4 text-center border-bottom border-info border-5" style="height: 100%;">
                <h6 class="text-muted mb-2">อาการดีขึ้น (Dashboard สรุป)</h6>
                <div class="d-flex justify-content-around mt-2">
                    <div><small class="text-muted">3 เดือน</small>
                        <h5 class="fw-bold text-info">{{ $improvement3m ?? 0 }}%</h5>
                    </div>
                    <div><small class="text-muted">6 เดือน</small>
                        <h5 class="fw-bold text-info">{{ $improvement6m ?? 0 }}%</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="row g-4 mb-4">
        <div class="col-md-6">
            <div class="card-3d p-4" style="height: 100%;">
                <h6 class="fw-bold text-center mb-4 text-primary"><i class="fas fa-chart-bar me-2"></i>สัดส่วนประเภทผู้ป่วย
                    SMI-V</h6>
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="chartSmiv"></canvas>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="card-3d p-4" style="height: 100%;">
                <h6 class="fw-bold text-center mb-4 text-primary"><i class="fas fa-chart-pie me-2"></i>ระดับความรุนแรง OAS
                    Score</h6>
                <div style="position: relative; height: 300px; width: 100%;">
                    <canvas id="chartOas"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Map Section (Full Width) -->
    <div class="row mb-5">
        <div class="col-12">
            <div class="card-3d p-4">
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <h5 class="fw-bold text-primary m-0"><i class="fas fa-map-marked-alt me-2"></i>แผนที่เฝ้าระวังจุดเสี่ยง
                        (Buriram Risk Map)</h5>
                    <div class="d-flex gap-2 small">
                        <span class="badge bg-danger">OAS-3</span>
                        <span class="badge bg-warning text-dark">OAS-2</span>
                        <span class="badge bg-info text-dark">OAS-1</span>
                        <span class="badge bg-success">OAS-0</span>
                        <span class="badge bg-purple">เกินนัด</span>
                    </div>
                </div>
                <div id="map" style="height: 500px; border-radius: 12px; z-index: 1;"></div>
            </div>
        </div>
    </div>
@endsection

@push('scripts')
    <!-- Data for JS Map -->
    @php
        $patientPoints = [];
        $districtCoords = [
            'เมืองบุรีรัมย์' => [14.993, 103.102],
            'นางรอง' => [14.629, 102.791],
            'ประโคนชัย' => [14.605, 103.125],
            'สตึก' => [15.297, 103.292],
            'ลำปลายมาศ' => [15.023, 102.834],
            'ละหานทราย' => [14.412, 102.966],
            'บ้านกรวด' => [14.419, 103.104],
            'พุทไธสง' => [15.532, 103.003],
            'พลับพลาชัย' => [14.739, 103.167],
            'ห้วยราช' => [14.96, 103.197],
            'กระสัง' => [14.919, 103.256],
            'คูเมือง' => [15.289, 103.016],
            'แคนดง' => [15.302, 103.107],
            'นาโพธิ์' => [15.667264, 102.929283]
        ];

        // Generate map points from actual patient data
        $patients = \App\Models\Patient::select('id', 'first_name', 'last_name', 'amphoe', 'oas_score', 'status')
            ->when($areaFilter, fn($q) => $q->where('amphoe', $areaFilter))
            ->when($smivFilter, fn($q) => $q->whereJsonContains('smiv_group', $smivFilter))
            ->when($oasFilter, fn($q) => $q->where('oas_score', $oasFilter))
            ->get();

        foreach ($patients as $patient) {
            $areaName = $patient->amphoe ?: 'เมืองบุรีรัมย์';
            $baseCoord = $districtCoords[$areaName] ?? [14.99, 103.1];

            $patientPoints[] = [
                'area' => $areaName,
                'oas' => $patient->oas_score ?? '0',
                'lat' => $baseCoord[0] + mt_rand(-30, 30) / 1000,
                'lng' => $baseCoord[1] + mt_rand(-30, 30) / 1000,
                'url' => route('patients.show', $patient),
                'name' => $patient->first_name . ' ' . $patient->last_name,
            ];
        }
    @endphp

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        // 1. SMI-V Bar Chart
        const ctxSmiv = document.getElementById('chartSmiv').getContext('2d');
        new Chart(ctxSmiv, {
            type: 'bar',
            data: {
                labels: @json(array_keys($smivStats)),
                datasets: [{
                    label: 'จำนวนผู้ป่วย (ราย)',
                    data: @json(array_values($smivStats)),
                    backgroundColor: ['#3b82f6', '#10b981', '#f59e0b', '#ef4444', '#8b5cf6', '#6366f1',
                        '#ec4899'
                    ],
                    borderRadius: 8,
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        grid: {
                            color: 'rgba(0,0,0,0.05)'
                        }
                    },
                    x: {
                        grid: {
                            display: false
                        }
                    }
                }
            }
        });

        // 2. OAS Doughnut Chart
        const ctxOas = document.getElementById('chartOas').getContext('2d');
        new Chart(ctxOas, {
            type: 'doughnut',
            data: {
                labels: ['OAS-0 (ปกติ)', 'OAS-1 (เฝ้าระวัง)', 'OAS-2 (เร่งด่วน)', 'OAS-3 (ฉุกเฉิน)', 'เกินนัด'],
                datasets: [{
                    data: [
                        {{ $stats['0'] ?? 0 }},
                        {{ $stats['1'] ?? 0 }},
                        {{ $stats['2'] ?? 0 }},
                        {{ $stats['3'] ?? 0 }},
                        {{ $stats['purple'] ?? 0 }}
                    ],
                    backgroundColor: ['#10b981', '#06b6d4', '#fbbf24', '#ef4444', '#8b5cf6'],
                    borderWidth: 0,
                    hoverOffset: 20
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                cutout: '70%',
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            usePointStyle: true,
                            padding: 15,
                            font: {
                                family: 'Prompt',
                                size: 11
                            }
                        }
                    }
                }
            }
        });

        // Map
        var map = L.map('map').setView([14.993, 103.102], 9);
        L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png').addTo(map);

        var patients = @json($patientPoints);
        var colors = {
            '0': '#4caf50',
            '1': '#2196f3',
            '2': '#ff9800',
            '3': '#ff4d4d',
            'purple': '#8e44ad'
        };
        var labels = {
            '0': 'ปกติ (OAS-0)',
            '1': 'เฝ้าระวัง (OAS-1)',
            '2': 'เร่งด่วน (OAS-2)',
            '3': 'ฉุกเฉิน (OAS-3)',
        };

        patients.forEach(function(p) {
            var severityKey = p.oas;
            var pulseClass = (severityKey === '3') ? 'pulse-red' : '';
            var iconHtml =
                `<div style="background:${colors[severityKey] || '#999'}; width:14px; height:14px; border-radius:50%; border:2px solid white; box-shadow: 0 2px 4px rgba(0,0,0,0.3);" class="${pulseClass}"></div>`;
            var customIcon = L.divIcon({
                html: iconHtml,
                className: '',
                iconSize: [14, 14],
                iconAnchor: [7, 7]
            });
            L.marker([p.lat, p.lng], {
                    icon: customIcon
                })
                .addTo(map)
                .bindPopup(`
                <div class="text-center">
                    <b class="text-primary">${p.area}</b><br>
                    <span class="badge ${p.oas === '3' ? 'bg-danger' :
                                       (p.oas === '2' ? 'bg-warning text-dark' :
                                       (p.oas === '1' ? 'bg-info text-dark' :
                                       (p.oas === '0' ? 'bg-success' : 'bg-purple')))} mb-2">
                        ${labels[p.oas] || p.oas}
                    </span><br>
                    <a href="${p.url}" class="btn btn-sm btn-outline-primary rounded-pill px-3" style="font-size: 0.8rem;">
                        <i class="fas fa-users me-1"></i> ดูรายชื่อกลุ่มนี้
                    </a>
                </div>
            `);
        });
    </script>
@endpush
