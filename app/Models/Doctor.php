<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Doctor extends Model
{
    protected $table = 'doctors';
    protected $fillable = [
        'user_id',
        'specialty',
        'certificate',
        'university',
        'patients',
        'exp',
        'about',
        'home_based',
        'online_consultation'
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function treatments()
    {
        return $this->belongsToMany(Treatment::class, 'doctor_treatments', 'doctor_id', 'treatment_id')->withTimestamps();
    }

    public function addresses()
    {
        return $this->morphMany(Addresses::class, 'addressable');
    }

    public function scopeDoctors($query)
    {
        return $query->whereHas('user', function ($q) {
            $q->where('role', 'doctor');
        });
    }

    public function availabilities()
    {
        return $this->hasMany(Availability::class, 'doctor_id');
    }

    public function appointments()
    {
        return $this->hasMany(Appointment::class, 'doctor_id');
    }

    public function medicalReports()
    {
        return $this->hasMany(MedicalReports::class, 'doctor_id');
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, 'doctor_id');
    }
}