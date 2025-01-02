<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PromoCodes extends Model
{
    use HasFactory;


    protected $fillable = [
        'code',
        'discount_value',
        'discount_type',
        'expiration_date',
        'usage_limit',
        'status',
        'usages'
    ];

    // Method to check if the promo code is valid
    public function isValid()
    {
        return $this->status === 'active' &&
            (!$this->expiration_date || now()->lte($this->expiration_date)) &&
            (!$this->usage_limit || $this->usages < $this->usage_limit);
    }
}
