<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductStoreRequest extends FormRequest
{
    /**
     * Determine if the user is authorized to make this request.
     */
    public function authorize(): bool
    {
        return true; // You can implement authorization logic here
    }

    /**
     * Get the validation rules that apply to the request.
     *
     * @return array<string, \Illuminate\Contracts\Validation\ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            'sku' => 'required|string|max:255|unique:products,sku',
            'barcode' => 'nullable|string|max:255|unique:products,barcode',
            'name' => 'required|string|max:255',
            'specs' => 'nullable|array',
            'specs.*' => 'nullable|string',
            'category_id' => 'required|exists:categories,id',
            'remarks' => 'nullable|string',
            'uom' => 'required|string|max:255',
            'supplier_id' => 'required|exists:suppliers,id',
            'supplier_code' => 'nullable|string|max:255',
            'price' => 'required|numeric|min:0',
            'price_note' => 'nullable|string',
            'cost' => 'required|numeric|min:0',
            'shelf_life_days' => 'nullable|integer|min:0',
            'pict_name' => 'nullable|string|max:255',
            'disabled' => 'boolean',
            'initial_quantity' => 'nullable|numeric|min:0',
            'location_id' => 'nullable|exists:inventory_locations,id',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'sku.required' => 'SKU is required',
            'sku.unique' => 'SKU must be unique',
            'barcode.unique' => 'Barcode must be unique',
            'name.required' => 'Product name is required',
            'category_id.required' => 'Category is required',
            'category_id.exists' => 'Selected category does not exist',
            'supplier_id.required' => 'Supplier is required',
            'supplier_id.exists' => 'Selected supplier does not exist',
            'price.required' => 'Price is required',
            'price.numeric' => 'Price must be a number',
            'price.min' => 'Price must be at least 0',
            'cost.required' => 'Cost is required',
            'cost.numeric' => 'Cost must be a number',
            'cost.min' => 'Cost must be at least 0',
            'shelf_life_days.integer' => 'Shelf life must be a number of days',
            'shelf_life_days.min' => 'Shelf life cannot be negative',
            'initial_quantity.numeric' => 'Initial quantity must be a number',
            'initial_quantity.min' => 'Initial quantity cannot be negative',
            'location_id.exists' => 'Selected location does not exist',
        ];
    }

    /**
     * Get custom attributes for validator errors.
     */
    public function attributes(): array
    {
        return [
            'sku' => 'SKU',
            'barcode' => 'Barcode',
            'name' => 'Product Name',
            'specs' => 'Specifications',
            'category_id' => 'Category',
            'remarks' => 'Remarks',
            'uom' => 'Unit of Measure',
            'supplier_id' => 'Supplier',
            'supplier_code' => 'Supplier Code',
            'price' => 'Price',
            'price_note' => 'Price Note',
            'cost' => 'Cost',
            'shelf_life_days' => 'Shelf Life (Days)',
            'pict_name' => 'Picture Name',
            'disabled' => 'Disabled Status',
            'initial_quantity' => 'Initial Quantity',
            'location_id' => 'Location',
        ];
    }

    /**
     * Prepare the data for validation.
     */
    protected function prepareForValidation(): void
    {
        // Convert empty strings to null for optional fields
        $this->merge([
            'barcode' => $this->barcode ?: null,
            'supplier_code' => $this->supplier_code ?: null,
            'price_note' => $this->price_note ?: null,
            'pict_name' => $this->pict_name ?: null,
            'disabled' => $this->boolean('disabled', false),
            'initial_quantity' => $this->initial_quantity ?: null,
            'location_id' => $this->location_id ?: null,
        ]);
    }
}