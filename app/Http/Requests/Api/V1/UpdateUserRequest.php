<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class UpdateUserRequest extends FormRequest
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
        $id = $this->route('id');

        return [
            'first_name' => 'sometimes|string|max:255',
            'last_name' => 'sometimes|string|max:255',
            'email' => "sometimes|email|unique:users,email,{$id}",
            'phone_number' => "sometimes|string|max:20|unique:users,phone_number,{$id}",
            'role' => 'nullable|in:patient,doctor,admin',
            'gender' => 'nullable|in:male,female',
            'date_of_birth' => 'nullable|date',
            'weight' => 'nullable|numeric',
            'nationality' => 'nullable|string',
            'device_token' => 'nullable|string',
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

