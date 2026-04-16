<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitTreatment extends Model
{
    //
    protected $fillable = [
        'visit_id',
        'treatment',
        'treatment_remarks',
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
