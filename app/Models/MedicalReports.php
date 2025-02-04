<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class MedicalReports extends Model
{
    protected $fillable = [
        'patient_id',
        'doctor_id',
        'treatment_id',
        'report_date',
        'report_details',
        'attachments'
    ];

    protected $casts = [
        'attachments' => 'array',
    ];

    public function patient()
    {
        return $this->belongsTo(User::class, 'patient_id');
    }

    public function doctor()
    {
        return $this->belongsTo(Doctor::class);
    }

    public function treatment()
    {
        return $this->belongsTo(Treatment::class);
    }
}
