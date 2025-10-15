<?php

namespace App\Http\Requests\MyProduct;

use Illuminate\Foundation\Http\FormRequest;

class ProductChangeImageRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'image' => 'required|image|mimes:jpeg,png,jpg,svg,avif,webp|max:5120',
        ];
    }
}
