<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class VisitFee extends Model
{
    //

    const DEFAULT_FEE_DESCRIPTION = 'Consultation Fee';

    protected $fillable = [
        'fee_amount',
        'fee_description',
        'visit_id',
        'created_by',
    ];

    protected $casts = [
        'fee_amount' => 'decimal:2',
    ];

    public function visit()
    {
        return $this->belongsTo(Visit::class);
    }
}
