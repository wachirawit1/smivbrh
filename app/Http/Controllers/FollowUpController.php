<?php

namespace App\Http\Controllers;

use App\Constants\SMIConstants;
use App\Models\FollowUp;
use App\Models\Patient;
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
        $appointmentPlan = $request->input('appointment_plan');
        $requiresNextAppointment = $appointmentPlan === 'นัดต่อเนื่อง';

        $rules = [
            'visit_status' => 'required',
            'appointment_plan' => 'required',
            'oas_score' => 'required',
            'visit_reason' => 'nullable|string|max:255',
            'details' => 'nullable|string',
        ];

        if ($requiresNextAppointment) {
            $rules['next_day'] = 'required';
            $rules['next_month'] = 'required';
            $rules['next_year'] = 'required';
        }

        $request->validate($rules, [
            'visit_status.required' => 'กรุณาเลือกสถานะการมา',
            'appointment_plan.required' => 'กรุณาเลือกแผนการดูแลต่อเนื่อง',
            'oas_score.required' => 'กรุณาเลือก OAS Score',
            'next_day.required' => 'กรุณาเลือกวันนัดครั้งถัดไป',
            'next_month.required' => 'กรุณาเลือกเดือนนัดครั้งถัดไป',
            'next_year.required' => 'กรุณาเลือกปีนัดครั้งถัดไป',
        ]);

        $data = $request->except(['visit_day', 'visit_month', 'visit_year', 'next_day', 'next_month', 'next_year', 'substance_other', 'appointment_plan_other']);
        $data['patient_id'] = $patient->id;
        $data['staff_name'] = trim((Auth::user()->name ?? '') ?: ((Auth::user()->fname ?? '') . ' ' . (Auth::user()->lname ?? '')));
        $data['visit_reason'] = $request->filled('visit_reason') ? trim($request->visit_reason) : null;

        // Process "Other" Substances
        if ($request->filled('substances') && is_array($request->substances)) {
            $subs = $request->substances;
            if (in_array('อื่นๆ', $subs) && $request->filled('substance_other')) {
                $subs = array_filter($subs, fn($v) => $v !== 'อื่นๆ');
                $subs[] = 'อื่นๆ: ' . trim($request->substance_other);
            }
            $data['substances'] = array_values($subs);
        }

        // Process "Other" Appointment Plan
        if ($appointmentPlan === 'อื่นๆ' && $request->filled('appointment_plan_other')) {
            $appointmentPlan = 'อื่นๆ: ' . trim($request->appointment_plan_other);
            $data['appointment_plan'] = $appointmentPlan;
        }

        if ($request->filled(['visit_day', 'visit_month', 'visit_year'])) {
            $year = (int) $request->visit_year - 543;
            $data['visit_date'] = "{$year}-{$request->visit_month}-{$request->visit_day}";
        } else {
            $data['visit_date'] = now()->toDateString();
        }

        $nextDate = null;
        if ($requiresNextAppointment && $request->filled(['next_day', 'next_month', 'next_year'])) {
            $year = (int) $request->next_year - 543;
            $nextDate = "{$year}-{$request->next_month}-{$request->next_day}";
            $data['next_appointment_date'] = $nextDate;
        } else {
            $data['next_appointment_date'] = null;
        }

        FollowUp::create($data);

        $isDischarged = in_array($appointmentPlan, ['จำหน่าย (D/C)', 'D/C', 'จำหน่าย'], true);
        $newStatus = $isDischarged ? 'จำหน่าย' : 'ติดตามปกติ';

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
            'next_appointment_date' => $isDischarged ? null : ($nextDate ?? $patient->next_appointment_date),
            'status' => $newStatus,
        ]);

        return redirect()->route('patients.show', $patient)->with('success', 'บันทึกการติดตามเรียบร้อยแล้ว');
    }
}
