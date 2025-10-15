<?php

declare(strict_types=1);

namespace App\Builders;

use App\Models\Category;
use Illuminate\Support\Collection as SupportCollection;
use Kalnoy\Nestedset\Collection as NestedsetCollection;
use Kalnoy\Nestedset\QueryBuilder;

/**
 * @method NestedsetCollection get($columns = ['*'])
 */
class CategoryQueryBuilder extends QueryBuilder
{
    public function withFeatures(): static
    {
        return $this->with('features');
    }

    public function whereFullPath(string $fullPath): self
    {
        return $this->where('full_path', $fullPath);
    }

    public function getIdByFullPath(string $fullPath): ?int
    {
        return $this->whereFullPath($fullPath)->first('id')?->id;
    }

    /**
     * Возвращает коллекцию ID для указанной категории + всех ее родителей
     */
    public function getAncestorAndSelfIds(Category|int $id): array
    {
        if ($id instanceof Category) {
            $id = $id->id;
        }

        return $this->ancestorsAndSelf($id)->pluck('id')->toArray();
    }

    /**
     * Возвращает коллекцию ID для указанной категории + всех ее детей
     */
    public function getDescendantsAndSelfIds(Category|int $id): array
    {
        if ($id instanceof Category) {
            $id = $id->id;
        }

        return $this->descendantsAndSelf($id, ['id'])->pluck('id')->toArray();
    }
}
