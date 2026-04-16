<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Settings extends Model
{
    //
    protected $fillable = [
        'data',
        'updated_by',
    ];

    protected $casts = [
        'data' => 'json',
    ];

    public function updatedBy(){
        return $this->belongsTo(User::class, 'updated_by');
    }
}
