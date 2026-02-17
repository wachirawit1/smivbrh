<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Constants\SMIConstants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(Request $request)
    {
        $credentials = $request->validate([
            'login_id' => ['required'], // Can be email or username
            'password' => ['required'],
        ]);

        $loginField = filter_var($request->login_id, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';

        if (Auth::attempt([$loginField => $request->login_id, 'password' => $request->password])) {
            $request->session()->regenerate();

            if (!Auth::user()->is_approved) {
                Auth::logout();
                return back()->with('error', 'บัญชีของคุณรอการอนุมัติจาก Admin');
            }

            return redirect()->intended('dashboard');
        }

        return back()->withErrors(['login_id' => 'ข้อมูลประจำตัวไม่ถูกต้อง'])->onlyInput('login_id');
    }

    public function showRegister()
    {
        $areas = SMIConstants::AREAS;
        $amphoes = SMIConstants::AMPHOES;
        return view('auth.register', compact('areas', 'amphoes'));
    }

    public function register(Request $request)
    {
        $request->validate([
            'prefix' => 'required',
            'fname' => 'required|string|max:255',
            'lname' => 'required|string|max:255',
            'username' => 'required|string|min:4|unique:users',
            'area' => 'required',
            'amphoe' => 'required',
            'password' => 'required|string|min:6|confirmed',
        ]);

        User::create([
            'prefix' => $request->prefix,
            'fname' => $request->fname,
            'lname' => $request->lname,
            'username' => $request->username,
            'area' => $request->area,
            'amphoe' => $request->amphoe,
            'password' => Hash::make($request->password),
            'role' => 'user',
            'is_approved' => false,
        ]);

        return redirect()->route('login')->with('success', 'ลงทะเบียนสำเร็จ โปรดรอ Admin อนุมัติ');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('login');
    }
}
