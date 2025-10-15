<?php

namespace App\Http\Requests\MyProduct;

use App\Models\Category;
use Illuminate\Foundation\Http\FormRequest;

class ProductChangeCategoryRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'category_id' => ['required', 'int', 'exists:categories,id'],
        ];
    }

    public function getModel(): Category
    {
        return Category::find($this->input('category_id'));
    }
}
