<?php

namespace App\Http\Requests\Api\V1;

use Illuminate\Foundation\Http\FormRequest;

class UpdateFaqRequest extends FormRequest
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
        $faqId = $this->route('faq')->id ?? null;

        return [
            'title' => 'sometimes|string|max:255|unique:faqs,title,' . $faqId,
            'answer' => 'sometimes|string',
            'content' => 'sometimes|string',
            'is_active' => 'sometimes|boolean',
            'order' => 'sometimes|integer|min:0',
        ];
    }
}
