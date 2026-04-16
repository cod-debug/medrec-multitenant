<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmissionTreatment extends Model
{
    //
    protected $fillable = [
        'admission_id',
        'treatment',
        'remarks',
        'created_by'
    ];

    public function admission()
    {
        return $this->belongsTo(Admission::class);
    }
}
