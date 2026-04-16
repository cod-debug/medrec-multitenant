<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Inpatient extends Model
{
    use HasFactory;

    protected $fillable = [
        'full_name',
        'date_of_birth',
        'gender',
        'address',
        'phone_number',
        'age',
        'created_by'
    ];

    protected $casts = [
        'date_of_birth' => 'date',
    ];

    public function creator()
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function admissions()
    {
        return $this->hasMany(Admission::class);
    }
}
