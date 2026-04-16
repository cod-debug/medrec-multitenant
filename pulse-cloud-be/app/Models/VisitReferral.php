<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitReferral extends Model
{
    //
    protected $fillable = [
        'description',
        'visit_id'
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
}
