<?php

namespace App\Http\Controllers;

use App\Jobs\CreateRandomUser;
use App\Jobs\CreateSpecificProduct;
use App\Models\Category;
use App\Models\User;
use App\Services\Demo\DemoProductList;
use App\Services\User\UserAvatarChanger;
use App\Services\User\UserRegistrar;
use Illuminate\Http\Request;
use Inertia\Inertia;

class TestController extends Controller
{

    public function __construct()
    {}

    public function test(
        DemoProductList $productList,
    ): mixed
    {

        $raw = $productList->raw()[3];

        CreateSpecificProduct::dispatch($productList->toData($raw));
//        CreateSpecificDemoProduct::dispatch($raw);

//        CreateSpecificDemoProduct::dispatch([1,2,3]);
//        CreateSpecificDemoProduct::dispatch();


        return  123;
    }


    public function testPage(): mixed
    {
        return Inertia::render('test/TestPage', [
            'categoriesTree' => Category::query()->withDepth()->get()->toTree(),
        ]);
    }
}
