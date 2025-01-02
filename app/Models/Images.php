<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\MorphTo;

class Images extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function getPathAttribute($value)
    {
        return url($value);
    }

    public function imageable(): MorphTo
    {
        return $this->morphTo();
    }
}
