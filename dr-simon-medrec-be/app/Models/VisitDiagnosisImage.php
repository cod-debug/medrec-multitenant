<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitDiagnosisImage extends Model
{
    //
    protected $fillable = [
        'visit_id',
        'image_path',
        'created_by',
    ];
    public function visit()
    {
        return $this->belongsTo(Visit::class, 'visit_id');
    }
}
