<?php

namespace App\Http\Controllers;

use App\Http\Requests\SearchStoreRequest;
use App\Services\Product\ProductQuery;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

class SearchController extends Controller
{
    public function form(Request $request): Response
    {
        return Inertia::render('SearchModal', [
            'searchValue' => $request->input('search'),
        ]);
    }

    public function store(SearchStoreRequest $request, ProductQuery $productQuery): Collection
    {
        return $productQuery->search($request->input('search'));
    }
}
