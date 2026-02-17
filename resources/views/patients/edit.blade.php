@extends('layout')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-11 col-xl-10">
            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('patients.show', $patient) }}"
                    class="btn btn-outline-secondary rounded-pill px-3 shadow-sm">
                    <i class="fas fa-chevron-left"></i> กลับ
                </a>
                <h3 class="fw-bold text-primary m-0"><i class="fas fa-user-edit me-2"></i>แก้ไขข้อมูลผู้ป่วย SMI-V</h3>
            </div>

            <form action="{{ route('patients.update', $patient) }}" method="POST" id="patientForm">
                @csrf
                @method('PUT')

                <!-- Section 1: ข้อมูลทั่วไป -->
                <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="m-0 fw-bold"><i class="fas fa-id-card me-2"></i>ส่วนที่ 1: ข้อมูลทั่วไป (Personal
                            Information)</h5>
                    </div>
                    <div class="card-body p-4 bg-light-subtle">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label fw-bold">คำนำหน้า</label>
                                <select name="prefix" class="form-select border-primary-subtle shadow-sm">
                                    <option value="นาย" {{ $patient->prefix == 'นาย' ? 'selected' : '' }}>นาย</option>
                                    <option value="นาง" {{ $patient->prefix == 'นาง' ? 'selected' : '' }}>นาง</option>
                                    <option value="นางสาว" {{ $patient->prefix == 'นางสาว' ? 'selected' : '' }}>นางสาว
                                    </option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">ชื่อ (First Name)</label>
                                <input type="text" name="first_name" class="form-control border-primary-subtle shadow-sm"
                                    required value="{{ $patient->first_name }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">นามสกุล (Last Name)</label>
                                <input type="text" name="last_name" class="form-control border-primary-subtle shadow-sm"
                                    required value="{{ $patient->last_name }}">
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">เพศ</label>
                                <select name="gender" class="form-select border-primary-subtle shadow-sm">
                                    <option value="ชาย" {{ $patient->gender == 'ชาย' ? 'selected' : '' }}>ชาย</option>
                                    <option value="หญิง" {{ $patient->gender == 'หญิง' ? 'selected' : '' }}>หญิง</option>
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold text-danger">เลขประจำตัวประชาชน (CID) *</label>
                                <input type="text" name="cid" class="form-control border-danger-subtle shadow-sm"
                                    maxlength="13" required placeholder="เลข 13 หลัก" value="{{ $patient->cid }}">
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">วันเกิด</label>
                                <div class="input-group">
                                    <select name="birth_day" id="birth_day"
                                        class="form-select border-primary-subtle shadow-sm"></select>
                                    <select name="birth_month" id="birth_month"
                                        class="form-select border-primary-subtle shadow-sm"></select>
                                    <select name="birth_year" id="birth_year"
                                        class="form-select border-primary-subtle shadow-sm"
                                        onchange="calculateAge()"></select>
                                </div>
                            </div>
                            <div class="col-md-2">
                                <label class="form-label fw-bold">อายุ (ปี)</label>
                                <input type="number" name="age" id="age_display" class="form-control bg-light" readonly
                                    value="{{ $patient->age }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">เบอร์โทรศัพท์ติดต่อ</label>
                                <input type="text" name="phone" class="form-control border-primary-subtle shadow-sm"
                                    placeholder="08x-xxxxxxx" value="{{ $patient->phone }}">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: ที่อยู่และการขึ้นทะเบียน -->
                <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                    <div class="card-header bg-success text-white py-3">
                        <h5 class="m-0 fw-bold"><i class="fas fa-map-marker-alt me-2"></i>ส่วนที่ 2:
                            ที่อยู่และการขึ้นทะเบียน (Address & Registry)</h5>
                    </div>
                    <div class="card-body p-4 bg-light-subtle">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">ที่อยู่ปัจจุบัน (บ้านเลขที่/หมู่ที่)</label>
                                <input type="text" name="address" class="form-control border-success-subtle shadow-sm"
                                    value="{{ $patient->address }}">
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">สถานบริการ (Area)</label>
                                <select name="area" class="form-select border-success-subtle shadow-sm" required>
                                    @foreach ($areas as $area)
                                        <option value="{{ $area }}"
                                            {{ $patient->area == $area ? 'selected' : '' }}>{{ $area }}</option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">อำเภอ (Amphoe)</label>
                                <select name="amphoe" id="amphoe" class="form-select border-success-subtle shadow-sm"
                                    onchange="updateTambons()"></select>
                            </div>
                            <div class="col-md-4">
                                <label class="form-label fw-bold">ตำบล (Tambon)</label>
                                <select name="tambon" id="tambon"
                                    class="form-select border-success-subtle shadow-sm"></select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 3: ข้อมูลทางคลินิก (Clinical Information) -->
                <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                    <div class="card-header bg-danger text-white py-3">
                        <h5 class="m-0 fw-bold"><i class="fas fa-stethoscope me-2"></i>ส่วนที่ 3: ข้อมูลทางคลินิก
                            (Clinical Information)</h5>
                    </div>
                    <div class="card-body p-4 bg-light-subtle">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">วินิจฉัย (ICD-10)</label>
                                <select name="diagnosis" class="form-select border-danger-subtle shadow-sm">
                                    <option value="">-- เลือก ICD-10 --</option>
                                    @foreach (\App\Constants\SMIConstants::ICD10 as $icd)
                                        <option value="{{ $icd }}"
                                            {{ $patient->diagnosis == $icd ? 'selected' : '' }}>{{ $icd }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>
                            <div class="col-md-12">
                                <label class="form-label fw-bold text-danger">กลุ่มผู้ป่วย SMI-V (เลือกได้มากกว่า 1
                                    ข้อ)</label>
                                <div class="bg-white p-3 rounded border border-danger-subtle">
                                    @php $currentGroups = $patient->smiv_group ?? []; @endphp
                                    @foreach (\App\Constants\SMIConstants::SMIV_TYPES as $key => $label)
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
                                <label class="form-label fw-bold">ระดับความรุนแรง OAS (จุดคัดกรอง)</label>
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
                                <label class="form-label fw-bold">ผลการประเมิน 6 ด้าน (5-point Scale)</label>
                                <div class="table-responsive bg-white rounded border border-secondary-subtle">
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
                                <label class="form-label fw-bold">สารเสพติดที่ใช้ (Substance Abuse)</label>
                                <div class="d-flex flex-wrap gap-3 bg-white p-3 rounded border border-secondary-subtle">
                                    @php $currentSubstances = $patient->substances ?? []; @endphp
                                    @foreach (\App\Constants\SMIConstants::SUBSTANCES as $sub)
                                        <div class="form-check">
                                            <input class="form-check-input" type="checkbox" name="substances[]"
                                                value="{{ $sub }}" id="sub_{{ $loop->index }}"
                                                {{ in_array($sub, (array) $currentSubstances) ? 'checked' : '' }}>
                                            <label class="form-check-label"
                                                for="sub_{{ $loop->index }}">{{ $sub }}</label>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: แผนการนัดหมาย -->
                <div class="card shadow-sm border-0 mb-5 overflow-hidden">
                    <div class="card-header bg-warning text-dark py-3">
                        <h5 class="m-0 fw-bold"><i class="fas fa-calendar-check me-2"></i>ส่วนที่ 4: แผนการนัดหมาย
                            (Appointment Plan)</h5>
                    </div>
                    <div class="card-body p-4 bg-light-subtle">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">วันที่มารับพยาบาลครั้งล่าสุด</label>
                                <div class="input-group">
                                    <select name="visit_day" id="visit_day"
                                        class="form-select border-warning-subtle shadow-sm"></select>
                                    <select name="visit_month" id="visit_month"
                                        class="form-select border-warning-subtle shadow-sm"></select>
                                    <select name="visit_year" id="visit_year"
                                        class="form-select border-warning-subtle shadow-sm"></select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold text-danger">วันที่นัดหมายครั้งถัดไป</label>
                                <div class="input-group">
                                    <select name="next_day" id="next_day"
                                        class="form-select border-danger shadow-sm"></select>
                                    <select name="next_month" id="next_month"
                                        class="form-select border-danger shadow-sm"></select>
                                    <select name="next_year" id="next_year"
                                        class="form-select border-danger shadow-sm"></select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">สถานะติดตาม</label>
                                <select name="status" class="form-select border-warning-subtle shadow-sm">
                                    <option value="ติดตามปกติ" {{ $patient->status == 'ติดตามปกติ' ? 'selected' : '' }}>
                                        ติดตามปกติ</option>
                                    <option value="จำหน่าย" {{ $patient->status == 'จำหน่าย' ? 'selected' : '' }}>จำหน่าย
                                        (D/C)</option>
                                    <option value="เกินกำหนดนัด"
                                        {{ $patient->status == 'เกินกำหนดนัด' ? 'selected' : '' }}>เกินกำหนดนัด</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="mb-5 text-center">
                    <button type="submit" class="btn btn-primary btn-lg px-5 py-3 rounded-pill shadow-lg fw-bold">
                        <i class="fas fa-save me-2"></i> บันทึกการแก้ไข
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const amphurTambonPath = @json(\App\Constants\SMIConstants::AMPHUR_TAMBON);
        const oasOptions = @json(\App\Constants\SMIConstants::OAS_OPTIONS);

        function populateDateDropdowns(dId, mId, yId, dateVal) {
            const dSelect = document.getElementById(dId);
            const mSelect = document.getElementById(mId);
            const ySelect = document.getElementById(yId);

            if (!dSelect) return;

            const months = ["มกราคม", "กุมภาพันธ์", "มีนาคม", "เมษายน", "พฤษภาคม", "มิถุนายน", "กรกฎาคม", "สิงหาคม",
                "กันยายน", "ตุลาคม", "พฤศจิกายน", "ธันวาคม"
            ];

            for (let i = 1; i <= 31; i++) {
                let opt = document.createElement('option');
                opt.value = i;
                opt.innerHTML = i;
                dSelect.appendChild(opt);
            }

            months.forEach((m, idx) => {
                let opt = document.createElement('option');
                opt.value = idx + 1;
                opt.innerHTML = m;
                mSelect.appendChild(opt);
            });

            const curYear = new Date().getFullYear() + 543;
            for (let i = curYear + 1; i >= curYear - 100; i--) {
                let opt = document.createElement('option');
                opt.value = i;
                opt.innerHTML = i;
                ySelect.appendChild(opt);
            }

            if (dateVal) {
                const d = new Date(dateVal);
                dSelect.value = d.getDate();
                mSelect.value = d.getMonth() + 1;
                ySelect.value = d.getFullYear() + 543;
            }
        }

        function calculateAge() {
            const d = document.getElementById('birth_day').value;
            const m = document.getElementById('birth_month').value;
            const y = parseInt(document.getElementById('birth_year').value) - 543;

            if (!y) return;

            const birthDate = new Date(y, m - 1, d);
            const today = new Date();
            let age = today.getFullYear() - birthDate.getFullYear();
            const mDiff = today.getMonth() - birthDate.getMonth();
            if (mDiff < 0 || (mDiff === 0 && today.getDate() < birthDate.getDate())) {
                age--;
            }
            document.getElementById('age_display').value = age;
        }

        function updateTambons(selectedTambon = null) {
            const amphoe = document.getElementById('amphoe').value;
            const tambonSelect = document.getElementById('tambon');
            tambonSelect.innerHTML = '';

            if (amphurTambonPath[amphoe]) {
                amphurTambonPath[amphoe].forEach(t => {
                    let opt = document.createElement('option');
                    opt.value = t;
                    opt.innerHTML = t;
                    if (t === selectedTambon) opt.selected = true;
                    tambonSelect.appendChild(opt);
                });
            }
        }

        function initAmphoes(selectedAmphoe = null, selectedTambon = null) {
            const amphoeSelect = document.getElementById('amphoe');
            Object.keys(amphurTambonPath).sort().forEach(a => {
                let opt = document.createElement('option');
                opt.value = a;
                opt.innerHTML = a;
                if (a === selectedAmphoe) opt.selected = true;
                amphoeSelect.appendChild(opt);
            });
            updateTambons(selectedTambon);
        }

        document.addEventListener('DOMContentLoaded', () => {
            populateDateDropdowns('birth_day', 'birth_month', 'birth_year',
                '{{ $patient->birth_date ? $patient->birth_date->toDateString() : '' }}');
            populateDateDropdowns('visit_day', 'visit_month', 'visit_year',
                '{{ $patient->last_visit_date ? $patient->last_visit_date->toDateString() : '' }}');
            populateDateDropdowns('next_day', 'next_month', 'next_year',
                '{{ $patient->next_appointment_date ? $patient->next_appointment_date->toDateString() : '' }}');
            initAmphoes('{{ $patient->amphoe }}', '{{ $patient->tambon }}');

            // OAS Details init
            const oasSelector = document.getElementById('oas_selector');
            const updateOasDetails = () => {
                const score = oasSelector.value;
                const detailsDiv = document.getElementById('oas_details');
                if (oasOptions[score]) {
                    detailsDiv.innerHTML = '<ul class="m-0 pl-3"><li>' + oasOptions[score].join('</li><li>') +
                        '</li></ul>';
                } else {
                    detailsDiv.innerHTML = '(เลือกเพื่อดูรายละเอียดอาการ)';
                }
            };
            oasSelector.addEventListener('change', updateOasDetails);
            updateOasDetails();
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
