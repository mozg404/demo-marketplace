<?php

namespace App\Http\Controllers;

use App\Data\Products\ProductForListingData;
use App\Services\Category\CategoryRepository;
use App\Services\Product\ProductQuery;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __invoke(
        ProductQuery $productQuery,
        CategoryRepository $categoryQuery,
    ): Response {
        $discounted = $productQuery->query()
            ->forListingPreset()
            ->latest()
            ->isDiscounted()
            ->take(12)
            ->get();
        $games = $productQuery->query()
            ->forListingPreset()
            ->latest()
            ->whereCategories($categoryQuery->getDescendantsAndSelfIdsByFullPath('keys/games'))
            ->take(12)
            ->get();
        $certificates = $productQuery->query()
            ->forListingPreset()
            ->latest()
            ->whereCategories($categoryQuery->getDescendantsAndSelfIdsByFullPath('certificates'))
            ->take(12)
            ->get();
        $subscriptions = $productQuery->query()
            ->forListingPreset()
            ->latest()
            ->whereCategories($categoryQuery->getDescendantsAndSelfIdsByFullPath('subscriptions'))
            ->take(12)
            ->get();

        return Inertia::render('IndexPage', [
            'discounted' => ProductForListingData::collect($discounted),
            'games' => ProductForListingData::collect($games),
            'certificates' => ProductForListingData::collect($certificates),
            'subscriptions' => ProductForListingData::collect($subscriptions),
        ]);
    }
}
