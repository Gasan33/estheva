<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\MorphOne;

class categories extends Model
{
    protected $fillable = [
        'name',
        'slug',
        'description',
        'visibility',
        'parent_id'
    ];

    protected $casts = [
        'visibility' => 'boolean'
    ];

    public function parent(): BelongsTo
    {
        return $this->belongsTo(Categories::class);
    }

    public function children(): HasMany
    {
        return $this->hasMany(Categories::class);
    }

    public function image(): MorphOne
    {
        return $this->morphOne(Images::class, 'imageable');
    }
}
