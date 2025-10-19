<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Services\Price\PriceService;
use App\Services\Product\DTO\PurchasableItem;
use App\Services\Product\DTO\ReservedProduct;
use App\ValueObjects\Price;
use Inertia\Inertia;

class TestController extends Controller
{

    public function __construct()
    {}

    public function test(PriceService $service): mixed
    {

        $items = [
            new ReservedProduct(1, 2, new Price(100), []),
            new ReservedProduct(1, 2, new Price(5000), []),
        ];


        dd($service->calculateTotalQuantityPrice($items)->getCurrentPrice());
    }


    public function testPage(): mixed
    {
        return Inertia::render('test/TestPage', [
            'categoriesTree' => Category::query()->withDepth()->get()->toTree(),
        ]);
    }
}
