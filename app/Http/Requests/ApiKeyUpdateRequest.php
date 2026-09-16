<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ApiKeyUpdateRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'thiscodeworks_api_key' => ['nullable', 'string', 'max:255'],
        ];
    }

    public function messages(): array
    {
        return [
            'thiscodeworks_api_key.max' => 'La API key no puede superar 255 caracteres.',
        ];
    }
}