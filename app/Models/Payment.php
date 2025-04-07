<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Payment extends Model
{
    protected $fillable = [
        'appointment_id',
        'amount',
        'payment_status',
        'payment_method',
        'card_last4',
        'card_brand',
        'card_exp_month',
        'card_exp_year',
    ];

    // Define the relationship with the Appointment model
    public function appointment()
    {
        return $this->belongsTo(Appointment::class);
    }
}
