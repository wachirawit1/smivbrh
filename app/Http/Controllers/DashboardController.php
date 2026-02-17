<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\FollowUp;
use App\Constants\SMIConstants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class DashboardController extends Controller
{
    public function index(Request $request)
    {
        $areaFilter = $request->get('area');
        $oasFilter = $request->get('oas_score');
        $smivFilter = $request->get('smiv_group');

        // Base stats for the OAS cards (using oas_score)
        $stats = Patient::select('oas_score', DB::raw('count(*) as total'))
            ->when($areaFilter, fn($q) => $q->where('amphoe', $areaFilter))
            ->when($smivFilter, fn($q) => $q->whereJsonContains('smiv_group', $smivFilter))
            ->when($oasFilter, fn($q) => $q->where('oas_score', $oasFilter))
            ->groupBy('oas_score')
            ->pluck('total', 'oas_score')
            ->toArray();

        // Handle Status: เกินกำหนดนัด (Overdue)
        $overdueCount = Patient::where('status', 'เกินกำหนดนัด')
            ->when($areaFilter, fn($q) => $q->where('amphoe', $areaFilter))
            ->count();
        $stats['purple'] = $overdueCount;

        // Handle Status: มาตามนัด (Scheduled/Normal Tracking)
        $scheduledCount = Patient::where('status', 'ติดตามปกติ')
            ->when($areaFilter, fn($q) => $q->where('amphoe', $areaFilter))
            ->count();
        $stats['scheduled'] = $scheduledCount;

        // SMI-V Stats for Bar Chart (Need to iterate constants since JSON)
        $smivTypes = SMIConstants::SMIV_TYPES;
        $smivStats = [];
        foreach ($smivTypes as $key => $name) {
            $smivStats[$key] = Patient::whereJsonContains('smiv_group', $key)
                ->when($areaFilter, fn($q) => $q->where('amphoe', $areaFilter))
                ->when($oasFilter, fn($q) => $q->where('oas_score', $oasFilter))
                ->count();
        }

        // Display Names mapping for UI
        $oasNames = [
            '3' => 'ฉุกเฉิน (OAS-3)',
            '2' => 'เร่งด่วน (OAS-2)',
            '1' => 'กึ่งเร่งด่วน (OAS-1)',
            '0' => 'ปกติ (OAS-0)',
            'purple' => 'เกินกำหนดนัด'
        ];

        $smivNames = $smivTypes;

        $areas = SMIConstants::AMPHOES;

        // Calculate Improvement Stats (OAS-0 patients vs Total patients visited in period)
        $total3m = Patient::when($areaFilter, fn($q) => $q->where('amphoe', $areaFilter))
            ->where('last_visit_date', '>=', now()->subMonths(3))
            ->count();
        $improvement3m = $total3m > 0 ? round((Patient::when($areaFilter, fn($q) => $q->where('amphoe', $areaFilter))->where('last_visit_date', '>=', now()->subMonths(3))->where('oas_score', '0')->count() / $total3m) * 100) : 0;

        $total6m = Patient::when($areaFilter, fn($q) => $q->where('amphoe', $areaFilter))
            ->where('last_visit_date', '>=', now()->subMonths(6))
            ->count();
        $improvement6m = $total6m > 0 ? round((Patient::when($areaFilter, fn($q) => $q->where('amphoe', $areaFilter))->where('last_visit_date', '>=', now()->subMonths(6))->where('oas_score', '0')->count() / $total6m) * 100) : 0;

        // Stats by area for map
        $statsByArea = Patient::select('amphoe', 'oas_score', DB::raw('count(*) as total'))
            ->when($areaFilter, fn($q) => $q->where('amphoe', $areaFilter))
            ->when($smivFilter, fn($q) => $q->whereJsonContains('smiv_group', $smivFilter))
            ->groupBy('amphoe', 'oas_score')
            ->get()
            ->groupBy('amphoe');

        return view('dashboard', compact(
            'stats',
            'smivStats',
            'areas',
            'areaFilter',
            'oasFilter',
            'smivFilter',
            'oasNames',
            'smivNames',
            'statsByArea',
            'improvement3m',
            'improvement6m'
        ));
    }

    public function map()
    {
        $areas = SMIConstants::AREAS;
        $statsByArea = Patient::select('area', 'oas_score', DB::raw('count(*) as total'))
            ->groupBy('area', 'oas_score')
            ->get()
            ->groupBy('area');

        return view('map', compact('areas', 'statsByArea'));
    }
}
