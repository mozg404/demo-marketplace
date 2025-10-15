<?php

namespace App\Http\Controllers\My\Product;

use App\Data\Products\ProductDetailedData;
use App\Data\Products\ProductEditData;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Support\SeoBuilder;
use Inertia\Inertia;
use Inertia\Response;

class ProductEditController extends Controller
{
    public function index(Product $product): Response
    {
        return Inertia::render('my/products/ProductEditPage', [
            'product' => ProductEditData::from($product),
            'seo' => new SeoBuilder('Редактирование товара #' . $product->id),
        ]);
    }
}
