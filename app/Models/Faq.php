<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Str;
class Faq extends Model
{
    use HasFactory;

    protected $fillable = ['title', 'slug', 'answer', 'content', 'is_active', 'order'];

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($faq) {
            $faq->slug = Str::slug($faq->title);
        });
    }
}
