<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class OperationSchedule extends Model
{
    //
    protected $fillable = [
        'doctor_id',
        'patient_id',
        'operation_type',
        'operating_room',
        'remarks',
        'diagnosis',
        'scheduled_at',
        'created_by'
    ];

    public function patient(){
        return $this->belongsTo(Patient::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
