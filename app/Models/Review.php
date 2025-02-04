<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Review extends Model
{
    protected $fillable = ['patient_id', 'doctor_id', 'treatment_id', 'rating', 'review_text'];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class, "doctor_id");
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class, "treatment_id");
    }
}
