<?php

namespace App\Http\Controllers\My\Product;

use App\Http\Controllers\Controller;
use App\Http\Requests\MyProduct\ProductChangeInstructionRequest;
use App\Models\Product;
use App\Services\Product\ProductService;
use App\Services\Product\ProductUpdater;
use App\Services\Toaster;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class ProductChangeInstructionController extends Controller
{
    public function index(Product $product): Response
    {
        return Inertia::render('my/products/ProductChangeInstructionModal', [
            'product' => $product,
        ]);
    }

    public function update(
        Product $product,
        ProductChangeInstructionRequest $request,
        ProductUpdater $updater,
        Toaster $toaster,
    ): RedirectResponse {
        $updater->updateInstruction($product, $request->input('instruction'));
        $toaster->success('Инструкция изменена');

        return back();
    }
}
