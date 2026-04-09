<?php

namespace App\Http\Controllers;

use App\Models\Patient;
use App\Models\FollowUp;
use App\Constants\SMIConstants;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Schema;

class PatientController extends Controller
{
    public function index(Request $request)
    {
        $query = Patient::query();

        // Security: Non-admins only see patients in their area
        if (Auth::user()->role !== 'admin') {
            $query->where('area', Auth::user()->area);
        }

        if ($request->filled('area') && Auth::user()->role === 'admin') {
            $query->where('area', $request->area);
        }

        if ($request->filled('search')) {
            $query->search($request->search);
        }

        if ($request->filled('oas_score')) {
            $query->where('oas_score', $request->oas_score);
        }

        if ($request->filled('smiv_group')) {
            $query->whereJsonContains('smiv_group', $request->smiv_group);
        }

        // Logic for Purple (Auto-update)
        Patient::where('next_appointment_date', '<', now()->toDateString())
            ->where('status', '!=', 'จำหน่าย')
            ->update(['status' => 'เกินกำหนดนัด']);

        $patients = $query->orderBy('created_at', 'desc')->paginate(15);
        $areas = SMIConstants::AREAS;

        return view('patients.index', compact('patients', 'areas'));
    }

    public function create()
    {
        $areas = SMIConstants::AREAS;
        return view('patients.create', compact('areas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'cid' => [
                'required',
                'digits:13',
                'unique:patients',
                function ($attribute, $value, $fail) {
                    if (strlen($value) != 13) return $fail('สิทธิ์การกรอกเลขบัตรประชาชนต้องมี 13 หลัก');
                    $sum = 0;
                    for ($i = 0; $i < 12; $i++) {
                        $sum += (int)($value[$i]) * (13 - $i);
                    }
                    if ((11 - ($sum % 11)) % 10 != (int)($value[12])) {
                        $fail('เลขบัตรประชาชนไม่ถูกต้องตามหลักการตรวจสอบ');
                    }
                },
            ],
            'phone' => 'nullable|regex:/^0[0-9]{9}$/',
            'relative_phone' => 'nullable|regex:/^0[0-9]{9}$/',
            'area' => 'required',
        ], [
            'cid.digits' => 'เลขบัตรประชาชนต้องมี 13 หลัก',
            'cid.unique' => 'เลขบัตรประชาชนนี้มีในระบบแล้ว',
            'phone.regex' => 'เบอร์โทรศัพท์ต้องมี 10 หลัก และขึ้นต้นด้วย 0',
            'relative_phone.regex' => 'เบอร์โทรศัพท์ญาติต้องมี 10 หลัก และขึ้นต้นด้วย 0',
        ]);

        $data = $request->except(['birth_day', 'birth_month', 'birth_year', 'visit_day', 'visit_month', 'visit_year', 'next_day', 'next_month', 'next_year', 'substance_other']);

        // Process "Other" Substances
        if ($request->filled('substances') && is_array($request->substances)) {
            $subs = $request->substances;
            if (in_array('อื่นๆ', $subs) && $request->filled('substance_other')) {
                $subs = array_filter($subs, fn($v) => $v !== 'อื่นๆ');
                $subs[] = 'อื่นๆ: ' . trim($request->substance_other);
            }
            $data['substances'] = array_values($subs);
        }

        // Handle Birth Date (Thai Year - 543)
        if ($request->filled(['birth_day', 'birth_month', 'birth_year'])) {
            $year = (int)$request->birth_year - 543;
            $data['birth_date'] = "{$year}-{$request->birth_month}-{$request->birth_day}";
            $data['age'] = \Carbon\Carbon::parse($data['birth_date'])->age;
        }

        // Handle Last Visit Date
        if ($request->filled(['visit_day', 'visit_month', 'visit_year'])) {
            $year = (int)$request->visit_year - 543;
            $data['last_visit_date'] = "{$year}-{$request->visit_month}-{$request->visit_day}";
        }

        // Handle Next Appointment Date
        if ($request->filled(['next_day', 'next_month', 'next_year'])) {
            $year = (int)$request->next_year - 543;
            $data['next_appointment_date'] = "{$year}-{$request->next_month}-{$request->next_day}";
        }

        $data['status'] = $data['status'] ?? 'ติดตามปกติ';

        Patient::create($data);

        return redirect()->route('patients.index')->with('success', 'ลงทะเบียนผู้ป่วยใหม่เรียบร้อย');
    }

    public function show(Patient $patient)
    {
        $history = $patient->followUps()->orderBy('visit_date', 'desc')->get();
        return view('patients.show', compact('patient', 'history'));
    }

    public function edit(Patient $patient)
    {
        $areas = SMIConstants::AREAS;
        return view('patients.edit', compact('patient', 'areas'));
    }

    public function update(Request $request, Patient $patient)
    {
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'cid' => [
                'required',
                'digits:13',
                'unique:patients,cid,' . $patient->id,
                function ($attribute, $value, $fail) {
                    $sum = 0;
                    for ($i = 0; $i < 12; $i++) {
                        $sum += (int)($value[$i]) * (13 - $i);
                    }
                    if ((11 - ($sum % 11)) % 10 != (int)($value[12])) {
                        $fail('เลขบัตรประชาชนไม่ถูกต้องตามหลักการตรวจสอบ');
                    }
                },
            ],
            'phone' => 'nullable|regex:/^0[0-9]{9}$/',
            'relative_phone' => 'nullable|regex:/^0[0-9]{9}$/',
            'area' => 'required',
        ], [
            'cid.digits' => 'เลขบัตรประชาชนต้องมี 13 หลัก',
            'cid.unique' => 'เลขบัตรประชาชนนี้มีในระบบแล้ว',
            'phone.regex' => 'เบอร์โทรศัพท์ต้องมี 10 หลัก และขึ้นต้นด้วย 0',
            'relative_phone.regex' => 'เบอร์โทรศัพท์ญาติต้องมี 10 หลัก และขึ้นต้นด้วย 0',
        ]);

        $data = $request->except(['birth_day', 'birth_month', 'birth_year', 'visit_day', 'visit_month', 'visit_year', 'next_day', 'next_month', 'next_year', 'substance_other']);

        // Process "Other" Substances
        if ($request->filled('substances') && is_array($request->substances)) {
            $subs = $request->substances;
            if (in_array('อื่นๆ', $subs) && $request->filled('substance_other')) {
                $subs = array_filter($subs, fn($v) => $v !== 'อื่นๆ');
                $subs[] = 'อื่นๆ: ' . trim($request->substance_other);
            }
            $data['substances'] = array_values($subs);
        }

        if ($request->filled(['birth_day', 'birth_month', 'birth_year'])) {
            $year = (int)$request->birth_year - 543;
            $data['birth_date'] = "{$year}-{$request->birth_month}-{$request->birth_day}";
            $data['age'] = \Carbon\Carbon::parse($data['birth_date'])->age;
        }

        if ($request->filled(['visit_day', 'visit_month', 'visit_year'])) {
            $year = (int)$request->visit_year - 543;
            $data['last_visit_date'] = "{$year}-{$request->visit_month}-{$request->visit_day}";
        }

        if ($request->filled(['next_day', 'next_month', 'next_year'])) {
            $year = (int)$request->next_year - 543;
            $data['next_appointment_date'] = "{$year}-{$request->next_month}-{$request->next_day}";
        }

        $patient->update($data);
        return redirect()->route('patients.index')->with('success', 'แก้ไขข้อมูลเรียบร้อย');
    }

    public function destroy(Patient $patient)
    {
        if (Auth::user()->role !== 'admin') abort(403);
        $patient->delete();
        return redirect()->route('patients.index')->with('success', 'ลบข้อมูลสำเร็จ');
    }
}
