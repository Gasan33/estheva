<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Advertisements extends Model
{
    protected $fillable = [
        'service_id',
        'ad_title',
        'ad_description',
        'ad_picture',
        'start_date',
        'end_date',
    ];

    public function service()
    {
        return $this->belongsTo(Services::class);
    }
}
