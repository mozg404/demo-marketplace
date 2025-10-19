<?php

namespace App\Http\Controllers;

use App\Contracts\Cart;
use App\Models\Category;
use App\Services\Cart\CartService;
use Inertia\Inertia;

class TestController extends Controller
{

    public function __construct()
    {}

    public function test(CartService $cart): mixed
    {
//        $cart->add(1,2);
//        $cart->add(2,1);

        return $cart->all();
    }


    public function testPage(): mixed
    {
        return Inertia::render('test/TestPage', [
            'categoriesTree' => Category::query()->withDepth()->get()->toTree(),
        ]);
    }
}
