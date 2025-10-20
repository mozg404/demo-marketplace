<?php

namespace App\Services\Category;

use App\Builders\CategoryQueryBuilder;
use App\Enum\TimeToLive;
use App\Exceptions\Category\CategoryNotFoundException;
use App\Models\Category;
use Illuminate\Support\Facades\Cache;
use Kalnoy\Nestedset\Collection as NestedCollection;

class CategoryQuery
{
    public const string CACHE_TAG = 'categories';

    public function getByFullPath(string $fullPath): Category
    {
        $category = Category::query()->whereFullPath($fullPath)->first();

        if (!isset($category)) {
            throw new CategoryNotFoundException();
        }

        return $category;
    }

    public function getTree(): NestedCollection
    {
        return Cache::tags([self::CACHE_TAG, 'tree'])->remember("categories:tree", TimeToLive::ThreeDays->value, static function () {
            return Category::query()->withDepth()->get()->toTree();
        });
    }
    
    public function findIdByPath(string $path): ?int
    {
        return $this->getPathIdsMap()[$path] ?? null;
    }

    public function getPathIdsMap(): array
    {
        return Cache::tags([self::CACHE_TAG, 'map'])->remember("categories:path_to_id_map", TimeToLive::ThreeDays->value, static function () {
            return Category::query()->select('id', 'full_path')
                ->get()
                ->mapWithKeys(fn (Category $category) => [$category->full_path => $category->id])
                ->toArray();
        });
    }

    public function getDescendantsIdsMap(): array
    {
        return Cache::tags([self::CACHE_TAG, 'map'])->remember("categories:descendants_map", TimeToLive::ThreeDays->value, static function () {
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

    public function getDescendantsAndSelfIdsFor(int $categoryId): array
    {
        return $this->getDescendantsIdsMap()[$categoryId] ?? [$categoryId];
    }

    public function getDescendantsAndSelfIdsByPath(string $path): array
    {
        return $this->getDescendantsAndSelfIdsFor(
            $this->findIdByPath($path) ?? 0
        );
    }

    public function getAncestorsIdsMap(): array
    {
        return Cache::tags([self::CACHE_TAG, 'map'])->remember("categories:ancestors_map", TimeToLive::ThreeDays->value, static function () {
            return Category::query()
                ->with('ancestors', fn($q) => $q->select('id', 'parent_id', '_lft', '_rgt'))
                ->select('id', 'parent_id', '_lft', '_rgt')
                ->get()
                ->mapWithKeys(fn (Category $category) => [
                    $category->id => $category->ancestors->pluck('id')->push($category->id)->toArray()
                ])
                ->toArray();
        });
    }

    public function getAncestorsAndSelfIdsFor(int $categoryId): array
    {
        return $this->getAncestorsIdsMap()[$categoryId] ?? [$categoryId];
    }

    public function clearCache(): void
    {
        Cache::tags([self::CACHE_TAG])->flush();
    }
}