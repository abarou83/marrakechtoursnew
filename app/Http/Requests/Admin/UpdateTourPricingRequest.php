<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;

class UpdateTourPricingRequest extends FormRequest
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
        $rules = [
            'title' => 'nullable|string|max:255',
            'pricing_mode' => 'required|in:group,private',
            'season' => 'required|in:low,normal,high,all',
            'is_active' => 'boolean',
        ];

        // Group pricing fields - only validate if pricing_mode is group
        if ($this->pricing_mode === 'group') {
            $rules['group_prices'] = 'required|array|min:1';
            $rules['group_prices.*.category'] = 'required|in:adult,child,infant';
            $rules['group_prices.*.age_min'] = 'nullable|integer|min:0';
            $rules['group_prices.*.age_max'] = 'nullable|integer|gte:group_prices.*.age_min';
            $rules['group_prices.*.price'] = 'required|numeric|min:0';
        }

        // Private pricing fields - only validate if pricing_mode is private
        if ($this->pricing_mode === 'private') {
            $rules['private_prices'] = 'required|array|min:1';
            $rules['private_prices.*.min_people'] = 'required|integer|min:1';
            $rules['private_prices.*.max_people'] = 'required|integer|gte:private_prices.*.min_people';
            $rules['private_prices.*.price'] = 'required|numeric|min:0';
        }

        return $rules;
    }
}





