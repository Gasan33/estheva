<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Blog extends Model
{
    use HasFactory;
    protected $fillable = ['title', 'content', 'short_description', 'slug', 'image', 'user_id'];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
