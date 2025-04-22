<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class UpdateDoctorRequest extends FormRequest
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
            'first_name' => 'sometimes|required|string|max:255',
            'last_name' => 'sometimes|required|string|max:255',
            'email' => 'sometimes|required|email',
            'phone_number' => 'sometimes|required|string',
            'password' => 'sometimes|required|string|min:8',
            'specialty' => 'sometimes|required|string',
            'certificate' => 'nullable|string',
            'university' => 'nullable|string',
            'patients' => 'nullable|integer',
            'exp' => 'nullable|integer',
            'about' => 'nullable|string',
            'home_based' => 'nullable|boolean',
            'online_consultation' => 'nullable|boolean',
            'availability' => 'sometimes|array',
            'availability.*.day' => 'sometimes|required|in:Monday,Tuesday,Wednesday,Thursday,Friday,Saturday,Sunday',
            'availability.*.start_time' => 'sometimes|required|date_format:H:i',
            'availability.*.end_time' => 'sometimes|required|date_format:H:i',
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
