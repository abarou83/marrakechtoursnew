<?php

namespace App\Http\Requests\Api;

use Illuminate\Foundation\Http\FormRequest;

class CalculatePriceRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // Public API endpoint
    }

    /**
     * Get the validation rules that apply to the request.
     */
    public function rules(): array
    {
        return [
            'tour_id' => 'required|exists:tours,id',
            'date' => ['required', 'date', function ($attribute, $value, $fail) {
                try {
                    $today = \Carbon\Carbon::today()->startOfDay();
                    $date = \Carbon\Carbon::parse($value)->startOfDay();
                    if ($date->lt($today)) {
                        $fail('Date must be today or in the future.');
                    }
                } catch (\Exception $e) {
                    $fail('Invalid date format.');
                }
            }],
            'pricing_mode' => 'required|in:group,private',
            'pricing_id' => 'nullable|exists:tour_pricings,id',
            'adults' => 'required_if:pricing_mode,group|integer|min:0',
            'children' => 'integer|min:0',
            'infants' => 'integer|min:0',
            'selected_addons' => 'array',
            'selected_addons.*' => 'integer|min:1',
            'accommodation_rooms' => 'nullable|array',
            'accommodation_rooms.*.accommodation_id' => 'required_with:accommodation_rooms|exists:accommodations,id',
            'accommodation_rooms.*.accommodation_room_id' => 'required_with:accommodation_rooms|exists:accommodation_rooms,id',
            'accommodation_rooms.*.room_type' => 'required_with:accommodation_rooms|in:single,double,twin,triple',
            'accommodation_rooms.*.quantity' => 'required_with:accommodation_rooms|integer|min:1',
            'accommodation_rooms.*.price_per_night' => 'required_with:accommodation_rooms|numeric|min:0',
            'accommodation_rooms.*.room_notes' => 'nullable|string|max:500',
            'nights' => 'nullable|integer|min:1',
            // Legacy fields for backward compatibility
            'accommodation_id' => 'nullable|exists:accommodations,id',
            'accommodation_room_id' => 'nullable|exists:accommodation_rooms,id',
            'room_type' => 'nullable|in:single,double,twin,triple',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'tour_id.required' => 'Tour ID is required.',
            'tour_id.exists' => 'Selected tour does not exist.',
            'date.required' => 'Date is required.',
            'date.after_or_equal' => 'Date must be today or in the future.',
            'pricing_mode.required' => 'Pricing mode is required.',
            'pricing_mode.in' => 'Pricing mode must be group or private.',
            'adults.required_if' => 'Number of adults is required for group pricing.',
            'adults.min' => 'Number of adults must be at least 0.',
        ];
    }
}




