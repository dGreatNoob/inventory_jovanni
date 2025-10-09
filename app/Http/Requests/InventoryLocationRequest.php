<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventoryLocationRequest extends FormRequest
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
            'type' => 'required|string|max:100',
            'address' => 'nullable|string',
            'description' => 'nullable|string',
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Location name is required.',
            'name.max' => 'Location name cannot exceed 255 characters.',
            'type.required' => 'Location type is required.',
            'type.max' => 'Location type cannot exceed 100 characters.',
        ];
    }
}
