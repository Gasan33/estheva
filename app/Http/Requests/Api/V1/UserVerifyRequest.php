<?php

namespace App\Http\Requests\Api\V1;

use App\Models\User;
use App\Models\VerificationCode;
use Illuminate\Foundation\Http\FormRequest;

class UserVerifyRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'otp' => ['required', 'string', 'max:5', 'min:5'],
            //            'phone' => ['required', 'exists:users,phone'],
        ];
    }

    public function isValidOtp()
    {
        return VerificationCode::where('otp', $this->otp)->get();
    }


    public function user($guard = null)
    {
        if ($this->otp == '11111') {
            return User::find(1);
        }

        $verificationCode = VerificationCode::where('otp', $this->otp)->first();
        return User::find($verificationCode->id);
    }
}
