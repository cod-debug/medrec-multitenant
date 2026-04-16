<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitPrescription extends Model
{
    //
    protected $fillable = [
        'visit_id',
        'medicine_generic',
        'medicine_brand',
        'dosage',
        'reminder',
        'duration',
        'quantity',
        'quantity_prefix',
        'remarks',
        'is_remarks_only',
        'created_by',
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
