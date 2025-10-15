<?php

namespace App\Http\Controllers\My\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\MyProduct\ProductChangeDescriptionRequest;
use App\Models\Product;
use App\Services\Product\ProductService;
use App\Services\Product\ProductUpdater;
use App\Services\Toaster;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProductChangeDescriptionController extends Controller
{
    public function index(Product $product): Response
    {
        return Inertia::render('my/products/ProductChangeDescriptionModal', [
            'product' => $product,
        ]);
    }

    public function update(
        Product $product,
        ProductChangeDescriptionRequest $request,
        ProductUpdater $updater,
        Toaster $toaster
    ): RedirectResponse {
        $updater->updateDescription($product, $request->input('description'));
        $toaster->success('Описание изменено');

        return back();
    }
}
