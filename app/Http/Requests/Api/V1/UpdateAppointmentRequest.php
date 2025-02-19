<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;
use Illuminate\Validation\ValidationException;

class UpdateAppointmentRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return false;
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'user_id.exists' => 'The selected user does not exist.',
            'doctor_id.exists' => 'The selected doctor does not exist.',
            'treatment_id.exists' => 'The selected treatment does not exist.',
            'appointment_date.date' => 'The appointment date must be a valid date.',
            'appointment_time.date_format' => 'The appointment time format must be HH:MM.',
            'status.string' => 'The status must be a string.',
            'status.max' => 'The status must not exceed 50 characters.',
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
