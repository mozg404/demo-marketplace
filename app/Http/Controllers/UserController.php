<?php

namespace App\Http\Controllers;

use App\Data\Products\ProductForListingData;
use App\Data\User\UserForListingData;
use App\Data\User\UserData;
use App\Models\Product;
use App\Models\User;
use App\Support\SeoBuilder;
use Inertia\Inertia;
use Inertia\Response;

class UserController extends Controller
{
    public function index(): Response
    {
        $users = User::query()
            ->withMedia()
            ->withAvailableProductsCount()
            ->hasAvailableProducts()
            ->paginate(config('project.sellers_count'));

        return Inertia::render('users/UsersIndexPage', [
            'users' => UserForListingData::collect($users),
            'seo' => new SeoBuilder('Продавцы'),
        ]);
    }

    public function show(User $user): Response
    {
        $products = Product::query()
            ->forListingPreset()
            ->whereSeller($user)
            ->latest()
            ->paginate(config('project.seller_latest_products_count'));

        return Inertia::render('users/UsersShowPage', [
            'products' => ProductForListingData::collect($products),
            'concreateUser' => UserData::from($user),
            'seo' => new SeoBuilder($user),
        ]);
    }
}
