<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Visit extends Model
{
    //
    public const STATUS_QUEUED = 'queued';
    public const STATUS_CHECKING = 'checking';
    public const STATUS_FOR_PAYMENT = 'for-payment';
    public const STATUS_SETTLED = 'settled';
    public const STATUS_CANCELLED = 'cancelled';

    protected $fillable = [
        'doctor_id',
        'patient_id',
        'visit_date',
        'reason_for_visit',
        'notes',
        'visit_status',
        'created_by',
    ];

    public function patient()
    {
        return $this->belongsTo(Patient::class);
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function complaints()
    {
        return $this->hasMany(VisitComplaint::class);
    }

    public function findings()
    {
        return $this->hasMany(VisitFindings::class);
    }

    public function treatments()
    {
        return $this->hasMany(VisitTreatment::class);
    }

    public function laboratories()
    {
        return $this->hasMany(VisitLaboratory::class);
    }
    
    public function laboratoriesImages()
    {
        return $this->hasMany(VisitLaboratoriesImage::class);
    }

    public function diagnoses(){
        return $this->hasMany(VisitDiagnosis::class);
    }

    public function visitFees(){
        return $this->hasMany(VisitFee::class);
    }

    public function findingsImages(){
        return $this->hasMany(VisitFindingsImage::class);
    }

    public function prescriptions(){
        return $this->hasMany(VisitPrescription::class);
    }

    public function referrals(){
        return $this->hasMany(VisitReferral::class);
    }
}
