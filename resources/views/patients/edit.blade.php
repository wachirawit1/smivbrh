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
                <div class="card card-3d p-4 mb-4 border-0">
                    <h5 class="fw-bold text-primary mb-3 border-bottom pb-2">1. ข้อมูลทั่วไป</h5>
                    <div class="row g-3">
                        <div class="col-md-2">
                            <label class="form-label fw-bold">คำนำหน้า</label>
                            <select name="prefix" class="form-select border-primary-subtle shadow-sm">
                                <option value="นาย" {{ $patient->prefix == 'นาย' ? 'selected' : '' }}>นาย</option>
                                <option value="นาง" {{ $patient->prefix == 'นาง' ? 'selected' : '' }}>นาง</option>
                                <option value="นางสาว" {{ $patient->prefix == 'นางสาว' ? 'selected' : '' }}>นางสาว</option>
                            </select>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">ชื่อ</label>
                            <input type="text" name="first_name" class="form-control border-primary-subtle shadow-sm" required value="{{ $patient->first_name }}">
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">นามสกุล</label>
                            <input type="text" name="last_name" class="form-control border-primary-subtle shadow-sm" required value="{{ $patient->last_name }}">
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">เพศ</label>
                            <select name="gender" class="form-select border-primary-subtle shadow-sm">
                                <option value="ชาย" {{ $patient->gender == 'ชาย' ? 'selected' : '' }}>ชาย</option>
                                <option value="หญิง" {{ $patient->gender == 'หญิง' ? 'selected' : '' }}>หญิง</option>
                            </select>
                        </div>
                        <div class="col-md-2">
                            <label class="form-label fw-bold">อายุ (ปี)</label>
                            <input type="number" name="age" id="age_display" class="form-control bg-light" readonly value="{{ $patient->age }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold text-danger">เลขประจำตัวประชาชน (CID) *</label>
                            <input type="text" name="cid" class="form-control border-danger-subtle shadow-sm @error('cid') is-invalid @enderror" 
                                maxlength="13" required placeholder="เลข 13 หลัก" value="{{ old('cid', $patient->cid) }}"
                                oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                            @error('cid')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-5">
                            <label class="form-label fw-bold">วันเกิด (พ.ศ.)</label>
                            <div class="input-group">
                                <select name="birth_day" id="birth_day" class="form-select border-primary-subtle shadow-sm"></select>
                                <select name="birth_month" id="birth_month" class="form-select border-primary-subtle shadow-sm"></select>
                                <select name="birth_year" id="birth_year" class="form-select border-primary-subtle shadow-sm" onchange="calculateAge()"></select>
                            </div>
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">เบอร์โทรศัพท์คนไข้</label>
                            <input type="text" name="phone" id="phone" class="form-control border-primary-subtle shadow-sm @error('phone') is-invalid @enderror" 
                                maxlength="10" placeholder="08xxxxxxxx" value="{{ old('phone', $patient->phone) }}"
                                oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                            @error('phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                        <div class="col-md-3">
                            <label class="form-label fw-bold">เบอร์โทรศัพท์ญาติ (ถ้ามี)</label>
                            <input type="text" name="relative_phone" id="relative_phone" class="form-control border-primary-subtle shadow-sm @error('relative_phone') is-invalid @enderror" 
                                maxlength="10" placeholder="08xxxxxxxx" value="{{ old('relative_phone', $patient->relative_phone) }}"
                                oninput="this.value=this.value.replace(/[^0-9]/g,'')">
                            @error('relative_phone')
                                <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>
                    </div>
                </div>

                <!-- Section 2: ที่อยู่และการขึ้นทะเบียน -->
                <div class="card card-3d p-4 mb-4 border-0">
                    <h5 class="fw-bold text-success mb-3 border-bottom pb-2">2. ที่อยู่และการขึ้นทะเบียน</h5>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">ที่อยู่ปัจจุบัน (บ้านเลขที่/หมู่ที่)</label>
                            <input type="text" name="address" class="form-control border-success-subtle shadow-sm" value="{{ $patient->address }}">
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">สถานบริการ (Area)</label>
                            <select name="area" class="form-select border-success-subtle shadow-sm" required>
                                @foreach ($areas as $area)
                                    <option value="{{ $area }}" {{ $patient->area == $area ? 'selected' : '' }}>{{ $area }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">อำเภอ</label>
                            <select name="amphoe" id="amphoe" class="form-select border-success-subtle shadow-sm" onchange="updateTambons()"></select>
                        </div>
                        <div class="col-md-4">
                            <label class="form-label fw-bold">ตำบล</label>
                            <select name="tambon" id="tambon" class="form-select border-success-subtle shadow-sm"></select>
                        </div>
                    </div>
                </div>

                <!-- Section 3: ข้อมูลทางคลินิก (Psychologist's Design) -->
                <div class="card card-3d p-4 mb-4 border-0">
                    <h5 class="fw-bold text-danger mb-3 border-bottom pb-2">3. ข้อมูลการเจ็บป่วยและอาการปัจจุบัน</h5>
                    <div class="row g-3">
                        <div class="col-md-12">
                            <label class="form-label fw-bold">วินิจฉัย (ICD-10)</label>
                            <select name="diagnosis" class="form-select border-danger-subtle shadow-sm">
                                <option value="">-- เลือก ICD-10 --</option>
                                @foreach (\App\Constants\SMIConstants::ICD10 as $icd)
                                    <option value="{{ $icd }}" {{ $patient->diagnosis == $icd ? 'selected' : '' }}>{{ $icd }}</option>
                                @endforeach
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label class="form-label fw-bold text-danger">ประเภท SMI-V (เลือกได้มากกว่า 1 ข้อ)</label>
                            <div class="bg-white p-3 rounded border border-danger-subtle">
                                @php $currentGroups = $patient->smiv_group ?? []; @endphp
                                @foreach (\App\Constants\SMIConstants::SMIV_TYPES as $key => $label)
                                    <div class="form-check mb-2">
                                        <input class="form-check-input" type="checkbox" name="smiv_group[]" value="{{ $key }}" id="smiv_{{ $loop->index }}" {{ in_array($key, (array) $currentGroups) ? 'checked' : '' }}>
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
                                            <option value="{{ $score }}" {{ $patient->oas_score == $score ? 'selected' : '' }}>{{ $item }}</option>
                                        @endforeach
                                    </optgroup>
                                @endforeach
                                <optgroup label="ปกติ">
                                    <option value="0" {{ $patient->oas_score == '0' ? 'selected' : '' }}>ไม่มีอาการข้างต้น</option>
                                </optgroup>
                            </select>
                        </div>

                        <div class="col-md-12 mt-4">
                            <h6 class="fw-bold text-dark mb-3"><i class="fas fa-clipboard-check me-2"></i>อาการปัจจุบัน 6 ด้าน</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">1. ด้านอาการทางจิต</label>
                                    <select name="symp_mind" class="form-select border-secondary-subtle" required>
                                        <option value="">เลือก</option>
                                        @foreach(\App\Constants\SMIConstants::SYMP_MIND as $opt) 
                                            <option value="{{$opt}}" {{ $patient->symp_mind == $opt ? 'selected' : '' }}>{{$opt}}</option> 
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-6">
                                    <label class="form-label fw-bold">2. ด้านการกินยา</label>
                                    <select name="symp_med" class="form-select border-secondary-subtle" required>
                                        <option value="">เลือก</option>
                                        @foreach(\App\Constants\SMIConstants::SYMP_MED as $opt) 
                                            <option value="{{$opt}}" {{ $patient->symp_med == $opt ? 'selected' : '' }}>{{$opt}}</option> 
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">3. ด้านผู้ดูแล/ญาติ</label>
                                    <select name="symp_care" class="form-select border-secondary-subtle" required>
                                        <option value="">เลือก</option>
                                        @foreach(\App\Constants\SMIConstants::SYMP_CARE as $opt) 
                                            <option value="{{$opt}}" {{ $patient->symp_care == $opt ? 'selected' : '' }}>{{$opt}}</option> 
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">4. ด้านการประกอบอาชีพ</label>
                                    <select name="symp_job" class="form-select border-secondary-subtle" required>
                                        <option value="">เลือก</option>
                                        @foreach(\App\Constants\SMIConstants::SYMP_JOB as $opt) 
                                            <option value="{{$opt}}" {{ $patient->symp_job == $opt ? 'selected' : '' }}>{{$opt}}</option> 
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">5. ด้านสิ่งแวดล้อม</label>
                                    <select name="symp_env" class="form-select border-secondary-subtle" required>
                                        <option value="">เลือก</option>
                                        @foreach(\App\Constants\SMIConstants::SYMP_ENV as $opt) 
                                            <option value="{{$opt}}" {{ $patient->symp_env == $opt ? 'selected' : '' }}>{{$opt}}</option> 
                                        @endforeach
                                    </select>
                                </div>
                                <div class="col-md-12">
                                    <label class="form-label fw-bold">6. ด้านการใช้สารเสพติด</label>
                                    <select name="symp_drug" id="symp_drug" class="form-select border-secondary-subtle" onchange="toggleSubstanceBox()" required>
                                        <option value="">เลือก</option>
                                        @foreach(\App\Constants\SMIConstants::SYMP_DRUG as $opt) 
                                            <option value="{{$opt}}" {{ $patient->symp_drug == $opt ? 'selected' : '' }}>{{$opt}}</option> 
                                        @endforeach
                                    </select>
                                </div>
                                <div id="substance_box" class="col-md-12" style="display: {{ in_array($patient->symp_drug, ['ใช้บ้าง', 'ใช้ประจำ']) ? 'block' : 'none' }};">
                                    <div class="bg-light p-3 rounded border border-secondary-subtle">
                                        <label class="form-label fw-bold small text-muted mb-2">ระบุสารเสพติดที่ใช้</label>
                                        <div class="d-flex flex-wrap gap-3">
                                            @php 
                                                $currentSubstances = (array) ($patient->substances ?? []); 
                                                $otherVal = '';
                                                foreach($currentSubstances as $s) {
                                                    if(strpos($s, 'อื่นๆ:') === 0) $otherVal = trim(str_replace('อื่นๆ:', '', $s));
                                                }
                                            @endphp
                                            @foreach (\App\Constants\SMIConstants::SUBSTANCES as $sub)
                                                <div class="form-check">
                                                    <input class="form-check-input substance-check" type="checkbox" name="substances[]" 
                                                        value="{{ $sub }}" id="sub_{{ $loop->index }}" 
                                                        {{ in_array($sub, $currentSubstances) || ($sub === 'อื่นๆ' && $otherVal !== '') ? 'checked' : '' }}
                                                        onchange="toggleOtherSubstance()">
                                                    <label class="form-check-label" for="sub_{{ $loop->index }} text-sm">{{ $sub }}</label>
                                                </div>
                                            @endforeach
                                        </div>
                                        <div id="substance_other_group" class="mt-2" style="display: {{ $otherVal !== '' ? 'block' : 'none' }};">
                                            <input type="text" name="substance_other" id="substance_other" 
                                                class="form-control form-control-sm border-secondary-subtle" 
                                                placeholder="ระบุสารเสพติดอื่นๆ..." value="{{ $otherVal }}">
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 4: แผนการนัดหมาย -->
                <div class="card card-3d p-4 mb-5 border-0">
                    <h5 class="fw-bold text-warning mb-3 border-bottom pb-2">4. การนัดหมาย</h5>
                    <div class="row g-3">
                        <div class="col-md-6">
                            <label class="form-label fw-bold">วันที่พบแพทย์/ออก รพ. ล่าสุด</label>
                            <div class="input-group">
                                <select name="visit_day" id="visit_day" class="form-select border-warning-subtle shadow-sm"></select>
                                <select name="visit_month" id="visit_month" class="form-select border-warning-subtle shadow-sm"></select>
                                <select name="visit_year" id="visit_year" class="form-select border-warning-subtle shadow-sm"></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold text-danger">วันที่นัดหมายครั้งถัดไป</label>
                            <div class="input-group">
                                <select name="next_day" id="next_day" class="form-select border-danger-subtle shadow-sm"></select>
                                <select name="next_month" id="next_month" class="form-select border-danger-subtle shadow-sm"></select>
                                <select name="next_year" id="next_year" class="form-select border-danger-subtle shadow-sm"></select>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="form-label fw-bold">สถานะติดตาม</label>
                            <select name="status" class="form-select border-warning-subtle shadow-sm">
                                <option value="ติดตามปกติ" {{ $patient->status == 'ติดตามปกติ' ? 'selected' : '' }}>ติดตามปกติ</option>
                                <option value="จำหน่าย" {{ $patient->status == 'จำหน่าย' ? 'selected' : '' }}>จำหน่าย (D/C)</option>
                                <option value="เกินกำหนดนัด" {{ $patient->status == 'เกินกำหนดนัด' ? 'selected' : '' }}>เกินกำหนดนัด</option>
                            </select>
                        </div>
                    </div>
                </div>

                <div class="text-center mb-5">
                    <button type="submit" class="btn btn-primary btn-lg px-5 py-2 rounded-pill shadow-lg fw-bold">
                        <i class="fas fa-save me-2"></i> บันทึกการแก้ไข
                    </button>
                </div>
            </form>
        </div>
    </div>

    <script>
        const amphurTambonPath = @json(\App\Constants\SMIConstants::AMPHUR_TAMBON);

        function toggleSubstanceBox() {
            const val = document.getElementById('symp_drug').value;
            const box = document.getElementById('substance_box');
            if (val === 'ใช้บ้าง' || val === 'ใช้ประจำ') {
                box.style.display = 'block';
            } else {
                box.style.display = 'none';
            }
        }

        function toggleOtherSubstance() {
            const checkboxes = document.querySelectorAll('.substance-check');
            const otherGroup = document.getElementById('substance_other_group');
            const otherInput = document.getElementById('substance_other');
            
            let isOtherChecked = false;
            checkboxes.forEach(cb => {
                if (cb.checked && cb.value === 'อื่นๆ') isOtherChecked = true;
            });

            otherGroup.style.display = isOtherChecked ? 'block' : 'none';
            otherInput.required = isOtherChecked;
            if (!isOtherChecked) otherInput.value = '';
        }

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
