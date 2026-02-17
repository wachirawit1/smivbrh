@extends('layout')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-11 col-xl-10">
            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm">
                    <i class="fas fa-chevron-left"></i> กลับหน้ารายการ
                </a>
                <h3 class="fw-bold text-primary m-0"><i class="fas fa-id-card-alt me-2"></i>โปรไฟล์ผู้ป่วย SMI-V</h3>
            </div>

            <div class="row g-4">
                <!-- Sidebar: Profile Stats -->
                <div class="col-lg-4">
                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                        <div class="card-body p-4 text-center">
                            <div class="bg-primary text-white rounded-circle d-flex align-items-center justify-content-center mx-auto mb-3 shadow"
                                style="width: 100px; height: 100px; font-size: 2.5rem; font-weight: bold;">
                                {{ mb_substr($patient->first_name, 0, 1) }}
                            </div>
                            <h4 class="fw-bold mb-1">{{ $patient->first_name }} {{ $patient->last_name }}</h4>
                            <p class="text-muted mb-3">CID: {{ $patient->cid }}</p>

                            @php
                                $oasBadgeClass = match ($patient->oas_score) {
                                    '3' => 'bg-danger',
                                    '2' => 'bg-warning text-dark',
                                    '1' => 'bg-info text-dark',
                                    '0' => 'bg-success',
                                    default => 'bg-secondary',
                                };
                                $oasLabel = match ($patient->oas_score) {
                                    '3' => 'ฉุกเฉิน (OAS-3)',
                                    '2' => 'เร่งด่วน (OAS-2)',
                                    '1' => 'เฝ้าระวัง (OAS-1)',
                                    '0' => 'ปกติ (OAS-0)',
                                    default => 'ไม่ระบุ',
                                };
                            @endphp
                            <span class="badge {{ $oasBadgeClass }} fs-6 py-2 px-3 rounded-pill mb-4 w-100">
                                ระดับความรุนแรง: {{ $oasLabel }}
                            </span>

                            <div class="list-group list-group-flush text-start small">
                                <div class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-muted"><i class="fas fa-birthday-cake me-2"></i>อายุ/เพศ</span>
                                    <span class="fw-bold">{{ $patient->age }} ปี / {{ $patient->gender }}</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-muted"><i class="fas fa-phone-alt me-2"></i>เบอร์โทร</span>
                                    <span class="fw-bold">{{ $patient->phone ?: '-' }}</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-muted"><i class="fas fa-hospital me-2"></i>พื้นที่ (Area)</span>
                                    <span class="fw-bold">{{ $patient->area }}</span>
                                </div>
                                <div class="list-group-item d-flex justify-content-between px-0">
                                    <span class="text-muted"><i class="fas fa-map-marker-alt me-2"></i>ที่อยู่</span>
                                    <span class="fw-bold text-end">{{ $patient->address }} {{ $patient->tambon }}
                                        {{ $patient->amphoe }}</span>
                                </div>
                            </div>

                            <div class="mt-4">
                                <a href="{{ route('patients.edit', $patient) }}"
                                    class="btn btn-outline-primary btn-sm w-100 rounded-pill mb-2">
                                    <i class="fas fa-edit me-1"></i> แก้ไขข้อมูลพื้นฐาน
                                </a>
                                <a href="{{ route('follow-ups.create', $patient) }}"
                                    class="btn btn-success btn-sm w-100 rounded-pill fw-bold">
                                    <i class="fas fa-plus me-1"></i> เพิ่มการบันทึกติดตาม
                                </a>
                            </div>
                        </div>
                    </div>

                    <div
                        class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4 border-start border-primary border-5">
                        <div class="card-body p-4">
                            <h6 class="fw-bold text-primary mb-3"><i
                                    class="fas fa-stethoscope me-2"></i>ข้อมูลทางคลินิกล่าสุด</h6>
                            <div class="mb-3">
                                <label class="small text-muted d-block">วินิจฉัย (ICD-10)</label>
                                <span class="fw-bold">{{ $patient->diagnosis ?: 'ไม่มีข้อมูล' }}</span>
                            </div>
                            <div class="mb-3">
                                <label class="small text-muted d-block">กลุ่ม SMI-V</label>
                                <div class="mt-1">
                                    @forelse((array)($patient->smiv_group ?? []) as $group)
                                        <span
                                            class="badge bg-danger-subtle text-danger border border-danger fw-normal mb-1 me-1 text-wrap text-start d-block">
                                            {{ \App\Constants\SMIConstants::SMIV_TYPES[$group] ?? $group }}
                                        </span>
                                    @empty
                                        <span class="text-muted">ไม่ระบุ</span>
                                    @endforelse
                                </div>
                            </div>
                            <div class="mb-0">
                                <label class="small text-muted d-block">สารเสพติดที่ใช้</label>
                                @forelse((array)($patient->substances ?? []) as $sub)
                                    <span class="badge bg-light text-dark border me-1">{{ $sub }}</span>
                                @empty
                                    <span class="text-muted">ไม่ระบุ</span>
                                @endforelse
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Content Area: Clinical Score & History -->
                <div class="col-lg-8">
                    <!-- Evaluation Radar-like bars -->
                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                        <div class="card-header bg-white py-3 border-0">
                            <h5 class="m-0 fw-bold text-dark"><i class="fas fa-chart-line me-2"></i> สรุปผลการประเมิน 6 ด้าน
                                (ล่าสุด)</h5>
                        </div>
                        <div class="card-body p-4 pt-0">
                            <div class="row g-4">
                                @foreach ([['icon' => 'brain', 'label' => 'อาการทางจิต', 'key' => 'symp_mind', 'color' => 'primary'], ['icon' => 'pills', 'label' => 'การรับประทานยา', 'key' => 'symp_med', 'color' => 'success'], ['icon' => 'walking', 'label' => 'กิจวัตรประจำวัน', 'key' => 'symp_care', 'color' => 'info'], ['icon' => 'briefcase', 'label' => 'การทำงาน/เรียน', 'key' => 'symp_job', 'color' => 'warning'], ['icon' => 'users', 'label' => 'สัมพันธภาพ', 'key' => 'symp_env', 'color' => 'secondary'], ['icon' => 'capsules', 'label' => 'ไม่ใช้สารเสพติด', 'key' => 'symp_drug', 'color' => 'danger']] as $eval)
                                    @php
                                        $score = $patient->{$eval['key']} ?? 0;
                                        $percent = $score * 20;
                                    @endphp
                                    <div class="col-md-6">
                                        <div class="d-flex justify-content-between mb-1 align-items-center">
                                            <span class="small fw-bold"><i
                                                    class="fas fa-{{ $eval['icon'] }} me-2 text-{{ $eval['color'] }}"></i>{{ $eval['label'] }}</span>
                                            <span
                                                class="badge bg-{{ $eval['color'] }}-subtle text-{{ $eval['color'] }}">{{ $score }}/5</span>
                                        </div>
                                        <div class="progress"
                                            style="height: 10px; border-radius: 5px; background-color: #eee;">
                                            <div class="progress-bar bg-{{ $eval['color'] }}" role="progressbar"
                                                style="width: {{ $percent }}%" aria-valuenow="{{ $score }}"
                                                aria-valuemin="0" aria-valuemax="5"></div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Last Visit & Next Appt -->
                    <div class="row g-3 mb-4">
                        <div class="col-md-6">
                            <div class="card shadow-sm border-0 rounded-4 bg-light border-start border-warning border-5">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-warning text-dark rounded p-2"><i
                                                class="fas fa-calendar-check fa-lg"></i></div>
                                        <div>
                                            <label class="small text-muted d-block">พบบริการล่าสุด</label>
                                            <span
                                                class="fw-bold">{{ $patient->last_visit_date ? $patient->last_visit_date->format('d/m/Y') : '-' }}</span>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="card shadow-sm border-0 rounded-4 bg-light border-start border-danger border-5">
                                <div class="card-body p-3">
                                    <div class="d-flex align-items-center gap-3">
                                        <div class="bg-danger text-white rounded p-2"><i
                                                class="fas fa-calendar-alt fa-lg"></i></div>
                                        <div>
                                            <label class="small text-muted d-block">นัดหมายครั้งถัดไป</label>
                                            <span
                                                class="fw-bold text-danger">{{ $patient->next_appointment_date ? $patient->next_appointment_date->format('d/m/Y') : '-' }}</span>
                                            @if ($patient->status == 'เกินกำหนดนัด')
                                                <span class="badge bg-danger ms-2">เลยนัด!</span>
                                            @endif
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Follow-up History -->
                    <div class="card shadow-sm border-0 rounded-4 overflow-hidden mb-4">
                        <div class="card-header bg-white py-3 border-0 d-flex justify-content-between align-items-center">
                            <h5 class="m-0 fw-bold"><i class="fas fa-history me-2"></i>ประวัติการติดตาม (History)</h5>
                            <span class="badge bg-secondary rounded-pill">{{ count($history) }} ครั้ง</span>
                        </div>
                        <div class="card-body p-0">
                            @forelse($history as $item)
                                <div class="p-3 border-bottom hover-bg-light">
                                    <div class="d-flex justify-content-between align-items-start mb-2">
                                        <div>
                                            <span
                                                class="fw-bold text-primary">{{ $item->visit_date->format('d/m/Y') }}</span>
                                            @php
                                                $vBadge = match ($item['visit_status'] ?? '') {
                                                    'มาตามนัด' => 'bg-success',
                                                    'มาก่อนนัด' => 'bg-info',
                                                    'มาหลังนัด' => 'bg-warning text-dark',
                                                    default => 'bg-light text-dark border',
                                                };
                                            @endphp
                                            <span
                                                class="badge {{ $vBadge }} ms-2 fw-normal">{{ $item->visit_status }}</span>
                                        </div>
                                        <span
                                            class="badge bg-{{ $item->oas_score == '3' ? 'danger' : ($item->oas_score == '2' ? 'warning text-dark' : ($item->oas_score == '1' ? 'info text-dark' : 'success')) }} rounded-pill">OAS-{{ $item->oas_score }}</span>
                                    </div>
                                    <div class="row small g-2">
                                        <div class="col-md-4"><i class="fas fa-user-md me-1 text-muted"></i> ผู้บันทึก:
                                            {{ $item->staff_name }}</div>
                                        <div class="col-md-8 text-truncate text-muted"><i
                                                class="fas fa-comment-dots me-1"></i> {{ $item->details ?: '-' }}</div>
                                        <div class="col-12">
                                            <div class="d-flex gap-2 mt-1">
                                                @foreach (['symp_mind', 'symp_med', 'symp_care', 'symp_job', 'symp_env', 'symp_drug'] as $sk)
                                                    <div class="rounded-pill px-2 border bg-light text-muted"
                                                        style="font-size: 0.7rem;">S{{ $loop->iteration }}:
                                                        {{ $item->{$sk} }}</div>
                                                @endforeach
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            @empty
                                <div class="p-5 text-center text-muted">
                                    <i class="fas fa-folder-open fa-3x mb-3 text-light"></i>
                                    <p>ยังไม่มีประวัติการติดตาม</p>
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        .hover-bg-light:hover {
            background-color: #fcfcfc;
        }
    </style>
@endsection
