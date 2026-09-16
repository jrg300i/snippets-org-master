<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLanguageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $language = $this->route('language');

        return [
            'name' => ['required', 'string', 'max:100', Rule::unique('languages', 'name')->ignore($language->id)],
            'slug' => [
                'required',
                'string',
                'max:50',
                Rule::unique('languages', 'slug')->ignore($language->id),
                'regex:/^[a-z0-9-]+$/',
            ],
            'color' => ['required', 'string', 'max:7', 'regex:/^#?([A-Fa-f0-9]{6}|[A-Fa-f0-9]{3})$/'],
            'description' => ['nullable', 'string', 'max:500'],
            'is_active' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'name.required' => 'El nombre del lenguaje es obligatorio.',
            'name.unique' => 'Ya existe un lenguaje con ese nombre.',
            'slug.required' => 'El slug es obligatorio.',
            'slug.unique' => 'Ya existe un lenguaje con ese slug.',
            'slug.regex' => 'El slug solo puede contener minúsculas, números y guiones.',
            'color.required' => 'El color es obligatorio.',
            'color.regex' => 'El color debe ser un hexadecimal (ej: #2563EB).',
        ];
    }
}