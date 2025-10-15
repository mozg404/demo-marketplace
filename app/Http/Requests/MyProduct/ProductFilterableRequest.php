<?php

namespace App\Http\Requests\MyProduct;

use Illuminate\Foundation\Http\FormRequest;

class ProductFilterableRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [];
    }

    public function prepareForValidation(): void
    {
        $this->merge([
            'search' => $this->normalizeSearch($this->input('search')),
            'sort' => $this->normalizeSort($this->input('sort')),
            'status' => $this->normalizeStatus($this->input('status')),
        ]);
    }

    protected function normalizeSearch(?string $sort): ?string
    {
        return !empty($sort) ? trim($sort) : null;
    }

    protected function normalizeSort(?string $sort): string
    {
        $allowed = [
            'latest',
            'oldest',
            'id_asc',
            'id_desc',
            'price_asc',
            'price_desc'
        ];

        return in_array($sort, $allowed, true) ? $sort : 'id_desc';
    }

    protected function normalizeStatus(?string $sort): string
    {
        $allowed = [
            'all',
            'available',
            'sold_out',
            'draft',
            'paused',
        ];

        return in_array($sort, $allowed, true) ? $sort : 'all';
    }

    public function getFiltersValues(): array
    {
        return $this->only('search', 'sort', 'status');
    }

    public function validateResolved(): void
    {
        $this->prepareForValidation();
    }
}
