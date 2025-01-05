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
        'availability',

    ];

    protected $casts = [
        'availability' => 'array',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }


    public function setAvailabilityAttribute($value)
    {
        $this->attributes['availability'] = json_encode($value); // Store as JSON
    }

    // This will make sure the availability is cast to an array automatically when accessed
    public function getAvailabilityAttribute($value)
    {
        return json_decode($value, true); // Ensure it's an array or you can customize this for specific formatting
    }
    public function services()
    {
        return $this->belongsToMany(Services::class, 'doctor_service', 'doctor_id', 'service_id');
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
