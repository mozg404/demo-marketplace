<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

abstract class FilterableRequest extends FormRequest
{
    abstract protected function filters(): array;

    public function getFiltersKeys(): array
    {
        return array_keys($this->filters());
    }

    public function getValues(): array
    {
        return $this->only($this->getFiltersKeys());
    }

    public function prepareForValidation(): void
    {
        foreach ($this->filters() as $key => $value) {
            $this->merge([$key => $value]);
        }
    }

    // Отрубаем переадресацию и вывод ошибок
    public function validateResolved(): void
    {
        $this->prepareForValidation();
    }

    public function authorize(): bool
    {
        return false;
    }

    public function rules(): array
    {
        return [];
    }
}
