<?php

namespace App\Http\Requests\MyProduct;

use App\DTO\Product\ProductBaseCreateDto;
use App\DTO\Product\ProductUpdateBaseDto;
use App\ValueObjects\Price;
use Illuminate\Foundation\Http\FormRequest;

class ProductChangeBaseRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'name' => 'required|string|min:3|max:255',
            'price_base' => ['required', 'numeric', 'min:10'],
            'price_discount' => ['sometimes', 'nullable', 'numeric', 'lt:price_base'],
            'category_id' => ['required', 'int', 'exists:categories,id'],
        ];
    }

    public function getDto(): ProductUpdateBaseDto
    {
        return ProductUpdateBaseDto::from([
            'category_id' => $this->validated('category_id'),
            'name' => $this->validated('name'),
            'price' => Price::fromBaseAndDiscount(
                $this->validated('price_base'),
                $this->validated('price_discount')
            ),
        ]);
    }
}
