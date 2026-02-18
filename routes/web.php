<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\PatientController;
use App\Http\Controllers\FollowUpController;
use App\Http\Controllers\UserManagementController;
use App\Http\Controllers\ExportController;
use App\Http\Middleware\ApprovedMiddleware;
use App\Http\Middleware\AdminMiddleware;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Auth;

// Public Data
Route::get('/', [DashboardController::class, 'index'])->name('home');
Route::get('/repair-db', function () {
    if (Auth::check() && Auth::user()->role === 'admin') {
        $exitCode = Artisan::call('migrate', ['--force' => true]);
        return "Migration finished with code: " . $exitCode . "<br><pre>" . Artisan::output() . "</pre>";
    }
    return "Unauthorized";
})->name('repair.db');

Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');
Route::get('/map', [DashboardController::class, 'map'])->name('map');

// Auth
Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::get('/register', [AuthController::class, 'showRegister'])->name('register');
Route::post('/register', [AuthController::class, 'register']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

// Approved Users Only
Route::middleware(['auth', ApprovedMiddleware::class])->group(function () {
    Route::get('/patients/export', [ExportController::class, 'exportPatients'])->name('patients.export');
    Route::resource('patients', PatientController::class);
    Route::get('/patients/{patient}/follow-up', [FollowUpController::class, 'create'])->name('follow-ups.create');
    Route::post('/patients/{patient}/follow-up', [FollowUpController::class, 'store'])->name('follow-ups.store');
});

// Admin Only
Route::middleware(['auth', AdminMiddleware::class])->group(function () {
    Route::get('/admin/users', [UserManagementController::class, 'index'])->name('admin.users');
    Route::post('/admin/users/{user}/approve', [UserManagementController::class, 'approve'])->name('admin.users.approve');
    Route::delete('/admin/users/{user}', [UserManagementController::class, 'destroy'])->name('admin.users.destroy');
});

Route::get('/run-seed', function () {
    Artisan::call('db:seed', ['--force' => true]);
    return 'Seed done';
});

