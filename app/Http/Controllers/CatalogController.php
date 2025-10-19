<?php

namespace App\Http\Controllers;

use App\Data\Feedback\FeedbackData;
use App\Data\Products\ProductDetailedData;
use App\Data\Products\ProductForListingData;
use App\Http\Requests\Catalog\CatalogCategoryFilterableRequest;
use App\Http\Requests\Catalog\CatalogFilterableRequest;
use App\Models\Category;
use App\Models\Product;
use App\Services\Category\CategoryQuery;
use App\Services\Feature\FeatureQuery;
use App\Services\Product\ProductQuery;
use App\Support\SeoBuilder;
use Inertia\Inertia;
use Inertia\Response;

class CatalogController extends Controller
{
    public function index(
        CatalogFilterableRequest $request,
        CategoryQuery $categoryQuery,
        ProductQuery $productQuery,
    ): Response {
        $categories = $categoryQuery->query()->get()->toTree();
        $products = $productQuery->query()
            ->forListingPreset()
            ->filterFromArray($request->getValues())
            ->paginate(20)
            ->appends($request->getValues());

        return Inertia::render('catalog/CatalogPage', [
            'filtersValues' => $request->getValues(),
            'categories' => $categories,
            'products' => ProductForListingData::collect($products),
            'seo' => new SeoBuilder('Каталог товаров'),
        ]);
    }

    public function category(
        Category $category,
        CatalogCategoryFilterableRequest $request,
        CategoryQuery $categoryQuery,
        ProductQuery $productQuery,
        FeatureQuery $featureQuery,
    ): Response {
        $categories = $categoryQuery->query()->get()->toTree();
        $features = $featureQuery->query()->forCategoryAndAncestors($category)->get();
        $products = $productQuery->query()
            ->forListingPreset()
            ->whereCategoryAndDescendants($category)
            ->filterFromArray($request->getValues())
            ->paginate(20)
            ->appends($request->getValues());

        return Inertia::render('catalog/CatalogCategoryPage', [
            'category' => $category,
            'features' => $features,
            'filtersValues' => $request->getValues(),
            'categories' => $categories,
            'products' => ProductForListingData::collect($products),
            'seo' => new SeoBuilder($category),
        ]);
    }

    public function show(Product $product): Response
    {
        if ($product->isDraft() && (!auth()->check() || auth()->id() !== $product->user_id)) {
            abort(403);
        }

        $feedbacks = $product->feedbacks()
            ->hasComments()
            ->withUser()
            ->latest()
            ->get();

        return Inertia::render('catalog/CatalogProductShowPage', [
            'product' => ProductDetailedData::from($product),
            'feedbacks' => FeedbackData::collect($feedbacks),
            'isOwner' => auth()?->id() === $product->user_id,
            'seo' => new SeoBuilder($product),
        ]);
    }
}
