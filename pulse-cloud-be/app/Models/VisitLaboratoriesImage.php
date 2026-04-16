<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitLaboratoriesImage extends Model
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

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
