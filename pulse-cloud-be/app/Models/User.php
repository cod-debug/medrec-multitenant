<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasApiTokens;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */

    const USER_ROLE_DOCTOR = 1;
    const USER_ROLE_SECRETARY = 2;
    
    protected $fillable = [
        'name',
        'email',
        'username',
        'password',
        'level_of_authorization',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get all doctors that this assistant works for.
     * Use when the current user is an assistant.
     */
    public function doctors()
    {
        return $this->belongsToMany(
            User::class,
            'assistant_doctor_pivots',
            'assistant_id',
            'doctor_id'
        )->withTimestamps();
    }

    /**
     * Get all assistants that work under this doctor.
     * Use when the current user is a doctor.
     */
    public function secretaries()
    {
        return $this->belongsToMany(
            User::class,
            'assistant_doctor_pivots',
            'doctor_id',
            'assistant_id'
        )->withTimestamps();
    }
}
