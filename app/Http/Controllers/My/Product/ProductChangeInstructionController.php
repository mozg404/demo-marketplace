<?php

namespace App\Http\Controllers\My\Product;

use App\DTO\Product\ProductUpdateInstructionDto;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Services\Product\ProductManager;
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
        ProductUpdateInstructionDto $dto,
        ProductManager $manager,
        Toaster $toaster,
    ): RedirectResponse {
        $manager->updateInstruction($product, $dto);
        $toaster->success('Инструкция изменена');

        return back();
    }
}
