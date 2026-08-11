<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class StoreAddonRequest extends FormRequest
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
     */
    public function rules(): array
    {
        $activeLocales = \App\Models\Language::active()->pluck('code')->toArray();

        return [
            'name' => 'required|string|max:255',
            'slug' => 'nullable|string|max:255|unique:addons,slug',
            'pricing_type' => 'required|in:per_person,per_group,free',
            'base_price' => 'nullable|numeric|min:0',
            'is_active' => 'boolean',
            'translations' => 'nullable|array',
            'translations.*.locale' => 'required|in:' . implode(',', $activeLocales),
            'translations.*.name' => 'required|string|max:255',
        ];
    }

    /**
     * Configure the validator instance.
     */
    public function withValidator($validator): void
    {
        $validator->sometimes('base_price', 'required', function ($input) {
            return in_array($input->pricing_type, ['per_person', 'per_group']);
        });
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Addon name is required.',
            'pricing_type.required' => 'Pricing type is required.',
            'pricing_type.in' => 'Pricing type must be per_person, per_group, or free.',
            'base_price.required_if' => 'Base price is required for per_person and per_group pricing types.',
        ];
    }
}





