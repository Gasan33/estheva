<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'first_name',
        'last_name',
        'email',
        'phone_number',
        'password',
        'role',
        'weight',
        'date_of_birth',
        'gender',
        'nationality',
        'profile_picture',
        'device_token'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected function profilePictureUrl(): Attribute
    {
        return Attribute::get(function () {
            return $this->profile_picture
                ? asset("storage/{$this->profile_picture}")
                : asset('user-avatar.png'); // Path to your default avatar
        });
    }

    public function getAgeAttribute()
    {
        return Carbon::parse($this->date_of_birth)->age;
    }

    public function getPhoneAttribute($value)
    {
        // Ensure the phone number is formatted as: +971 XX XXX XXXX
        $formatted = preg_replace('/\D/', '', $value); // Remove non-numeric characters
        if (strlen($formatted) === 12 && substr($formatted, 0, 3) === '971') {
            // Format numbers with +971 country code
            return '+971 ' . substr($formatted, 3, 2) . ' ' . substr($formatted, 5, 3) . ' ' . substr($formatted, 8);
        } elseif (strlen($formatted) === 9 && substr($formatted, 0, 1) === '5') {
            // Format numbers without country code as UAE local format
            return '+971 ' . substr($formatted, 0, 1) . ' ' . substr($formatted, 1, 3) . ' ' . substr($formatted, 4);
        }
        return $value; // Return unformatted if it doesn't match
    }

    // Define the mutator for the phone attribute
    public function setPhoneAttribute($value)
    {
        // Normalize the phone number
        $normalized = preg_replace('/\D/', '', $value); // Remove non-numeric characters

        if (strlen($normalized) === 12 && substr($normalized, 0, 3) === '971') {
            // Keep as-is if already includes +971
            $this->attributes['phone_number'] = $normalized;
        } elseif (strlen($normalized) === 9 && substr($normalized, 0, 1) === '5') {
            // Add +971 country code for local numbers
            $this->attributes['phone_number'] = '971' . $normalized;
        } else {
            // Handle invalid phone numbers (optional)
            $this->attributes['phone_number'] = $normalized; // Save as-is if it doesn't match UAE patterns
        }
    }


    public function lastVerificationCode()
    {
        return $this->hasOne(VerificationCode::class)->orderByDesc('id')->first();
    }



    public function addresses()
    {
        return $this->hasMany(Addresses::class);
    }



    public function isAdmin()
    {
        return $this->role === 'admin';
    }
    public function isDoctor()
    {
        return $this->role === 'doctor';
    }


    public function sentMessages()
    {
        return $this->hasMany(Messages::class, 'sender_id');
    }

    public function receivedMessages()
    {
        return $this->hasMany(Messages::class, 'receiver_id');
    }

    public function medicalReports()
    {
        return $this->hasMany(MedicalReports::class, 'patient_id');
    }
}
