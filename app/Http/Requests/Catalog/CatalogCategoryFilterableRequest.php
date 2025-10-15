<?php

namespace App\Http\Requests\Catalog;

class CatalogCategoryFilterableRequest extends CatalogFilterableRequest
{
    protected function filters(): array
    {
        return array_merge(parent::filters(), [
            'features' => $this->input('features'),
        ]);
    }
}
