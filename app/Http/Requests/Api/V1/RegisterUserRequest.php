<?php

namespace App\Http\Requests\Api\V1;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class RegisterUserRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     *
     * @return bool
     */
    public function authorize()
    {
        return true; // Allow everyone to register
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array
     */
    public function rules()
    {
        return [
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'phone_number' => [
                'required',
                // 'regex:/^([\+971|971|05])\d{7}$/',
                // 'regex:/^([0-9\s\-\+\(\)]*)$/',
                // 'min:10',
                // 'regex:/^(?:971|05)\d{7}$/',
                'unique:users,phone_number',
            ],
            'password' => [
                'required',
                'string',
                'min:6',
                'max:255',
                'regex:/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&])[A-Za-z\d@$!%*?&]{8,}$/',
            ],
            'device_token' => ['nullable', 'string'],
            'name' => ['nullable', 'string', 'max:255'],
        ];
    }

    protected function failedValidation(Validator $validator)
    {
        throw new ValidationException($validator, response()->json([
            'error' => 'Validation failed.',
            'messages' => $validator->errors(),
        ], 422));
    }
}

