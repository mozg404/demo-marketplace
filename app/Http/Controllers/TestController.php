<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Services\Cart\CartService;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Redis;
use Inertia\Inertia;

class TestController extends Controller
{

    public function __construct()
    {}

    public function test(CartService $cart): mixed
    {
        // Тест кеширования
        Cache::put('test_key', 'Hello Redis!', 60);
        $value = Cache::get('test_key');

        return [
            'cache' => $value,
            'redis_connection' => Redis::ping(),
        ];
    }


    public function testPage(): mixed
    {
        return Inertia::render('test/TestPage', [
            'categoriesTree' => Category::query()->withDepth()->get()->toTree(),
        ]);
    }
}
