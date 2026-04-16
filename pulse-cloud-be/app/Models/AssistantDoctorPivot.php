<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AssistantDoctorPivot extends Model
{
    protected $table = 'assistant_doctor_pivots';

    protected $fillable = [
        'assistant_id',
        'doctor_id',
    ];

    /**
     * Get the assistant user.
     */
    public function assistant()
    {
        return $this->belongsTo(User::class, 'assistant_id');
    }

    /**
     * Get the doctor user.
     */
    public function doctor()
    {
        return $this->belongsTo(User::class, 'doctor_id');
    }
}
