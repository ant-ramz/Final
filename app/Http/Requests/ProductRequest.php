<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class ProductRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'components' => 'sometimes|array',
            'components.*.inventory_item_id' => 'required_with:components|exists:inventory_items,id',
            'components.*.quantity' => 'required_with:components|numeric|min:0.0001',
            'components.*.unit_id' => 'required_with:components|exists:units,id',
        ];
    }
}
