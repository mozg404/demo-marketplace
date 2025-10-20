<?php

namespace App\Http\Controllers\My\Product;

use App\Data\Products\ProductForListingData;
use App\Http\Controllers\Controller;
use App\Http\Requests\MyProduct\ProductFilterableRequest;
use App\Services\Product\ProductQuery;
use App\Support\SeoBuilder;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class ProductIndexController extends Controller
{
    public function index(ProductFilterableRequest $request, ProductQuery $productQuery): Response
    {
        $products = $productQuery->query()
            ->whereSeller(Auth::id())
            ->withStockItemsCount()
            ->withAvailableCount()
            ->filterFromArray($request->getFiltersValues())
            ->paginate(config('project.cabinet_products_count'))
            ->appends($request->getFiltersValues());

        return Inertia::render('my/products/ProductIndexPage', [
            'products' => ProductForListingData::collect($products),
            'filters' => $request->getFiltersValues(),
            'seo' => new SeoBuilder('Мои товары'),
        ]);
    }
}
