@extends('layout')

@section('content')
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-7">
            <div class="text-center mb-5 pt-4">
                <h2 class="fw-bold text-primary mb-2"><i class="fas fa-user-plus me-2"></i>สมัครสมาชิกใหม่</h2>
                <p class="text-muted">ลงทะเบียนเพื่อเข้าใช้ระบบติดตามผู้ป่วย SMI-V</p>
            </div>

            @if ($errors->any())
                <div class="alert alert-danger alert-dismissible fade show mb-4 rounded-3" role="alert">
                    <i class="fas fa-exclamation-circle me-2"></i>
                    <strong>เกิดข้อผิดพลาด</strong>
                    <ul class="mb-0 mt-2">
                        @foreach ($errors->all() as $error)
                            <li>{{ $error }}</li>
                        @endforeach
                    </ul>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            @endif

            <form action="{{ route('register') }}" method="POST" id="registerForm">
                @csrf

                <!-- Section 1: ข้อมูลทั่วไป -->
                <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden">
                    <div class="card-header bg-primary text-white py-3">
                        <h5 class="m-0 fw-bold"><i class="fas fa-user me-2"></i>ข้อมูลทั่วไป</h5>
                    </div>
                    <div class="card-body p-4 bg-light-subtle">
                        <div class="row g-3">
                            <div class="col-md-2">
                                <label class="form-label fw-bold">คำนำหน้า</label>
                                <select name="prefix" class="form-select border-primary-subtle shadow-sm" required>
                                    <option value="นาย">นาย</option>
                                    <option value="นาง">นาง</option>
                                    <option value="นางสาว">นางสาว</option>
                                </select>
                            </div>
                            <div class="col-md-5">
                                <label class="form-label fw-bold">ชื่อ</label>
                                <input type="text" name="fname" class="form-control border-primary-subtle shadow-sm"
                                    required placeholder="สมชาย"  value="{{ old('fname') }}">
                                @error('fname')
                                    <small class="text-danger d-block mt-1"><i class="fas fa-times-circle me-1"></i>{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-5">
                                <label class="form-label fw-bold">นามสกุล</label>
                                <input type="text" name="lname" class="form-control border-primary-subtle shadow-sm"
                                    required placeholder="ใจดี"  value="{{ old('lname') }}">
                                @error('lname')
                                    <small class="text-danger d-block mt-1"><i class="fas fa-times-circle me-1"></i>{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">พื้นที่รับผิดชอบ</label>
                                <select name="area" class="form-select border-primary-subtle shadow-sm" required>
                                    <option value="">-- เลือกพื้นที่ --</option>
                                    @foreach ($areas as $area)
                                        <option value="{{ $area }}" {{ old('area') == $area ? 'selected' : '' }}>
                                            {{ $area }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('area')
                                    <small class="text-danger d-block mt-1"><i class="fas fa-times-circle me-1"></i>{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">อำเภอ</label>
                                <select name="amphoe" class="form-select border-primary-subtle shadow-sm" required>
                                    <option value="">-- เลือกอำเภอ --</option>
                                    @foreach ($amphoes as $amphoe)
                                        <option value="{{ $amphoe }}" {{ old('amphoe') == $amphoe ? 'selected' : '' }}>
                                            {{ $amphoe }}
                                        </option>
                                    @endforeach
                                </select>
                                @error('amphoe')
                                    <small class="text-danger d-block mt-1"><i class="fas fa-times-circle me-1"></i>{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Section 2: ข้อมูลเข้าใช้ระบบ -->
                <div class="card shadow-sm border-0 mb-4 rounded-4 overflow-hidden">
                    <div class="card-header bg-success text-white py-3">
                        <h5 class="m-0 fw-bold"><i class="fas fa-lock me-2"></i>ข้อมูลเข้าใช้ระบบ</h5>
                    </div>
                    <div class="card-body p-4 bg-light-subtle">
                        <div class="row g-3">
                            <div class="col-md-12">
                                <label class="form-label fw-bold">Username</label>
                                <input type="text" name="username" class="form-control border-success-subtle shadow-sm"
                                    required placeholder="smivbrh" value="{{ old('username') }}">
                                @error('username')
                                    <small class="text-danger d-block mt-1"><i class="fas fa-times-circle me-1"></i>{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">รหัสผ่าน</label>
                                <input type="password" name="password" class="form-control border-success-subtle shadow-sm"
                                    required placeholder="ตั้งรหัสผ่าน">
                                @error('password')
                                    <small class="text-danger d-block mt-1"><i class="fas fa-times-circle me-1"></i>{{ $message }}</small>
                                @enderror
                            </div>

                            <div class="col-md-6">
                                <label class="form-label fw-bold">ยืนยันรหัสผ่าน</label>
                                <input type="password" name="password_confirmation" class="form-control border-success-subtle shadow-sm"
                                    required placeholder="ยืนยันรหัสผ่าน">
                                @error('password_confirmation')
                                    <small class="text-danger d-block mt-1"><i class="fas fa-times-circle me-1"></i>{{ $message }}</small>
                                @enderror
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="d-grid gap-2 mb-4">
                    <button type="submit" class="btn btn-primary btn-lg rounded-pill fw-bold shadow-sm">
                        <i class="fas fa-check me-2"></i>สมัครสมาชิก
                    </button>
                </div>

                <div class="text-center pb-4">
                    <p class="text-muted">มีบัญชีอยู่แล้ว?
                        <a href="{{ route('login') }}" class="text-primary fw-bold text-decoration-none">เข้าสู่ระบบ</a>
                    </p>
                </div>
            </form>
        </div>
    </div>
@endsection
