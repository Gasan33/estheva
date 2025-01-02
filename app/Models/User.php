<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Carbon\Carbon;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Tymon\JWTAuth\Contracts\JWTSubject;
use Filament\Panel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use libphonenumber\PhoneNumberUtil;
use libphonenumber\PhoneNumberFormat;

class User extends Authenticatable implements JWTSubject, FilamentUser
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable;

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
        'date_of_birth',
        'gender',
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
            'phone_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }


    protected function profilePictureUrl(): Attribute
    {
        return Attribute::get(function () {
            return $this->profile_picture
                ? asset('storage/' . $this->profile_picture)
                : asset('user-avatar.png'); // Path to your default avatar
        });
    }

    public function getAgeAttribute()
    {
        return Carbon::parse($this->date_of_birth)->age;
    }

    public function getFormattedPhoneNumberAttribute()
    {

        $phoneUtil = PhoneNumberUtil::getInstance();
        $number = $phoneUtil->parse($this->phone_number, 'AE'); // 'AE' is the country code for UAE

        // Format the phone number in national format
        $formattedPhone = $phoneUtil->format($number, PhoneNumberFormat::NATIONAL);
        // Check if the phone number includes the UAE country code (+971)
        // $phone = $this->phone_number;

        // // Remove the country code (+971) and format the number
        // $formattedPhone = preg_replace('/\+971(\d{3})(\d{3})(\d{4})/', '$1 $2 $3', $phone);

        return $formattedPhone;
    }

    public function getFullNameAttribute()
    {
        return $this->first_name . ' ' . $this->last_name;
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


    // JWT methods
    public function getJWTIdentifier()
    {
        return $this->getKey();
    }

    /**
     * Return a key value array, containing any custom claims to be added to the JWT.
     *
     * @return array
     */
    public function getJWTCustomClaims()
    {
        return [];
    }

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') {
            return $this->isAdmin();
        }

        return true;
    }
}
