<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdmissionDiagnosis extends Model
{
    //
    protected $fillable = [
        'admission_id',
        'diagnosis',
        'remarks',
        'created_by'
    ];

    public function admission()
    {
        return $this->belongsTo(Admission::class);
    }
}
