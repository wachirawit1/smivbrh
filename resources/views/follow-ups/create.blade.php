@extends('layout')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-11 col-xl-10">
            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('patients.show', $patient) }}"
                    class="btn btn-outline-secondary rounded-pill px-3 shadow-sm">
                    <i class="fas fa-chevron-left"></i> กลับไปที่โปรไฟล์
                </a>
                <h3 class="fw-bold text-success m-0"><i class="fas fa-file-medical me-2"></i>บันทึกการติดตามผู้ป่วย
                    (Follow-up)</h3>
            </div>

            <div class="card shadow-sm border-0 mb-4 bg-success text-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white text-success rounded-circle d-flex align-items-center justify-content-center"
                            style="width: 50px; height: 50px; font-weight: bold; font-size: 1.2rem;">
                            {{ mb_substr($patient->first_name, 0, 1) }}
                        </div>
                        <div>
                            <h5 class="m-0 fw-bold">{{ $patient->first_name }} {{ $patient->last_name }}</h5>
                            <p class="m-0 small">CID: {{ $patient->cid }} | อายุ: {{ $patient->age }} ปี</p>
                        </div>
                    </div>
                </div>
            </div>

            <form action="{{ route('follow-ups.store', $patient) }}" method="POST">
                @csrf

                <!-- Section 1: ข้อมูลการติดตาม -->
                <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                    <div class="card-header bg-success text-white py-3">
                        <h5 class="m-0 fw-bold"><i class="fas fa-calendar-check me-2"></i>ข้อมูลการติดตามในวันนี้</h5>
                    </div>
                    <div class="card-body p-4 bg-light-subtle">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">วันที่มารับพยาบาล</label>
                                <div class="input-group">
                                    <select name="visit_day" id="visit_day"
                                        class="form-select border-success-subtle shadow-sm"></select>
                                    <select name="visit_month" id="visit_month"
                                        class="form-select border-success-subtle shadow-sm"></select>
                                    <select name="visit_year" id="visit_year"
                                        class="form-select border-success-subtle shadow-sm"></select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">สถานะการมา</label>
                                <select name="visit_status" class="form-select border-success-subtle shadow-sm">
                                    <option value="มาตามนัด">มาตามนัด</option>
                                    <option value="มาก่อนนัด">มาก่อนนัด</option>
                                    <option value="มาหลังนัด">มาหลังนัด</option>
                                    <option value="ไม่ได้นัด">ไม่ได้นัด</option>
                                </select>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">เหตุผล (กรณีไม่มาตามนัด)</label>
                                <input type="text" name="visit_reason"
                                    class="form-control border-success-subtle shadow-sm" placeholder="ระบุเหตุผล...">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: ข้อมูลทางคลินิก ( Clinical State) -->
                <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                    <div class="card-header bg-danger text-white py-3">
                        <h5 class="m-0 fw-bold"><i class="fas fa-stethoscope me-2"></i>ข้อมูลทางคลินิกและการประเมิน
                            (Clinical Assessment)</h5>
                    </div>
                    <div class="card-body p-4 bg-light-subtle">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">วินิจฉัยปัจจุบัน (ICD-10)</label>
                                <select name="diagnosis" class="form-select border-danger-subtle shadow-sm">
                                    <option value="">-- เลือก ICD-10 --</option>
                                    @foreach ($icd10 as $icd)
                                        <option value="{{ $icd }}"
                                            {{ $patient->diagnosis == $icd ? 'selected' : '' }}>{{ $icd }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">กลุ่มผู้ป่วย SMI-V (ปรับปรุงถ้ามีการเปลี่ยนแปลง)</label>
                                <div class="bg-white p-3 rounded border border-danger-subtle">
                                    @php $currentGroups = $patient->smiv_group ?? []; @endphp
                                    @foreach ($smiv_types as $key => $label)
                                        <div class="form-check mb-2">
                                            <input class="form-check-input" type="checkbox" name="smiv_group[]"
                                                value="{{ $key }}" id="smiv_{{ $loop->index }}"
                                                {{ in_array($key, (array) $currentGroups) ? 'checked' : '' }}>
                                            <label class="form-check-label"
                                                for="smiv_{{ $loop->index }}">{{ $label }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">ระดับความรุนแรง OAS (ครั้งนี้)</label>
                                <div class="row g-3">
                                    <div class="col-md-3">
                                        <select name="oas_score"
                                            class="form-select border-danger-subtle shadow-sm fw-bold text-primary"
                                            id="oas_selector">
                                            <option value="0" {{ $patient->oas_score == '0' ? 'selected' : '' }}>🟢
                                                OAS-0 ปกติ</option>
                                            <option value="1" {{ $patient->oas_score == '1' ? 'selected' : '' }}>🟡
                                                OAS-1 เฝ้าระวัง</option>
                                            <option value="2" {{ $patient->oas_score == '2' ? 'selected' : '' }}>🟠
                                                OAS-2 เร่งด่วน</option>
                                            <option value="3" {{ $patient->oas_score == '3' ? 'selected' : '' }}>🔴
                                                OAS-3 ฉุกเฉิน</option>
                                        </select>
                                    </div>
                                    <div class="col-md-9">
                                        <div id="oas_details"
                                            class="small text-muted bg-white p-2 rounded border border-secondary-subtle"
                                            style="min-height: 40px;">
                                            (เลือกเพื่อดูรายละเอียดอาการ)
                                        </div>
                                    </div>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold text-primary">การประเมินความก้าวหน้า (Score 1-5)</label>
                                <div class="table-responsive bg-white rounded border border-primary-subtle shadow-sm">
                                    <table class="table table-sm table-bordered m-0 text-center align-middle">
                                        <thead class="table-light">
                                            <tr>
                                                <th>หัวข้อการประเมิน</th>
                                                <th style="width: 15%">(5) ปกติ</th>
                                                <th style="width: 15%">(4) ดี</th>
                                                <th style="width: 15%">(3) ปานกลาง</th>
                                                <th style="width: 15%">(2) น้อย</th>
                                                <th style="width: 15%">(1) บกพร่องมาก</th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            @foreach ([['name' => 'symp_mind', 'label' => '1. อาการทางจิต (หลงผิด/หูแว่ว)'], ['name' => 'symp_med', 'label' => '2. การรับประทานยา'], ['name' => 'symp_care', 'label' => '3. การดูแลกิจวัตรประจำวัน'], ['name' => 'symp_job', 'label' => '4. การทำงาน/การเรียน'], ['name' => 'symp_env', 'label' => '5. สัมพันธภาพกับผู้อื่น'], ['name' => 'symp_drug', 'label' => '6. การใช้สารเสพติด']] as $s)
                                                <tr>
                                                    <td class="text-start ps-3 fw-bold">{{ $s['label'] }}</td>
                                                    @for ($i = 5; $i >= 1; $i--)
                                                        <td><input class="form-check-input" type="radio"
                                                                name="{{ $s['name'] }}" value="{{ $i }}"
                                                                {{ $patient->{$s['name']} == $i ? 'checked' : '' }}
                                                                required></td>
                                                    @endfor
                                                </tr>
                                            @endforeach
                                        </tbody>
                                    </table>
                                </div>
                            </div>

                            <div class="col-md-12">
                                <label class="form-label fw-bold">การใช้สารเสพติด (ครั้งนี้)</label>
                                <div class="d-flex flex-wrap gap-3 bg-white p-3 rounded border border-secondary-subtle">
                                    @php $currentSubs = $patient->substances ?? []; @endphp
                                    @foreach ($substances as $sub)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="substances[]"
                                                value="{{ $sub }}" id="sub_{{ $loop->index }}"
                                                {{ in_array($sub, (array) $currentSubs) ? 'checked' : '' }}>
                                            <label class="form-check-label"
                                                for="sub_{{ $loop->index }}">{{ $sub }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: แผนการรักษาและนัดหมาย -->
                <div class="card shadow-sm border-0 mb-5 overflow-hidden">
                    <div class="card-header bg-warning text-dark py-3">
                        <h5 class="m-0 fw-bold"><i class="fas fa-calendar-alt me-2"></i>แผนการรักษาและการนัดหมาย (Plan &
                            Next Appt)</h5>
                    </div>
                    <div class="card-body p-4 bg-light-subtle">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">แผนการดูแลต่อเนื่อง</label>
                                <div class="d-flex gap-4">
                                    @foreach (['นัดต่อเนื่อง', 'Refer (ส่งต่อ)', 'จำหน่าย (D/C)', 'อื่นๆ'] as $plan)
                                        <div class="form-check">
                                            <input class="form-check-input" type="radio" name="appointment_plan"
                                                value="{{ $plan }}" id="plan_{{ $loop->index }}"
                                                {{ $plan == 'นัดต่อเนื่อง' ? 'checked' : '' }}>
                                            <label class="form-check-label"
                                                for="plan_{{ $loop->index }}">{{ $plan }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-danger">วันที่นัดครั้งถัดไป (Appointment
                                    Date)</label>
                                <div class="input-group">
                                    <select name="next_day" id="next_day"
                                        class="form-select border-danger shadow-sm"></select>
                                    <select name="next_month" id="next_month"
                                        class="form-select border-danger shadow-sm"></select>
                                    <select name="next_year" id="next_year"
                                        class="form-select border-danger shadow-sm"></select>
                                </div>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold">รายละเอียด/หมายเหตุการติดตาม</label>
                                <textarea name="details" class="form-control border-secondary-subtle shadow-sm" rows="3"
                                    placeholder="ระบุความคืบหน้า อาการเด่น หรือการเปลี่ยนแปลงยา..."></textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-5 text-center">
                    <button type="submit" class="btn btn-success btn-lg px-5 py-3 rounded-pill shadow-lg fw-bold">
                        <i class="fas fa-save me-2"></i> บันทึกข้อมูลการติดตาม
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Function to populate date dropdowns
            function populateDateDropdowns(dId, mId, yId, setToday = false) {
                const daySelect = document.getElementById(dId);
                const monthSelect = document.getElementById(mId);
                const yearSelect = document.getElementById(yId);

                // Days 1-31
                for (let i = 1; i <= 31; i++) {
                    let opt = document.createElement('option');
                    opt.value = i;
                    opt.textContent = i;
                    daySelect.appendChild(opt);
                }

                // Months
                const months = [
                    "มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน",
                    "กรกฎาคม", "สิงหาคม", "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
                ];
                months.forEach((m, index) => {
                    let opt = document.createElement('option');
                    opt.value = index + 1;
                    opt.textContent = m;
                    monthSelect.appendChild(opt);
                });

                // Years (Current year +/- 5)
                const currentYear = new Date().getFullYear();
                const thaiYear = currentYear + 543;
                for (let i = thaiYear - 5; i <= thaiYear + 5; i++) {
                    let opt = document.createElement('option');
                    opt.value = i;
                    opt.textContent = i;
                    if (i === thaiYear) opt.selected = true;
                    yearSelect.appendChild(opt);
                }

                // Set Today if requested
                if (setToday) {
                    const today = new Date();
                    daySelect.value = today.getDate();
                    monthSelect.value = today.getMonth() + 1;
                }
            }

            // Execute population
            populateDateDropdowns('visit_day', 'visit_month', 'visit_year', true);
            populateDateDropdowns('next_day', 'next_month', 'next_year', false);

            // OAS Logic with passed options
            const oasOptions = @json($oas_options);
            const oasSelector = document.getElementById('oas_selector');
            const oasDetailsDiv = document.getElementById('oas_details');

            function updateOasDetails() {
                const score = oasSelector.value;
                const details = oasOptions[score];
                if (details && Array.isArray(details)) {
                    let html = '<ul class="mb-0 ps-3 small">';
                    details.forEach(detail => {
                        html += `<li>${detail}</li>`;
                    });
                    html += '</ul>';
                    oasDetailsDiv.innerHTML = html;
                } else {
                    oasDetailsDiv.innerHTML = '<span class="text-muted small">(เลือกเพื่อดูรายละเอียดอาการ)</span>';
                }
            }

            if (oasSelector) {
                oasSelector.addEventListener('change', updateOasDetails);
                // Trigger once on load
                updateOasDetails();
            }
        });
    </script>

    <style>
        .bg-light-subtle {
            background-color: #f8f9fa;
        }

        .card-header {
            font-size: 1.1rem;
        }

        .form-label {
            color: #495057;
        }
    </style>
@endsection
