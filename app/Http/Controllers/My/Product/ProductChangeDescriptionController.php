<?php

namespace App\Http\Controllers\My\Product;

use App\DTO\Product\ProductUpdateDescriptionDto;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Product\ProductManager;
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
        ProductUpdateDescriptionDto $dto,
        ProductManager $manager,
        Toaster $toaster
    ): RedirectResponse {
        $manager->updateDescription($product, $dto);
        $toaster->success('Описание изменено');

        return back();
    }
}
