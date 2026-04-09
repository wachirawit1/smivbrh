@extends('layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary">
            <i class="fas fa-users-cog me-2"></i>ข้อมูลพื้นที่ของตนเอง:
            <span class="text-success">{{ auth()->user()->area }}</span>
        </h3>
        <div class="btn-group shadow-sm">
            <a href="{{ route('patients.create') }}" class="btn btn-primary d-flex align-items-center px-4">
                <i class="fas fa-user-plus me-2"></i>ลงทะเบียนใหม่
            </a>
        </div>
    </div>

    <div class="card-3d p-4">
        {{-- Search & Filter --}}
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
                    <option value="3" {{ request('oas_score') == '3' ? 'selected' : '' }}>🔴 OAS-3</option>
                    <option value="2" {{ request('oas_score') == '2' ? 'selected' : '' }}>🟠 OAS-2</option>
                    <option value="1" {{ request('oas_score') == '1' ? 'selected' : '' }}>🟡 OAS-1</option>
                    <option value="0" {{ request('oas_score') == '0' ? 'selected' : '' }}>🟢 OAS-0</option>
                </select>
            </div>
            <div class="col-md-3">
                <div class="btn-group w-100">
                    <button type="submit" class="btn btn-primary shadow-sm">กรอง</button>
                    <a href="{{ route('patients.export') }}" class="btn btn-outline-success shadow-sm">
                        <i class="fas fa-file-export"></i> Export CSV
                    </a>
                    <a href="{{ route('patients.index') }}" class="btn btn-outline-secondary shadow-sm">
                        <i class="fas fa-undo"></i>
                    </a>
                </div>
            </div>
        </form>

        {{-- Table --}}
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>HN / ชื่อ–นามสกุล</th>
                        <th>ที่อยู่ (อำเภอ / ตำบล)</th>
                        <th>กลุ่ม SMI-V</th>
                        <th class="text-center">OAS</th>
                        <th>พบบริการล่าสุด / นัดถัดไป</th>
                        <th class="text-center">สถานะ</th>
                        <th class="text-center">จัดการ / ติดตาม</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($patients as $patient)
                        <tr>
                            {{-- ชื่อ --}}
                            <td>
                                <div class="small text-muted mb-1">
                                    <i class="fas fa-id-card me-1"></i>{{ $patient->cid ?: '---' }}
                                </div>
                                <div class="fw-bold text-primary">
                                    {{ $patient->prefix }}{{ $patient->first_name }} {{ $patient->last_name }}
                                </div>
                                <small class="text-muted">อายุ: {{ $patient->age ?? '-' }} ปี</small>
                            </td>

                            {{-- ที่อยู่ --}}
                            <td>
                                <div class="small"><i class="fas fa-map-marker-alt text-secondary me-1"></i>{{ $patient->amphoe ?: '-' }}</div>
                                <div class="small text-muted">{{ $patient->tambon ?: '-' }}</div>
                            </td>

                            {{-- SMI-V --}}
                            <td>
                                @foreach ((array) ($patient->smiv_group ?? []) as $group)
                                    <span
                                        class="badge bg-danger-subtle text-danger border border-danger fw-normal small mb-1 me-1 d-block">
                                        {{ $group }}
                                    </span>
                                @endforeach
                            </td>

                            {{-- OAS Score --}}
                            <td class="text-center">
                                @php
                                    $oasBadge = match ($patient->oas_score) {
                                        '3' => 'bg-oas-3',
                                        '2' => 'bg-oas-2',
                                        '1' => 'bg-oas-1',
                                        '0' => 'bg-oas-0',
                                        default => 'bg-secondary',
                                    };
                                @endphp
                                <span class="badge {{ $oasBadge }} rounded-pill px-3 shadow-sm fw-bold">
                                    OAS-{{ $patient->oas_score ?? '?' }}
                                </span>
                            </td>

                            {{-- วันที่ --}}
                            <td>
                                @if ($patient->last_visit_date)
                                    <div class="small fw-bold text-success">
                                        <i class="fas fa-check-circle me-1"></i>{{ $patient->last_visit_date->format('d/m/Y') }}
                                    </div>
                                @else
                                    <div class="small text-muted fst-italic">ยังไม่มีข้อมูล</div>
                                @endif
                                @if ($patient->next_appointment_date)
                                    <div class="small {{ $patient->status === 'เกินกำหนดนัด' ? 'text-danger fw-bold' : 'text-dark' }}">
                                        📅 นัด: {{ $patient->next_appointment_date->format('d/m/Y') }}
                                    </div>
                                @endif
                            </td>

                            {{-- สถานะ --}}
                            <td class="text-center">
                                @php
                                    $statusBadge = match ($patient->status) {
                                        'จำหน่าย' => 'bg-secondary',
                                        'เกินกำหนดนัด' => 'bg-danger pulse-red',
                                        'ติดตามปกติ' => 'bg-success-subtle text-success border border-success',
                                        default => 'bg-success-subtle text-success border border-success',
                                    };
                                @endphp
                                <span class="badge {{ $statusBadge }} fw-normal px-2">{{ $patient->status }}</span>
                            </td>

                            {{-- จัดการ --}}
                            <td class="text-center">
                                <div class="d-flex gap-1 justify-content-center">
                                    <a href="{{ route('patients.show', $patient) }}"
                                        class="btn btn-sm btn-info text-white rounded-circle shadow-sm" title="ดูรายละเอียด">
                                        <i class="fas fa-eye"></i>
                                    </a>
                                    <a href="{{ route('follow-ups.create', $patient) }}"
                                        class="btn btn-sm btn-warning rounded-circle shadow-sm"
                                        title="บันทึกการติดตามอาการ (Follow Up)">
                                        <i class="fas fa-notes-medical"></i>
                                    </a>
                                    @if (auth()->user()->role === 'admin')
                                        <a href="{{ route('patients.edit', $patient) }}"
                                            class="btn btn-sm btn-secondary rounded-circle shadow-sm" title="แก้ไขข้อมูล">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        <form action="{{ route('patients.destroy', $patient) }}" method="POST"
                                            onsubmit="return confirm('ยืนยันการลบผู้ป่วยรายนี้?')" style="display:inline;">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit"
                                                class="btn btn-sm btn-danger rounded-circle shadow-sm" title="ลบ">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </form>
                                    @endif
                                </div>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="7" class="text-center py-5 text-muted">
                                <i class="fas fa-users fa-2x mb-2 d-block opacity-25"></i>
                                ไม่พบข้อมูลผู้ป่วยในพื้นที่นี้
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="mt-4">
            {{ $patients->appends(request()->query())->links() }}
        </div>
    </div>
@endsection
