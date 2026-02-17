@extends('layout')

@section('content')
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h3 class="fw-bold text-primary m-0"><i class="fas fa-user-shield me-2"></i>จัดการสมาชิกและอนุมัติสิทธิ์</h3>
    </div>

    <div class="card-3d p-4">
        <div class="table-responsive">
            <table class="table table-hover align-middle">
                <thead class="table-light">
                    <tr>
                        <th>ชื่อ-นามสกุล</th>
                        <th>เบอร์โทร</th>
                        <th>พื้นที่รับผิดชอบ</th>
                        <th>สถานะ</th>
                        <th class="text-center">การจัดการ</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach ($users as $user)
                        <tr>
                            <td>
                                <div class="fw-bold">{{ $user->prefix }}{{ $user->name }}</div>
                                <small class="text-muted">ID: {{ $user->username ?: $user->email }}</small>
                            </td>
                            <td>{{ $user->phone }}</td>
                            <td><span class="badge bg-light text-primary border">{{ $user->area }}</span></td>
                            <td>
                                @if ($user->is_approved)
                                    <span class="badge bg-success rounded-pill px-3">อนุมัติแล้ว</span>
                                @else
                                    <span
                                        class="badge bg-warning text-dark rounded-pill px-3 shadow-sm blink">รออนุมัติ</span>
                                @endif
                            </td>
                            <td class="text-center">
                                <div class="btn-group gap-2">
                                    @if (!$user->is_approved)
                                        <form action="{{ route('admin.users.approve', $user) }}" method="POST">
                                            @csrf
                                            <button type="submit"
                                                class="btn btn-sm btn-success rounded-pill px-3 fw-bold shadow-sm">
                                                <i class="fas fa-check me-1"></i> อนุมัติ
                                            </button>
                                        </form>
                                    @endif
                                    <form action="{{ route('admin.users.destroy', $user) }}" method="POST"
                                        onsubmit="return confirm('ยืนยันการลบบัญชีนี้?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger rounded-pill px-3">
                                            <i class="fas fa-trash-alt me-1"></i> ลบ
                                        </button>
                                    </form>
                                </div>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
        <div class="mt-4 d-flex justify-content-center">
            {{ $users->links() }}
        </div>
    </div>
@endsection
