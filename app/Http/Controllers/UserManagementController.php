<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class UserManagementController extends Controller
{
    public function index()
    {
        $users = User::where('role', '!=', 'admin')->orderBy('is_approved', 'asc')->orderBy('created_at', 'desc')->paginate(20);
        return view('admin.users', compact('users'));
    }

    public function approve(User $user)
    {
        $user->update(['is_approved' => true]);
        return back()->with('success', 'อนุมัติสมาชิกเรียบร้อย');
    }

    public function toggleAdmin(User $user)
    {
        $user->update(['role' => $user->role === 'admin' ? 'user' : 'admin']);
        return back()->with('success', 'เปลี่ยนบทบาทเรียบร้อย');
    }

    public function destroy(User $user)
    {
        $user->delete();
        return back()->with('success', 'ลบบัญชีเรียบร้อย');
    }
}
