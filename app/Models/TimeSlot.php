<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TimeSlot extends Model
{
    protected $fillable = ['doctor_id', 'treatment_id', 'date', 'start_time', 'end_time', 'is_available'];

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class, 'treatment_id');
    }
}
