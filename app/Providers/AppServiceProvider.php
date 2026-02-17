<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        \Illuminate\Support\Facades\View::composer('layout', function ($view) {
            if (\Illuminate\Support\Facades\Auth::check()) {
                $user = \Illuminate\Support\Facades\Auth::user();
                $area = $user->area;
                $isAdmin = $user->role === 'admin';

                $notifications = [];

                // 1. New Patients (Last 3 days)
                $newPatientsQuery = \App\Models\Patient::where('created_at', '>=', now()->subDays(3));
                if (!$isAdmin) $newPatientsQuery->where('area', $area);
                $newCount = $newPatientsQuery->count();
                if ($newCount > 0) {
                    $notifications[] = [
                        'text' => "📍 ผู้ป่วยใหม่ $newCount ราย (3 วันที่ผ่านมา)",
                        'icon' => 'fa-user-plus',
                        'color' => 'var(--primary)',
                        'url' => route('patients.index')
                    ];
                }

                // 2. Overdue patients
                $overdueQuery = \App\Models\Patient::where('next_appointment_date', '<', now()->toDateString())
                    ->where('status', '!=', 'จำหน่าย');
                if (!$isAdmin) $overdueQuery->where('area', $area);
                $overdueCount = $overdueQuery->count();
                if ($overdueCount > 0) {
                    $notifications[] = [
                        'text' => "⚠️ เกินกำหนดนัด $overdueCount ราย (สีม่วง)",
                        'icon' => 'fa-triangle-exclamation',
                        'color' => '#9b51e0',
                        'url' => route('patients.index', ['severity' => 'purple'])
                    ];
                }

                // 3. Upcoming (Tomorrow)
                $tomorrow = now()->addDay()->toDateString();
                $upcomingQuery = \App\Models\Patient::where('next_appointment_date', $tomorrow);
                if (!$isAdmin) $upcomingQuery->where('area', $area);
                $upcomingCount = $upcomingQuery->count();
                if ($upcomingCount > 0) {
                    $notifications[] = [
                        'text' => "🏥 มีนัดพรุ่งนี้ $upcomingCount ราย",
                        'icon' => 'fa-calendar-check',
                        'color' => '#28a745',
                        'url' => route('patients.index')
                    ];
                }

                $view->with('global_notifications', $notifications);
            } else {
                $view->with('global_notifications', []);
            }
        });
    }
}
