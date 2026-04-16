<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitLaboratory extends Model
{
    //
    protected $fillable = [
        'visit_id',
        'laboratory_test',
        'laboratory_remarks',
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
