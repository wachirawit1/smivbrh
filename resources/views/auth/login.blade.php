@extends('layout')

@section('content')
    <div class="row justify-content-center mt-5">
        <div class="col-md-5">
            <div class="card-3d p-4 text-center">
                <h4 class="text-primary fw-bold mb-4"><i class="fas fa-lock me-2"></i>เข้าสู่ระบบ SMI-V</h4>

                <form action="{{ route('login') }}" method="POST" class="text-start mt-2">
                    @csrf
                    <div class="mb-3">
                        <label class="form-label">Username / Email (ชื่อผู้ใช้ หรือ อีเมล)</label>
                        <input type="text" name="login_id" class="form-control @error('login_id') is-invalid @enderror"
                            required autofocus value="{{ old('login_id') }}">
                        @error('login_id')
                            <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                    <div class="mb-4">
                        <label class="form-label">Password (รหัสผ่าน)</label>
                        <input type="password" name="password" class="form-control" required>
                    </div>

                    <button type="submit" class="btn-3d w-100 py-2 fs-5 text-white">Login</button>
                </form>

                <hr class="mt-4">
                <p class="text-muted small">หากยังไม่มีบัญชี <a href="{{ route('register') }}"
                        class="text-decoration-none fw-bold">ลงทะเบียนที่นี่</a> หรือติดต่อผู้ดูแลระบบ</p>
            </div>
        </div>
    </div>
@endsection
