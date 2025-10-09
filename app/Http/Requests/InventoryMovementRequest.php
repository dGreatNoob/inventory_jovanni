<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class InventoryMovementRequest extends FormRequest
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
            'product_id' => 'required|exists:products,id',
            'location_id' => 'required|exists:inventory_locations,id',
            'movement_type' => 'required|string|in:purchase,sale,adjustment,transfer_in,transfer_out,return,damage,expired',
            'quantity' => 'required|numeric',
            'unit_cost' => 'nullable|numeric|min:0',
            'total_cost' => 'nullable|numeric|min:0',
            'reference_type' => 'nullable|string|max:100',
            'reference_id' => 'nullable|integer',
            'notes' => 'nullable|string',
            'metadata' => 'nullable|array',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'product_id.required' => 'Product is required.',
            'product_id.exists' => 'Selected product does not exist.',
            'location_id.required' => 'Location is required.',
            'location_id.exists' => 'Selected location does not exist.',
            'movement_type.required' => 'Movement type is required.',
            'movement_type.in' => 'Invalid movement type selected.',
            'quantity.required' => 'Quantity is required.',
            'quantity.numeric' => 'Quantity must be a number.',
            'unit_cost.numeric' => 'Unit cost must be a number.',
            'unit_cost.min' => 'Unit cost must be greater than or equal to 0.',
            'total_cost.numeric' => 'Total cost must be a number.',
            'total_cost.min' => 'Total cost must be greater than or equal to 0.',
            'reference_type.max' => 'Reference type cannot exceed 100 characters.',
            'reference_id.integer' => 'Reference ID must be a number.',
        ];
    }
}
