<?php

namespace App\Http\Requests;

use Illuminate\Contracts\Validation\ValidationRule;
use Illuminate\Foundation\Http\FormRequest;

class UpdateProductRequest extends FormRequest
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
     * @return array<string, ValidationRule|array<mixed>|string>
     */
    public function rules(): array
    {
        return [
            /**
             * Product name.
             * @example "Wireless Mouse"
             */
            'name' => ['sometimes', 'required', 'string', 'max:255'],
            /**
             * Product description shown on the product detail page.
             * @example "Compact wireless mouse with silent clicks."
             */
            'description' => ['nullable', 'string'],
            /**
             * Product price in the store currency.
             * @example 139.90
             */
            'price' => ['sometimes', 'required', 'numeric', 'min:0'],
            /**
             * Available stock quantity.
             * @example 48
             */
            'stock' => ['sometimes', 'required', 'integer', 'min:0'],
        ];
    }
}
