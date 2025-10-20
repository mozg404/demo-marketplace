<?php

namespace App\Http\Controllers\My\Product;

use App\Data\Products\ProductChangeBaseData;
use App\Enum\ProductStatus;
use App\Http\Controllers\Controller;
use App\Http\Requests\MyProduct\ProductChangeBaseRequest;
use App\Models\Product;
use App\Services\Category\CategoryQuery;
use App\Services\Product\ProductManager;
use App\Services\Toaster;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProductChangeBaseController extends Controller
{
    public function index(Product $product, CategoryQuery $categoryQuery): Response
    {
        return Inertia::render('my/products/ProductChangeBaseModal', [
            'product' => ProductChangeBaseData::from($product),
            'statuses' => ProductStatus::names(),
            'categoriesTree' => $categoryQuery->getTree(),
        ]);
    }

    public function update(
        Product $product,
        ProductChangeBaseRequest $request,
        ProductManager $manager,
        Toaster $toaster,
    ): RedirectResponse {
        $manager->updateBaseProduct($product, $request->getDto());
        $toaster->success('Название отредактировано');

        return back();
    }
}
