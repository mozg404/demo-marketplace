<?php

namespace App\Http\Controllers;

use App\Data\Products\ProductForListingData;
use App\Enum\ProductGroup;
use App\Services\Product\ProductQuery;
use Inertia\Inertia;
use Inertia\Response;

class IndexController extends Controller
{
    public function __invoke(ProductQuery $productQuery): Response {
        return Inertia::render('IndexPage', [
            'discounted' => ProductForListingData::collect(
                $productQuery->getLatestDiscountedProducts(
                    config('project.main_products_count')
                )
            ),
            'games' => ProductForListingData::collect(
                $productQuery->getLatestProductsForGroup(
                    ProductGroup::Games,
                    config('project.main_products_count')
                )
            ),
            'certificates' => ProductForListingData::collect(
                $productQuery->getLatestProductsForGroup(
                    ProductGroup::Certificates,
                    config('project.main_products_count')
                )
            ),
            'subscriptions' => ProductForListingData::collect(
                $productQuery->getLatestProductsForGroup(
                    ProductGroup::Subscriptions,
                    config('project.main_products_count')
                )
            ),
        ]);
    }
}
