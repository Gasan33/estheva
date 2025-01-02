<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Addresses extends Model
{
    protected $fillable = [
        'user_id',
        'addressable_id',
        'addressable_type',
        'address_line_1',
        'address_line_2',
        'city',
        'state',
        'postal_code',
        'country',
        'latitude',
        'longitude',
        'is_primary',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function addressable()
    {
        return $this->morphTo();
    }
}
