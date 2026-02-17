@extends('layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary"><i class="fas fa-users-cog me-2"></i>ข้อมูลผู้ป่วย: พื้นที่ <span
                class="text-success">{{ auth()->user()->area }}</span></h3>
        <div class="btn-group shadow-sm rounded-pill overflow-hidden">
            <a href="{{ route('patients.create') }}" class="btn btn-primary d-flex align-items-center px-4"><i
                    class="fas fa-user-plus me-2"></i>ลงทะเบียนใหม่</a>
            <button class="btn btn-warning d-flex align-items-center px-4"
                onclick="location.href='#'; alert('กรุณาเลือกคนไข้จากรายการเพื่อบันทึกการติดตาม')"><i
                    class="fas fa-notes-medical me-2"></i>เพิ่มการติดตาม</button>
        </div>
    </div>

    <div class="card-3d p-4">
        <form action="{{ route('patients.index') }}" method="GET" class="row g-2 mb-4">
            <div class="col-md-4">
                <div class="input-group">
                    <span class="input-group-text bg-white border-end-0"><i class="fas fa-search text-muted"></i></span>
                    <input type="text" name="search" class="form-control border-start-0 shadow-none"
                        placeholder="ค้นหาด้วย CID, ชื่อ หรือ นามสกุล..." value="{{ request('search') }}">
                </div>
            </div>
            <div class="col-md-3">
                <select name="smiv_group" class="form-select" onchange="this.form.submit()">
                    <option value="">-- ทุกกลุ่ม SMI-V --</option>
                    @foreach (\App\Constants\SMIConstants::SMIV_TYPES as $key => $label)
                        <option value="{{ $key }}" {{ request('smiv_group') == $key ? 'selected' : '' }}>
                            {{ $key }}</option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2">
                <select name="oas_score" class="form-select" onchange="this.form.submit()">
                    <option value="">-- ระดับ OAS --</option>
                    <option value="3" {{ request('oas_score') == '3' ? 'selected' : '' }}>🔴 OAS-3 (รุนแรงมาก)</option>
                    <option value="2" {{ request('oas_score') == '2' ? 'selected' : '' }}>🟠 OAS-2 (เร่งด่วน)</option>
                    <option value="1" {{ request('oas_score') == '1' ? 'selected' : '' }}>🟡 OAS-1 (เฝ้าระวัง)</option>
                    <option value="0" {{ request('oas_score') == '0' ? 'selected' : '' }}>🟢 OAS-0 (ปกติ)</option>
                </select>
            </div>
            <div class="col-md-3 text-end">
                <div class="btn-group w-100">
                    <button type="submit" class="btn btn-primary shadow-sm">กรอง</button>
                    <a href="{{ route('patients.export') }}" class="btn btn-outline-success shadow-sm"><i
                            class="fas fa-file-export"></i> CSV</a>
                </div>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>CID / ชื่อ-นามสกุล</th>
                        <th>กลุ่ม SMI-V</th>
                        <th class="text-center">OAS Score</th>
                        <th>พบบริการล่าสุด / นัดถัดไป</th>
                        <th class="text-center">สถานะ</th>
                        <th class="text-center">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                        <tr>
                            <td>
                                <div class="small text-muted mb-1"><i
                                        class="fas fa-id-card me-1"></i>{{ $patient->cid ?: '---' }}</div>
                                <div class="fw-bold text-primary">{{ $patient->prefix }}{{ $patient->first_name }}
                                    {{ $patient->last_name }}</div>
                            </td>
                            <td>
                                @foreach ((array) ($patient->smiv_group ?? []) as $group)
                                    <span
                                        class="badge bg-danger-subtle text-danger border border-danger fw-normal small mb-1 me-1">
                                        {{ $group }}
                                    </span>
                                @endforeach
                            </td>
                            <td class="text-center">
                                @php
                                    $oasBadge = match ($patient->oas_score) {
                                        '3' => 'bg-danger',
                                        '2' => 'bg-warning text-dark',
                                        '1' => 'bg-info text-dark',
                                        '0' => 'bg-success',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $oasBadge }} rounded-pill px-3 shadow-sm">
                                    OAS-{{ $patient->oas_score ?? '?' }}
                                </span>
                            </td>
                            <td>
                                @if ($patient->last_visit_date)
                                    <div class="small fw-bold text-success"><i
                                            class="fas fa-check-circle me-1"></i>{{ $patient->last_visit_date->format('d/m/Y') }}
                                    </div>
                                @else
                                    <div class="small text-muted italic">ยังไม่มีข้อมูล</div>
                                @endif
                                @if ($patient->next_appointment_date)
                                    <div class="small text-danger fw-bold">📅 นัด:
                                        {{ $patient->next_appointment_date->format('d/m/Y') }}</div>
                                @endif
                            </td>
                            <td class="text-center">
                                @php
                                    $statusBadge = match ($patient->status) {
                                        'จำหน่าย' => 'bg-secondary',
                                        'เกินกำหนดนัด' => 'bg-danger',
                                        default => 'bg-success-subtle text-success border border-success',
                                    };
                                @endphp
                                <span class="badge {{ $statusBadge }} fw-normal px-2">{{ $patient->status }}</span>
                            </td>
                            <td class="text-center">
                                <div class="btn-group gap-1">
                                    <a href="{{ route('patients.show', $patient) }}"
                                        class="btn btn-sm btn-info text-white rounded-circle shadow-sm"
                                        title="ดูรายละเอียด"><i class="fas fa-eye"></i></a>
                                    <a href="{{ route('follow-ups.create', $patient) }}"
                                        class="btn btn-sm btn-warning rounded-circle shadow-sm" title="บันทึกติดตามอาการ"><i
                                            class="fas fa-notes-medical"></i></a>
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-5 text-muted">ไม่พบข้อมูลผู้ป่วยในพื้นที่นี้</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="mt-4">
            {{ $patients->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
