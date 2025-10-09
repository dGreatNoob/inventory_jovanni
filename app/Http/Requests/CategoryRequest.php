<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CategoryRequest extends FormRequest
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
        $categoryId = $this->route('id');

        return [
            'entity_id' => 'nullable|integer',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'parent_id' => [
                'nullable',
                'exists:categories,id',
                function ($attribute, $value, $fail) use ($categoryId) {
                    if ($value && $value == $categoryId) {
                        $fail('A category cannot be its own parent.');
                    }
                },
            ],
            'sort_order' => 'nullable|integer|min:0',
            'slug' => [
                'nullable',
                'string',
                'max:255',
                'unique:categories,slug' . ($categoryId ? ',' . $categoryId : ''),
            ],
            'is_active' => 'boolean',
        ];
    }

    /**
     * Get custom messages for validator errors.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Category name is required.',
            'name.max' => 'Category name cannot exceed 255 characters.',
            'parent_id.exists' => 'Selected parent category does not exist.',
            'slug.unique' => 'This slug is already in use.',
            'sort_order.integer' => 'Sort order must be a number.',
            'sort_order.min' => 'Sort order must be greater than or equal to 0.',
        ];
    }
}
