<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class TreatmentRequest extends FormRequest
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
     * @return array<string, mixed>
     */
    public function rules(): array
    {
        return [
            'title' => 'required|string|max:255',
            'description' => 'nullable|string',
            'price' => 'required|numeric',
            'images' => 'nullable|array',
            'doctor_id' => 'nullable|array',
            'doctor_id.*' => 'exists:doctors,id',
            'home_based' => 'sometimes|boolean',
            'video' => 'nullable|string',
            'duration' => 'nullable|numeric',
            'benefits' => 'nullable|array',
            'instructions' => 'nullable|array',
            'discount_value' => 'nullable|numeric',
            'discount_type' => 'nullable|in:percentage,fixed',
            'treatment_sale_tag' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
        ];
    }
}
