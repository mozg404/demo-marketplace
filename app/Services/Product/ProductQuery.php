<?php

namespace App\Services\Product;

use App\Builders\ProductQueryBuilder;
use App\Collections\ProductCollection;
use App\Enum\ProductGroup;
use App\Enum\TimeToLive;
use App\Models\Product;
use App\Services\Category\CategoryQuery;
use Illuminate\Support\Facades\Cache;

readonly class ProductQuery
{
    public const string CACHE_TAG = 'products';

    public function __construct(
        private CategoryQuery $categoryQuery,
    ) {
    }

    public function query(): ProductQueryBuilder
    {
        return Product::query();
    }

    public function search(string $request): ProductCollection
    {
        return Product::query()
            ->forListingPreset()
            ->searchAndSort($request)
            ->take(20)
            ->get();
    }

    public function getLatestDiscountedProducts(int $limit = 12): ProductCollection
    {
        return Cache::tags([self::CACHE_TAG, 'latest_discounted'])->remember("products:latest:discounted:{$limit}", TimeToLive::Day->value, function () use ($limit) {
            return Product::query()
                ->forListingPreset()
                ->orderByRating()
                ->isDiscounted()
                ->take($limit)
                ->get();
        });
    }

    public function getLatestProductsForGroup(ProductGroup $group, int $limit = 12): ProductCollection
    {
        return Cache::tags([self::CACHE_TAG, 'latest_group'])->remember("products:latest:group:$group->value:{$limit}", TimeToLive::Day->value, function () use ($group, $limit) {
            return Product::query()
                ->forListingPreset()
                ->latest()
                ->whereCategories($this->categoryQuery->getDescendantsAndSelfIdsByPath($group->getCategoryPath()))
                ->take($limit)
                ->get();
        });
    }

    public function clearCache(): void
    {
        Cache::tags([self::CACHE_TAG])->flush();
    }
}