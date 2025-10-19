<?php

namespace App\Services\Category;

use App\Builders\CategoryQueryBuilder;
use App\Exceptions\Category\CategoryNotFoundException;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;

class CategoryQuery
{
    public const array CACHE_KEYS = [
        'categories:path_to_id_map',
        'categories:descendants_map',
    ];

    public function query(): CategoryQueryBuilder
    {
        return Category::query();
    }

    public function getByFullPath(string $fullPath): Category
    {
        $category = $this->query()->whereFullPath($fullPath)->first();

        if (!isset($category)) {
            throw new CategoryNotFoundException();
        }

        return $category;
    }

    public function findIdByPath(string $path): ?int
    {
        return $this->getPathIdsMap()[$path] ?? null;
    }

    public function getDescendantsAndSelfIdsFor(int $categoryId): array
    {
        return $this->getDescendantsIdsMap()[$categoryId] ?? [$categoryId];
    }

    public function getPathIdsMap(): array
    {
        return Cache::remember("categories:path_to_id_map", 86400, function () {
            return Category::query()->select('id', 'full_path')
                ->get()
                ->mapWithKeys(fn (Category $category) => [$category->full_path => $category->id])
                ->toArray();
        });
    }

    public function getDescendantsIdsMap(): array
    {
        return Cache::remember("categories:descendants_map", 86400, function () {
            return Category::query()
                ->with('descendants', fn($q) => $q->select('id', 'parent_id', '_lft', '_rgt'))
                ->select('id', 'parent_id', '_lft', '_rgt')
                ->get()
                ->mapWithKeys(fn (Category $category) => [
                    $category->id => $category->descendants->pluck('id')->push($category->id)->toArray()
                ])
                ->toArray();
        });
    }

    public function getDescendantsAndSelfIdsByPath(string $path): array
    {
        return $this->getDescendantsAndSelfIdsFor(
            $this->findIdByPath($path) ?? 0
        );
    }

    public function clearCache(): void
    {
        foreach (self::CACHE_KEYS as $key) {
            Cache::forget($key);
        }
    }
}