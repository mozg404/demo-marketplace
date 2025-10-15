<?php

namespace App\Http\Requests\MyProduct;

use Illuminate\Foundation\Http\FormRequest;

class ProductChangeDescriptionRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'description' => ['required', 'string', 'min:5'],
        ];
    }
}
