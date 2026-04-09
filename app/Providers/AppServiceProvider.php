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
        // Force HTTPS in production (for Render.com)
        if (app()->environment('production')) {
            \URL::forceScheme('https');
        }

        \Illuminate\Support\Facades\View::composer('layout', function ($view) {
            if (\Illuminate\Support\Facades\Auth::check()) {
                $user = \Illuminate\Support\Facades\Auth::user();
                $area = $user->area;
                $isAdmin = $user->role === 'admin';

                // --- 1. Overdue Patients ---
                $overdueQuery = \App\Models\Patient::where('next_appointment_date', '<', now()->toDateString())
                    ->whereNotNull('next_appointment_date');
                if (!$isAdmin) $overdueQuery->where('amphoe', $area);
                $overduePatients = $overdueQuery->limit(50)->get();

                // --- 2. Today's Patients ---
                $todayQuery = \App\Models\Patient::whereDate('next_appointment_date', now()->toDateString());
                if (!$isAdmin) $todayQuery->where('amphoe', $area);
                $todayPatients = $todayQuery->limit(50)->get();

                // --- 3. Upcoming Patients (Next 3 days, excluding today) ---
                $upcomingQuery = \App\Models\Patient::whereBetween('next_appointment_date', [
                    now()->addDay()->toDateString(),
                    now()->addDays(3)->toDateString()
                ]);
                if (!$isAdmin) $upcomingQuery->where('amphoe', $area);
                $upcomingPatients = $upcomingQuery->limit(50)->get();

                // Prototype Summary Text: "เกินกำหนด X ราย | นัดวันนี้ Y ราย | ล่วงหน้า(3วัน) Z ราย"
                $summary = sprintf(
                    "เกินกำหนด %d ราย | นัดวันนี้ %d ราย | ล่วงหน้า(3วัน) %d ราย",
                    count($overduePatients),
                    count($todayPatients),
                    count($upcomingPatients)
                );

                $view->with([
                    'global_overdue' => $overduePatients,
                    'global_today' => $todayPatients,
                    'global_upcoming' => $upcomingPatients,
                    'global_alert_summary' => $summary,
                    'has_alerts' => (count($overduePatients) + count($todayPatients) + count($upcomingPatients)) > 0
                ]);
            } else {
                $view->with([
                    'global_overdue' => collect(),
                    'global_today' => collect(),
                    'global_upcoming' => collect(),
                    'global_alert_summary' => '',
                    'has_alerts' => false
                ]);
            }
        });
    }
}
