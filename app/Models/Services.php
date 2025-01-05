<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class Services extends Model
{

    protected $fillable = [
        'name',
        'description',
        'price',
        'images',
        'home_based',
        'video',
        'duration',
        'benefits',
        'discount_value',
        'discount_type',
        'service_sale_tag',
        'category_id',
        'doctors',
    ];

    protected $casts = [
        'images' => 'array',
        'benefits' => 'array',
        'doctors' => 'array',
    ];

    public function category()
    {
        return $this->belongsTo(Category::class);
    }

    public function image(): MorphOne
    {
        return $this->morphOne(Images::class, 'imageable');
    }

    public function doctors()
    {
        return $this->belongsToMany(Doctor::class, 'doctor_service', 'service_id', 'doctor_id');
    }

    public function timeSlots()
    {
        return $this->hasMany(TimeSlot::class, 'service_id');
    }



    public function appointments()
    {
        return $this->hasMany(Appointment::class);
    }
    public function medicalReports()
    {
        return $this->hasMany(MedicalReports::class);
    }

    public function getDiscountedPrice()
    {
        if ($this->discount_type == 'percentage') {
            // Calculate discount as percentage
            return $this->price - ($this->price * ($this->discount_value / 100));
        } elseif ($this->discount_type == 'fixed') {
            // Calculate discount as a fixed amount
            return $this->price - $this->discount_value;
        }

        return $this->price; // No discount applied
    }

    public function reviews()
    {
        return $this->hasMany(Review::class, "service_id");
    }

}
