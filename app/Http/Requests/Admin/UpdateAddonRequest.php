<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateAddonRequest extends FormRequest
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
        $addonId = $this->route('addon')->id ?? null;
        $activeLocales = \App\Models\Language::active()->pluck('code')->toArray();

        return [
            'name' => 'required|string|max:255',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('addons', 'slug')->ignore($addonId),
            ],
            'pricing_type' => 'required|in:per_person,per_group,free',
            'base_price' => 'required_if:pricing_type,per_person,per_group|numeric|min:0',
            'is_active' => 'boolean',
            'translations' => 'nullable|array',
            'translations.*.locale' => 'required|in:' . implode(',', $activeLocales),
            'translations.*.name' => 'required|string|max:255',
        ];
    }
}




