<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphOne;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Treatment extends Model
{
    protected $fillable = [
        'title',
        'short_description',
        'description',
        'price',
        'images',
        'home_based',
        'video',
        'duration',
        'benefits',
        'instructions',
        'discount_value',
        'discount_type',
        'treatment_sale_tag',
        'category_id',
    ];

    protected $casts = [
        'images' => 'array',
        'benefits' => 'array',
        'instructions' => 'array',
    ];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function image(): MorphOne
    {
        return $this->morphOne(Images::class, 'imageable');
    }

    public function doctors(): BelongsToMany
    {
        return $this->belongsToMany(Doctor::class, 'doctor_treatments', 'treatment_id', 'doctor_id')->withTimestamps();
    }

    public function timeSlots(): HasMany
    {
        return $this->hasMany(TimeSlot::class);
    }

    public function appointments(): HasMany
    {
        return $this->hasMany(Appointment::class);
    }

    public function medicalReports(): HasMany
    {
        return $this->hasMany(MedicalReports::class);
    }

    public function reviews(): HasMany
    {
        return $this->hasMany(Review::class, 'treatment_id');
    }

    public function getDiscountedPrice(): float
    {
        if ($this->discount_type === 'percentage' && $this->discount_value > 0) {
            return max(0, $this->price - ($this->price * ($this->discount_value / 100)));
        } elseif ($this->discount_type === 'fixed' && $this->discount_value > 0) {
            return max(0, $this->price - $this->discount_value);
        }

        return $this->price; // No discount applied
    }
}
