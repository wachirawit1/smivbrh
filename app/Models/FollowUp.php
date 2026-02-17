<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FollowUp extends Model
{
    use HasFactory;

    protected $fillable = [
        'patient_id',
        'diagnosis',
        'smiv_group',
        'oas_score',
        'symp_mind',
        'symp_med',
        'symp_care',
        'symp_job',
        'symp_env',
        'symp_drug',
        'substances',
        'visit_status',
        'visit_reason',
        'visit_date',
        'appointment_plan',
        'next_appointment_date',
        'staff_name'
    ];

    protected $casts = [
        'smiv_group' => 'json',
        'substances' => 'json',
        'visit_date' => 'date',
        'next_appointment_date' => 'date',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }
}
