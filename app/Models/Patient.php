<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

use App\Traits\HashableId;

class Patient extends Model
{
    use HasFactory, HashableId;

    protected $fillable = [
        'prefix',
        'first_name',
        'last_name',
        'cid',
        'birth_date',
        'age',
        'gender',
        'phone',
        'address',
        'tambon',
        'amphoe',
        'area',
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
        'last_visit_date',
        'next_appointment_date',
        'status'
    ];

    protected $casts = [
        'smiv_group' => 'json',
        'substances' => 'json',
        'birth_date' => 'date',
        'last_visit_date' => 'date',
        'next_appointment_date' => 'date',
    ];

    public function getFullnameAttribute()
    {
        return "{$this->first_name} {$this->last_name}";
    }

    public function followUps()
    {
        return $this->hasMany(FollowUp::class);
    }

    public function scopeSearch($query, $term)
    {
        if ($term) {
            $query->where(function ($q) use ($term) {
                $q->where('first_name', 'LIKE', "%{$term}%")
                    ->orWhere('last_name', 'LIKE', "%{$term}%")
                    ->orWhere('cid', 'LIKE', "%{$term}%");
            });
        }
    }
}
