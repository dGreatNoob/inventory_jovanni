<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductUpdateRequest extends FormRequest
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
        $productId = $this->route('id') ?? $this->route('product');
        
        return [
            'sku' => 'sometimes|required|string|max:255|unique:products,sku,' . $productId,
            'barcode' => 'nullable|string|max:255|unique:products,barcode,' . $productId,
            'name' => 'sometimes|required|string|max:255',
            'specs' => 'nullable|array',
            'specs.*' => 'nullable|string',
            'category_id' => 'sometimes|required|exists:categories,id',
            'remarks' => 'nullable|string',
            'uom' => 'sometimes|required|string|max:255',
            'supplier_id' => 'sometimes|required|exists:suppliers,id',
            'supplier_code' => 'nullable|string|max:255',
            'price' => 'sometimes|required|numeric|min:0',
            'price_note' => 'nullable|string',
            'cost' => 'sometimes|required|numeric|min:0',
            'shelf_life_days' => 'nullable|integer|min:0',
            'pict_name' => 'nullable|string|max:255',
            'disabled' => 'boolean',
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
        ]);
    }
}