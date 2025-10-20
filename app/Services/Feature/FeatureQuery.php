<?php

namespace App\Services\Feature;

use App\Builders\FeatureQueryBuilder;
use App\Collections\FeatureCollection;
use App\Enum\TimeToLive;
use App\Models\Feature;
use App\Services\Category\CategoryQuery;
use Illuminate\Support\Facades\Cache;

readonly class FeatureQuery
{
    public const string CACHE_TAG = 'features';
    public function __construct(
        private CategoryQuery $categoryQuery,
    ) {
    }

    public function getFeaturesByCategory(int $categoryId): FeatureCollection
    {
        return Cache::tags([self::CACHE_TAG])
            ->remember("features:category:$categoryId", TimeToLive::ThreeDays->value, function () use ($categoryId) {
                return Feature::query()
                    ->whereCategories($this->categoryQuery->getAncestorsAndSelfIdsFor($categoryId))
                    ->get();
            });
    }

    public function cacheClear(): void
    {
        Cache::tags([self::CACHE_TAG])->flush();
    }
}