<?php

namespace App\Http\Requests\MyProduct\Stock;

use Illuminate\Foundation\Http\FormRequest;

class StockStoreRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'content' => 'required|string|min:3|max:255',
        ];
    }
}
