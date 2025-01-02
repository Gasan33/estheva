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
        'day_of_week',
        'start_time',
        'end_time',
    ];

    // protected $casts = [
    //     'start_time' => 'time',
    //     'end_time' => 'time',
    // ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    // public function services()
    // {
    //     return $this->belongsToMany(Services::class, 'doctor_services');
    // }

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

    public function availability()
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
