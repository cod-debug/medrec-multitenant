<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patient extends Model
{
    //
    protected $fillable = [
        'doctor_id',
        'full_name',
        'date_of_birth',
        'gender',
        'address',
        'phone_number',
        'created_by',
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function visits()
    {
        return $this->hasMany(Visit::class);
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
