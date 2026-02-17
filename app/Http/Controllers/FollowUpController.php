<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\FollowUp;
use App\Constants\SMIConstants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class FollowUpController extends Controller
{
    public function create(Patient $patient)
    {
        $smiv_types = SMIConstants::SMIV_TYPES;
        $oas_options = SMIConstants::OAS_OPTIONS;
        $substances = SMIConstants::SUBSTANCES;
        $icd10 = SMIConstants::ICD10;

        return view('follow-ups.create', compact('patient', 'smiv_types', 'oas_options', 'substances', 'icd10'));
    }

    public function store(Request $request, Patient $patient)
    {
        $request->validate([
            'visit_date' => 'nullable', // using day/month/year components
            'oas_score' => 'required',
        ]);

        $data = $request->except(['visit_day', 'visit_month', 'visit_year', 'next_day', 'next_month', 'next_year']);
        $data['patient_id'] = $patient->id;
        $data['staff_name'] = Auth::user()->name;

        // Handle Visit Date
        if ($request->filled(['visit_day', 'visit_month', 'visit_year'])) {
            $year = (int)$request->visit_year - 543;
            $data['visit_date'] = "{$year}-{$request->visit_month}-{$request->visit_day}";
        } else {
            $data['visit_date'] = now()->toDateString();
        }

        // Handle Next Appointment Date
        if ($request->filled(['next_day', 'next_month', 'next_year'])) {
            $year = (int)$request->next_year - 543;
            $data['next_appointment_date'] = "{$year}-{$request->next_month}-{$request->next_day}";
        }

        FollowUp::create($data);

        // Update Patient current state
        $patient->update([
            'diagnosis' => $data['diagnosis'] ?? $patient->diagnosis,
            'smiv_group' => $data['smiv_group'] ?? $patient->smiv_group,
            'oas_score' => $data['oas_score'],
            'symp_mind' => $data['symp_mind'] ?? $patient->symp_mind,
            'symp_med' => $data['symp_med'] ?? $patient->symp_med,
            'symp_care' => $data['symp_care'] ?? $patient->symp_care,
            'symp_job' => $data['symp_job'] ?? $patient->symp_job,
            'symp_env' => $data['symp_env'] ?? $patient->symp_env,
            'symp_drug' => $data['symp_drug'] ?? $patient->symp_drug,
            'substances' => $data['substances'] ?? $patient->substances,
            'last_visit_date' => $data['visit_date'],
            'next_appointment_date' => $data['next_appointment_date'] ?? $patient->next_appointment_date,
            'status' => $data['appointment_plan'] == 'D/C' ? 'จำหน่าย' : 'ติดตามปกติ',
        ]);

        return redirect()->route('patients.show', $patient)->with('success', 'บันทึกการติดตามเรียบร้อย');
    }
}
