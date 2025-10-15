<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class CatalogRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    // Пустые правила - валидация отключена
    public function rules(): array
    {
        return [];
    }

    protected function prepareForValidation(): void
    {
        if ($this->has('onlyDiscounted')) {
            $this->merge(['onlyDiscounted' => $this->toBoolean($this->input('onlyDiscounted'))]);
        }

        if ($this->has('priceMin')) {
            $this->merge(['priceMin' => (int) $this->input('priceMin')]);
        }

        if ($this->input('priceMax')) {
            $this->has(['priceMax' => (int) $this->input('priceMax')]);
        }
    }

    private function toBoolean($value): bool
    {
        return filter_var($value, FILTER_VALIDATE_BOOLEAN);
    }
}
