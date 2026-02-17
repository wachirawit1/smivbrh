<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class ExportController extends Controller
{
    public function exportPatients()
    {
        $query = Patient::query();
        if (auth()->user()->role !== 'admin') {
            $query->where('area', auth()->user()->area);
        }

        $patients = $query->get();
        $csvHeader = ['HN', 'First Name', 'Last Name', 'Birth Date', 'Age', 'Gender', 'Phone', 'Area', 'Severity', 'Next Follow Up', 'Status'];

        $callback = function () use ($patients, $csvHeader) {
            $file = fopen('php://output', 'w');
            fputs($file, "\xEF\xBB\xBF"); // UTF-8 BOM
            fputcsv($file, $csvHeader);

            foreach ($patients as $p) {
                fputcsv($file, [
                    $p->hn,
                    $p->first_name,
                    $p->last_name,
                    $p->birth_date,
                    $p->age,
                    $p->gender,
                    $p->phone,
                    $p->area,
                    $p->severity,
                    $p->next_follow_up,
                    $p->status
                ]);
            }
            fclose($file);
        };

        return Response::stream($callback, 200, [
            "Content-type" => "text/csv",
            "Content-Disposition" => "attachment; filename=patients_export_" . date('Y-m-d') . ".csv",
            "Pragma" => "no-cache",
            "Cache-Control" => "must-revalidate, post-check=0, pre-check=0",
            "Expires" => "0"
        ]);
    }
}
