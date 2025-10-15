<?php

namespace App\Http\Requests\MyProduct;

use Illuminate\Foundation\Http\FormRequest;

class ProductChangeFeaturesRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'features' => 'required|array',
        ];
    }

    public function getValidatedValues(): array
    {
        return array_filter($this->input('features', []), static fn ($value) => isset($value));
    }
}
