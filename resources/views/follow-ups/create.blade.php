@extends('layout')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-11 col-xl-10">
            <div class="d-flex align-items-center gap-3 mb-4">
                <a href="{{ route('patients.show', $patient) }}" class="btn btn-outline-secondary rounded-pill px-3 shadow-sm">
                    <i class="fas fa-chevron-left"></i> กลับไปที่โปรไฟล์
                </a>
                <h3 class="fw-bold text-success m-0"><i class="fas fa-file-medical me-2"></i>บันทึกการติดตามผู้ป่วย (Follow-up)</h3>
            </div>

            <div class="card shadow-sm border-0 mb-4 bg-success text-white">
                <div class="card-body p-3">
                    <div class="d-flex align-items-center gap-3">
                        <div class="bg-white text-success rounded-circle d-flex align-items-center justify-content-center mx-1"
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

                @if ($errors->any())
                    <div class="alert alert-danger shadow-sm">
                        <div class="fw-bold mb-2">กรุณาตรวจสอบข้อมูลการติดตาม</div>
                        <ul class="mb-0 ps-3">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="card shadow-sm border-0 mb-4 overflow-hidden">
                    <div class="card-header bg-success text-white py-3">
                        <h5 class="m-0 fw-bold"><i class="fas fa-calendar-check me-2"></i>ข้อมูลการติดตามในวันนี้</h5>
                    </div>
                    <div class="card-body p-4 bg-light-subtle">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">วันที่มารับบริการ</label>
                                <div class="input-group">
                                    <select name="visit_day" id="visit_day" class="form-select border-success-subtle shadow-sm"></select>
                                    <select name="visit_month" id="visit_month" class="form-select border-success-subtle shadow-sm"></select>
                                    <select name="visit_year" id="visit_year" class="form-select border-success-subtle shadow-sm"></select>
                                </div>
                            </div>
                            <div class="col-md-3">
                                <label class="form-label fw-bold">สถานะการมา</label>
                                <select name="visit_status" id="visit_status" class="form-select border-success-subtle shadow-sm">
                                    <option value="มาตามนัด" {{ old('visit_status') == 'มาตามนัด' ? 'selected' : '' }}>มาตามนัด</option>
                                    <option value="มาก่อนนัด" {{ old('visit_status') == 'มาก่อนนัด' ? 'selected' : '' }}>มาก่อนนัด</option>
                                    <option value="มาหลังนัด" {{ old('visit_status') == 'มาหลังนัด' ? 'selected' : '' }}>มาหลังนัด</option>
                                </select>
                            </div>
                            <div class="col-md-3" id="reason_container" style="display: none;">
                                <label class="form-label fw-bold text-danger">เหตุผล (บังคับเลือก)</label>
                                <select name="visit_reason" id="visit_reason" class="form-select border-danger-subtle shadow-sm">
                                    <option value="">-- เลือกเหตุผล --</option>
                                    <option value="อาการทางจิตเวชกำเริบ" {{ old('visit_reason') == 'อาการทางจิตเวชกำเริบ' ? 'selected' : '' }}>อาการทางจิตเวชกำเริบ</option>
                                    <option value="ยาหมดก่อนนัด" {{ old('visit_reason') == 'ยาหมดก่อนนัด' ? 'selected' : '' }}>ยาหมดก่อนนัด</option>
                                    <option value="มีอาการไม่พึงประสงค์จากยา" {{ old('visit_reason') == 'มีอาการไม่พึงประสงค์จากยา' ? 'selected' : '' }}>มีอาการไม่พึงประสงค์จากยา</option>
                                    <option value="มีปัญหากับครอบครัวหรือสังคม" {{ old('visit_reason') == 'มีปัญหากับครอบครัวหรือสังคม' ? 'selected' : '' }}>มีปัญหากับครอบครัวหรือสังคม</option>
                                    <option value="มีความเข้าใจผิดเรื่องวันนัด" {{ old('visit_reason') == 'มีความเข้าใจผิดเรื่องวันนัด' ? 'selected' : '' }}>มีความเข้าใจผิดเรื่องวันนัด</option>
                                    <option value="ติดธุระในวันนัด" {{ old('visit_reason') == 'ติดธุระในวันนัด' ? 'selected' : '' }}>ติดธุระในวันนัด</option>
                                    <option value="ยากลำบากในการเดินทาง" {{ old('visit_reason') == 'ยากลำบากในการเดินทาง' ? 'selected' : '' }}>ยากลำบากในการเดินทาง</option>
                                    <option value="อื่นๆ" {{ old('visit_reason') == 'อื่นๆ' ? 'selected' : '' }}>อื่นๆ</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card card-3d p-4 mb-4 border-0">
                    <h5 class="fw-bold text-danger mb-3 border-bottom pb-2">2. ข้อมูลทางคลินิกและการประเมิน</h5>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">วินิจฉัยปัจจุบัน (ICD-10)</label>
                            <select name="diagnosis" class="form-select border-danger-subtle shadow-sm">
                                <option value="">-- เลือก ICD-10 --</option>
                                @foreach ($icd10 as $icd)
                                    <option value="{{ $icd }}" {{ old('diagnosis', $patient->diagnosis) == $icd ? 'selected' : '' }}>
                                        {{ $icd }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">กลุ่มผู้ป่วย SMI-V (ครั้งนี้)</label>
                            <div class="bg-white p-3 rounded border border-danger-subtle">
                                @php $currentGroups = old('smiv_group', (array) ($patient->smiv_group ?? [])); @endphp
                                @foreach ($smiv_types as $key => $label)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="smiv_group[]"
                                            value="{{ $key }}" id="smiv_{{ $loop->index }}"
                                            {{ in_array($key, $currentGroups) ? 'checked' : '' }}>
                                        <label class="form-check-label" for="smiv_{{ $loop->index }}">{{ $label }}</label>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold">แบบประเมินพฤติกรรมก้าวร้าวรุนแรง (OAS)</label>
                            <select name="oas_score" class="form-select border-danger-subtle shadow-sm" required>
                                <option value="">-- เลือกอาการที่ตรวจพบ --</option>
                                @foreach (\App\Constants\SMIConstants::OAS_OPTIONS as $score => $items)
                                    <optgroup label="OAS Score {{ $score }} คะแนน">
                                        @foreach ($items as $item)
                                            <option value="{{ $score }}" {{ old('oas_score', $patient->oas_score) == $score ? 'selected' : '' }}>{{ $item }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                                <optgroup label="ปกติ">
                                    <option value="0" {{ old('oas_score', $patient->oas_score) == '0' ? 'selected' : '' }}>ไม่มีอาการข้างต้น</option>
                                </optgroup>
                            </select>
                        </div>

                        <div class="col-md-12 mt-4">
                            <h6 class="fw-bold text-dark mb-3"><i class="fas fa-clipboard-check me-2"></i>อาการปัจจุบัน 6 ด้าน (ครั้งนี้)</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">1. ด้านอาการทางจิต</label>
                                    <select name="symp_mind" class="form-select border-secondary-subtle" required>
                                        <option value="">เลือก</option>
                                        @foreach(\App\Constants\SMIConstants::SYMP_MIND as $opt) 
                                            <option value="{{$opt}}" {{ old('symp_mind', $patient->symp_mind) == $opt ? 'selected' : '' }}>{{$opt}}</option> 
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">2. ด้านการกินยา</label>
                                    <select name="symp_med" class="form-select border-secondary-subtle" required>
                                        <option value="">เลือก</option>
                                        @foreach(\App\Constants\SMIConstants::SYMP_MED as $opt) 
                                            <option value="{{$opt}}" {{ old('symp_med', $patient->symp_med) == $opt ? 'selected' : '' }}>{{$opt}}</option> 
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">3. ด้านผู้ดูแล/ญาติ</label>
                                    <select name="symp_care" class="form-select border-secondary-subtle" required>
                                        <option value="">เลือก</option>
                                        @foreach(\App\Constants\SMIConstants::SYMP_CARE as $opt) 
                                            <option value="{{$opt}}" {{ old('symp_care', $patient->symp_care) == $opt ? 'selected' : '' }}>{{$opt}}</option> 
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">4. ด้านการประกอบอาชีพ</label>
                                    <select name="symp_job" class="form-select border-secondary-subtle" required>
                                        <option value="">เลือก</option>
                                        @foreach(\App\Constants\SMIConstants::SYMP_JOB as $opt) 
                                            <option value="{{$opt}}" {{ old('symp_job', $patient->symp_job) == $opt ? 'selected' : '' }}>{{$opt}}</option> 
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">5. ด้านสิ่งแวดล้อม</label>
                                    <select name="symp_env" class="form-select border-secondary-subtle" required>
                                        <option value="">เลือก</option>
                                        @foreach(\App\Constants\SMIConstants::SYMP_ENV as $opt) 
                                            <option value="{{$opt}}" {{ old('symp_env', $patient->symp_env) == $opt ? 'selected' : '' }}>{{$opt}}</option> 
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">6. ด้านการใช้สารเสพติด</label>
                                    <select name="symp_drug" id="symp_drug" class="form-select border-secondary-subtle" onchange="toggleSubstanceBox()" required>
                                        <option value="">เลือก</option>
                                        @foreach(\App\Constants\SMIConstants::SYMP_DRUG as $opt) 
                                            <option value="{{$opt}}" {{ old('symp_drug', $patient->symp_drug) == $opt ? 'selected' : '' }}>{{$opt}}</option> 
                                        @endforeach
                                    </select>
                                </div>
                                                <div id="substance_box" class="col-md-12" style="display: {{ in_array(old('symp_drug', $patient->symp_drug), ['ใช้บ้าง', 'ใช้ประจำ']) ? 'block' : 'none' }};">
                                    <div class="bg-light p-3 rounded border border-secondary-subtle">
                                        <label class="form-label fw-bold small text-muted mb-2">ระบุสารเสพติดที่ใช้ (ครั้งนี้)</label>
                                        <div class="d-flex flex-wrap gap-3">
                                            @php $currentSubs = old('substances', (array) ($patient->substances ?? [])); @endphp
                                            @foreach ($substances as $sub)
                                                <div class="form-check">
                                                    <input class="form-check-input substance-check" type="checkbox" name="substances[]"
                                                        value="{{ $sub }}" id="sub_{{ $loop->index }}"
                                                        {{ in_array($sub, $currentSubs) ? 'checked' : '' }}
                                                        onchange="toggleOtherSubstance()">
                                                    <label class="form-check-label" for="sub_{{ $loop->index }} text-sm">{{ $sub }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div id="substance_other_block" class="mt-2" style="display: none;">
                                            <label class="small fw-bold text-primary mb-1">ระบุสารเสพติดอื่นๆ (โปรดระบุ):</label>
                                            <input type="text" name="substance_other" id="substance_other_input" 
                                                class="form-control form-control-sm border-primary-subtle" 
                                                placeholder="ระบุชื่อสารเสพติด..." value="{{ old('substance_other') }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="card shadow-sm border-0 mb-5 overflow-hidden">
                    <div class="card-header bg-warning text-dark py-3">
                        <h5 class="m-0 fw-bold"><i class="fas fa-calendar-alt me-2"></i>แผนการรักษาและการนัดหมาย</h5>
                    </div>
                    <div class="card-body p-4 bg-light-subtle">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">แผนการดูแลต่อเนื่อง</label>
                                <div class="d-flex flex-wrap gap-4">
                                    @foreach (['นัดต่อเนื่อง', 'Refer (ส่งต่อ)', 'จำหน่าย (D/C)', 'อื่นๆ'] as $plan)
                                        <div class="form-check">
                                            <input class="form-check-input plan-radio" type="radio" name="appointment_plan"
                                                value="{{ $plan }}" id="plan_{{ $loop->index }}"
                                                {{ old('appointment_plan', 'นัดต่อเนื่อง') == $plan ? 'checked' : '' }}
                                                onchange="toggleNextAppointment()">
                                            <label class="form-check-label" for="plan_{{ $loop->index }}">{{ $plan }}</label>
                                        </div>
                                    @endforeach
                                </div>
                                <div id="plan_other_block" class="mt-2" style="display: none;">
                                    <label class="small fw-bold text-warning mb-1">ระบุรายละเอียดแผนงานอื่นๆ (โปรดระบุ):</label>
                                    <input type="text" name="appointment_plan_other" id="appointment_plan_other_input" 
                                        class="form-control border-warning-subtle shadow-sm" 
                                        placeholder="ระบุแผนการดูแล..." value="{{ old('appointment_plan_other') }}">
                                </div>
                            </div>
                            <div class="col-md-6" id="next_appointment_group">
                                <label class="form-label fw-bold text-danger">วันที่นัดครั้งถัดไป</label>
                                <div class="input-group">
                                    <select name="next_day" id="next_day" class="form-select border-danger shadow-sm"></select>
                                    <select name="next_month" id="next_month" class="form-select border-danger shadow-sm"></select>
                                    <select name="next_year" id="next_year" class="form-select border-danger shadow-sm"></select>
                                </div>
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
        // --- 1. Functions for Dynamic Field Visibility (Global Scope) ---
        
        function toggleSubstanceBox() {
            const val = document.getElementById('symp_drug').value;
            const box = document.getElementById('substance_box');
            if (!box) return;
            box.style.display = (val === 'ใช้บ้าง' || val === 'ใช้ประจำ') ? 'block' : 'none';
        }

        function toggleOtherSubstance() {
            const checkboxes = document.querySelectorAll('.substance-check');
            const otherBlock = document.getElementById('substance_other_block');
            const otherInput = document.getElementById('substance_other_input');
            if (!otherBlock) return;
            
            let isOtherChecked = false;
            checkboxes.forEach(cb => {
                if (cb.checked && cb.value === 'อื่นๆ') isOtherChecked = true;
            });

            otherBlock.style.display = isOtherChecked ? 'block' : 'none';
            otherInput.required = isOtherChecked;
            if (!isOtherChecked) otherInput.value = '';
        }

        function toggleNextAppointment() {
            const selectedPlan = document.querySelector('input[name="appointment_plan"]:checked')?.value;
            const nextAppointmentGroup = document.getElementById('next_appointment_group');
            const planOtherBlock = document.getElementById('plan_other_block');
            const planOtherInput = document.getElementById('appointment_plan_other_input');
            
            if (!nextAppointmentGroup || !planOtherBlock) return;

            // Date Fields
            const nextFields = [
                document.getElementById('next_day'),
                document.getElementById('next_month'),
                document.getElementById('next_year'),
            ];

            // 1. Logic for NEXT APPOINTMENT DATE (Only for "นัดต่อเนื่อง")
            const isNud = (selectedPlan === 'นัดต่อเนื่อง');
            nextAppointmentGroup.style.display = isNud ? 'block' : 'none';
            
            nextFields.forEach(field => {
                if (field) {
                    field.disabled = !isNud;
                    field.required = isNud;
                    if (!isNud) field.value = '';
                }
            });

            // 2. Logic for OTHER PLAN TEXTBOX (Only for "อื่นๆ")
            const isOther = (selectedPlan === 'อื่นๆ');
            planOtherBlock.style.display = isOther ? 'block' : 'none';
            planOtherInput.required = isOther;
            if (!isOther) planOtherInput.value = '';
        }

        function toggleVisitReason() {
            const visitStatus = document.getElementById('visit_status').value;
            const reasonContainer = document.getElementById('reason_container');
            const visitReason = document.getElementById('visit_reason');
            if (!reasonContainer || !visitReason) return;
            
            const showReason = (visitStatus === 'มาก่อนนัด' || visitStatus === 'มาหลังนัด');
            reasonContainer.style.display = showReason ? 'block' : 'none';
            visitReason.required = showReason;
            if (!showReason) visitReason.value = '';
        }

        // --- 2. Date Dropdown Population (Global Scope) ---

        function populateDateDropdowns(dId, mId, yId, setToday = false, selected = null) {
            const dSelect = document.getElementById(dId);
            const mSelect = document.getElementById(mId);
            const ySelect = document.getElementById(yId);
            if (!dSelect || !mSelect || !ySelect) return;

            dSelect.innerHTML = '';
            for (let i = 1; i <= 31; i++) {
                const opt = document.createElement('option');
                opt.value = i; opt.textContent = i;
                dSelect.appendChild(opt);
            }

            const months = ['มกราคม', 'กุมภาพันธ์', 'มีนาคม', 'เมษายน', 'พฤษภาคม', 'มิถุนายน', 'กรกฎาคม', 'สิงหาคม', 'กันยายน', 'ตุลาคม', 'พฤศจิกายน', 'ธันวาคม'];
            mSelect.innerHTML = '';
            months.forEach((m, idx) => {
                const opt = document.createElement('option');
                opt.value = idx + 1; opt.textContent = m;
                mSelect.appendChild(opt);
            });

            const thaiYear = new Date().getFullYear() + 543;
            ySelect.innerHTML = '';
            for (let i = thaiYear - 5; i <= thaiYear + 5; i++) {
                const opt = document.createElement('option');
                opt.value = i; opt.textContent = i;
                if (i === thaiYear) opt.selected = true;
                ySelect.appendChild(opt);
            }

            if (setToday) {
                const today = new Date();
                dSelect.value = today.getDate();
                mSelect.value = today.getMonth() + 1;
                ySelect.value = today.getFullYear() + 543;
            }

            if (selected) {
                if (selected.day) dSelect.value = selected.day;
                if (selected.month) mSelect.value = selected.month;
                if (selected.year) ySelect.value = selected.year;
            }
        }

        // --- 3. Initial Run on DOM Ready ---

        document.addEventListener('DOMContentLoaded', function() {
            const oldVisitDate = @json(['day' => old('visit_day'), 'month' => old('visit_month'), 'year' => old('visit_year')]);
            const oldNextDate = @json(['day' => old('next_day'), 'month' => old('next_month'), 'year' => old('next_year')]);

            populateDateDropdowns('visit_day', 'visit_month', 'visit_year', true, oldVisitDate);
            populateDateDropdowns('next_day', 'next_month', 'next_year', false, oldNextDate);

            // Run initial visibility check
            toggleVisitReason();
            toggleNextAppointment();
            toggleOtherSubstance();
            toggleSubstanceBox();
            
            // Re-bind listeners just in case
            document.getElementById('visit_status').onchange = toggleVisitReason;
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
