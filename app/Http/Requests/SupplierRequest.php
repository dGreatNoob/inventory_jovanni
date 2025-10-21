<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class SupplierRequest extends FormRequest
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
            'entity_id' => 'nullable|integer',
            'name' => 'required|string|max:255',
            'contact_person' => 'nullable|string|max:255',
            'email' => 'nullable|email|max:255',
            'phone' => 'nullable|string|max:50',
            'address' => 'nullable|string',
            'city' => 'nullable|string|max:100',
            'country' => 'nullable|string|max:100',
            'postal_code' => 'nullable|string|max:20',
            'terms' => 'nullable|string',
            'tax_id' => 'nullable|string|max:100',
            'credit_limit' => 'nullable|numeric|min:0',
            'payment_terms_days' => 'nullable|integer|min:0',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Supplier name is required.',
            'name.max' => 'Supplier name cannot exceed 255 characters.',
            'email.email' => 'Please provide a valid email address.',
            'phone.max' => 'Phone number cannot exceed 50 characters.',
            'city.max' => 'City name cannot exceed 100 characters.',
            'country.max' => 'Country name cannot exceed 100 characters.',
            'postal_code.max' => 'Postal code cannot exceed 20 characters.',
            'tax_id.max' => 'Tax ID cannot exceed 100 characters.',
            'credit_limit.numeric' => 'Credit limit must be a number.',
            'credit_limit.min' => 'Credit limit must be greater than or equal to 0.',
            'payment_terms_days.integer' => 'Payment terms days must be a number.',
            'payment_terms_days.min' => 'Payment terms days must be greater than or equal to 0.',
        ];
    }
}
