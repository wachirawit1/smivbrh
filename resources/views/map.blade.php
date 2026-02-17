@extends('layout')

@section('content')
    <link rel="stylesheet" href="https://unpkg.com/leaflet@1.9.4/dist/leaflet.css" />
    <style>
        #map {
            height: 600px;
            border-radius: 20px;
            box-shadow: var(--shadow-3d);
            border: 4px solid white;
            z-index: 1;
            margin-bottom: 1.5rem;
        }

        .map-legend {
            background: white;
            padding: 1rem;
            border-radius: 15px;
            box-shadow: var(--shadow);
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 2rem;
            justify-content: center;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            font-size: 0.9rem;
        }

        .dot {
            width: 14px;
            height: 14px;
            border-radius: 50%;
            display: inline-block;
        }
    </style>

    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 2rem;">
        <h1><i class="fas fa-map-marked-alt"></i> แผนที่เฝ้าระวังจุดเสี่ยง (เตือนเชิงรุก)</h1>
        <div
            style="background: white; padding: 0.5rem 1rem; border-radius: 12px; box-shadow: var(--shadow-3d); font-size: 0.9rem;">
            <i class="fas fa-crosshairs text-primary"></i> แสดงจุดเฝ้าระวังตามระดับความรุนแรง
        </div>
    </div>

    <div class="map-legend">
        <div class="legend-item"><span class="dot" style="background: #28a745;"></span> OAS-0 ปกติ (เขียว)</div>
        <div class="legend-item"><span class="dot" style="background: #0dcaf0;"></span> OAS-1 เฝ้าระวัง (ฟ้า)</div>
        <div class="legend-item"><span class="dot" style="background: #ffc107;"></span> OAS-2 เร่งด่วน (เหลือง)</div>
        <div class="legend-item"><span class="dot pulse-red" style="background: #dc3545; border:2px solid white;"></span>
            OAS-3 ฉุกเฉิน (แดง)</div>
    </div>

    <div id="map"></div>

    <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
        @foreach ($areas as $area)
            @php
                $areaData = $statsByArea[$area] ?? collect();
                $total = $areaData->sum('total');
                if ($total == 0) {
                    continue;
                }
            @endphp
            <div class="card p-3 shadow-sm border-0 rounded-4">
                <h4 class="fw-bold text-primary mb-2">{{ $area }}</h4>
                <div class="d-flex justify-content-between align-items-center">
                    <span class="text-muted">รวม {{ $total }} ราย</span>
                    <a href="{{ route('patients.index', ['area' => $area]) }}"
                        class="btn btn-sm btn-primary rounded-pill px-3">
                        <i class="fas fa-list me-1"></i> ดูรายชื่อ
                    </a>
                </div>
            </div>
        @endforeach
    </div>

    <!-- Data for JS -->
    @php
        $patientPoints = [];
        $districtCoords = [
            'รพ.บุรีรัมย์' => [14.993, 103.102],
            'คลินิกรักษ์ใจ' => [14.995, 103.105],
            'รพ.กระสัง' => [14.919, 103.256],
            'รพ.คูเมือง' => [15.289, 103.016],
            'รพ.แคนดง' => [15.302, 103.107],
            'รพ.นางรอง' => [14.629, 102.791],
            'รพ.ประโคนชัย' => [14.605, 103.125],
            'รพ.สตึก' => [15.297, 103.292],
        ];

        foreach ($areas as $areaName) {
            $areaData = $statsByArea[$areaName] ?? collect();
            if ($areaData->isEmpty()) {
                continue;
            }

            $baseCoord = $districtCoords[$areaName] ?? [14.99, 103.1];

            foreach ($areaData as $stat) {
                for ($i = 0; $i < $stat->total; $i++) {
                    $patientPoints[] = [
                        'area' => $areaName,
                        'oas' => $stat->oas_score,
                        'lat' => $baseCoord[0] + mt_rand(-30, 30) / 1000,
                        'lng' => $baseCoord[1] + mt_rand(-30, 30) / 1000,
                    ];
                }
            }
        }
    @endphp

    <script src="https://unpkg.com/leaflet@1.9.4/dist/leaflet.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            var map = L.map('map').setView([14.993, 103.102], 9);

            L.tileLayer('https://{s}.basemaps.cartocdn.com/light_all/{z}/{x}/{y}{r}.png', {
                attribution: '© OpenStreetMap'
            }).addTo(map);

            var patients = @json($patientPoints);
            var colors = {
                '0': '#28a745',
                '1': '#0dcaf0',
                '2': '#ffc107',
                '3': '#dc3545',
            };

            var labels = {
                '0': 'ปกติ (OAS-0)',
                '1': 'เฝ้าระวัง (OAS-1)',
                '2': 'เร่งด่วน (OAS-2)',
                '3': 'ฉุกเฉิน (OAS-3)',
            };

            patients.forEach(function(p) {
                var oas = p.oas;
                var pulseClass = (oas === '3') ? 'pulse-red' : '';
                var iconHtml =
                    `<div style="background:${colors[oas] || '#999'}; width:16px; height:16px; border-radius:50%; border:2px solid white; box-shadow: 0 4px 8px rgba(0,0,0,0.3);" class="${pulseClass}"></div>`;

                var customIcon = L.divIcon({
                    html: iconHtml,
                    className: '',
                    iconSize: [16, 16],
                    iconAnchor: [8, 8]
                });

                L.marker([p.lat, p.lng], {
                        icon: customIcon
                    })
                    .addTo(map)
                    .bindPopup(`<b>พื้นที่: ${p.area}</b><br>ระดับความรุนแรง: ${labels[oas] || oas}`);
            });
        });
    </script>
@endsection
