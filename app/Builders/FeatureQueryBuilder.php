<?php

declare(strict_types=1);

namespace App\Builders;

use App\Collections\FeatureCollection;
use App\Models\Feature;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Collection;

/**
 * @method FeatureCollection|Feature[] get(array|string $column = ['*'])
 */
class FeatureQueryBuilder extends Builder
{
    public function whereCategories(array|Collection|\Illuminate\Support\Collection $ids): self
    {
        return $this->whereHas('categories', function($query) use ($ids) {
            $query->whereIn('id', $ids);
        });
    }
}
