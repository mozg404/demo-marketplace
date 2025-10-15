<?php

namespace App\Http\Requests\Catalog;

use App\Http\Requests\FilterableRequest;

class CatalogFilterableRequest extends FilterableRequest
{
    protected function filters(): array
    {
        return [
            'is_discounted' => $this->normalizeIsDiscounted($this->input('is_discounted')),
            'price_min' => $this->normalizePrice($this->input('price_min')),
            'price_max' => $this->normalizePrice($this->input('price_max')),
            'sort' => $this->normalizeSort($this->input('sort')),
            'search' => $this->input('search'),
        ];
    }

    protected function normalizePrice(mixed $value): ?int
    {
        return is_numeric($value) ? (int)$value : null;
    }

    protected function normalizeIsDiscounted(?string $value): ?bool
    {
        if ($this->has('is_discounted')) {
            return filter_var($value, FILTER_VALIDATE_BOOLEAN);
        }

        return null;
    }

    protected function normalizeSort(?string $sort): string
    {
        $allowed = [
            'rating',
            'price_asc',
            'price_desc',
            'latest',
            'oldest',
        ];

        return in_array($sort, $allowed, true) ? $sort : 'rating';
    }
}
