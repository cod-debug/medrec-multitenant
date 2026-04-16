<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitFindings extends Model
{
    //
    protected $fillable = [
        'visit_id',
        'finding',
        'finding_remarks',
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

    public function images()
    {
        return $this->hasMany(VisitFindingsImage::class);
    }
}
