<?php

namespace App\Services\Product;

use App\Builders\ProductQueryBuilder;
use App\Collections\ProductCollection;
use App\Enum\ProductGroup;
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

    public function getLatestDiscountedProducts(): ProductCollection
    {
        return Cache::tags([self::CACHE_TAG, 'latest_discounted'])->remember("products:latest:discounted", 3600, function () {
            return $this->query()
                ->forListingPreset()
                ->orderByRating()
                ->isDiscounted()
                ->take(12)
                ->get();
        });
    }

    public function getLatestProductsForGroup(ProductGroup $group): ProductCollection
    {
        return Cache::tags([self::CACHE_TAG, 'latest_group'])->remember("products:latest:group:$group->value", 3600, function () use ($group) {
            return $this->query()
                ->forListingPreset()
                ->latest()
                ->whereCategories($this->categoryQuery->getDescendantsAndSelfIdsByPath($group->getCategoryPath()))
                ->take(12)
                ->get();
        });
    }

    public function clearCache(): void
    {
        Cache::tags([self::CACHE_TAG])->flush();
    }
}