<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
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
        $productId = $this->route('id');

        return [
            'sku' => [
                'required',
                'string',
                'max:255',
                'unique:products,sku' . ($productId ? ',' . $productId : ''),
            ],
            'barcode' => [
                'nullable',
                'string',
                'max:255',
                'unique:products,barcode' . ($productId ? ',' . $productId : ''),
            ],
            'name' => 'required|string|max:255',
            'specs' => 'nullable|array',
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
            'sku.required' => 'SKU is required.',
            'sku.unique' => 'This SKU is already in use.',
            'barcode.unique' => 'This barcode is already in use.',
            'name.required' => 'Product name is required.',
            'category_id.required' => 'Category is required.',
            'category_id.exists' => 'Selected category does not exist.',
            'supplier_id.required' => 'Supplier is required.',
            'supplier_id.exists' => 'Selected supplier does not exist.',
            'price.required' => 'Price is required.',
            'price.numeric' => 'Price must be a number.',
            'price.min' => 'Price must be greater than or equal to 0.',
            'cost.required' => 'Cost is required.',
            'cost.numeric' => 'Cost must be a number.',
            'cost.min' => 'Cost must be greater than or equal to 0.',
            'uom.required' => 'Unit of measure is required.',
        ];
    }
}
