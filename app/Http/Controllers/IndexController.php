<?php

namespace App\Http\Controllers;

use App\Data\Products\ProductForListingData;
use App\Enum\ProductGroup;
use App\Services\Category\CategoryQuery;
use App\Services\Product\ProductQuery;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __invoke(
        ProductQuery $productQuery,
        CategoryQuery $categoryQuery,
    ): Response {
        return Inertia::render('IndexPage', [
            'discounted' => ProductForListingData::collect(
                $productQuery->getLatestDiscountedProducts()
            ),
            'games' => ProductForListingData::collect(
                $productQuery->getLatestProductsForGroup(ProductGroup::Games)
            ),
            'certificates' => ProductForListingData::collect(
                $productQuery->getLatestProductsForGroup(ProductGroup::Certificates)
            ),
            'subscriptions' => ProductForListingData::collect(
                $productQuery->getLatestProductsForGroup(ProductGroup::Subscriptions)
            ),
        ]);
    }
}
