<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admission extends Model
{
    //
    
    public const ADMISSION_STATUS_ADMITTED = 'admitted';
    public const ADMISSION_STATUS_DISCHARGED = 'discharged';

    protected $fillable = [
        'inpatient_id',
        'admission_date',
        'referred_by',
        'referred_by_hospital',
        'reason_for_admission',
        'admission_status',
        'admission_fee',
        'discharge_date',
        'room_number',
        'created_by'
    ];

    public function patient()
    {
        return $this->belongsTo(Inpatient::class, 'inpatient_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function diagnoses()
    {
        return $this->hasMany(AdmissionDiagnosis::class);
    }

    public function treatments()
    {
        return $this->hasMany(AdmissionTreatment::class);
    }
}
