<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Rewards extends Model
{
    protected $fillable = [
        'user_id',
        'points',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
