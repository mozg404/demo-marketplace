<?php

namespace App\Http\Controllers\My\Product;

use App\Data\Products\ProductDetailedData;
use App\Data\Products\ProductForListingData;
use App\Data\Products\StockItemDetailedData;
use App\DTO\Product\StockCreateDto;
use App\DTO\Product\StockUpdateDto;
use App\Http\Controllers\Controller;
use App\Models\Product;
use App\Models\StockItem;
use App\Services\Product\ProductManager;
use App\Services\Toaster;
use App\Support\SeoBuilder;
use Illuminate\Http\RedirectResponse;
use Inertia\Inertia;
use Inertia\Response;

class StockController extends Controller
{
    public function __construct(
        private readonly Toaster $toaster,
    ) {
    }

    public function index(Product $product): Response
    {
        return Inertia::render('my/products/stock/StockIndexPage', [
            'product' => ProductDetailedData::from($product),
            'itemsPaginated' => StockItemDetailedData::collect($product->stockItems()->latest()->paginate(10)),
            'availableItemsCount' => $product->stockItems()->isAvailable()->count(),
            'reservedItemsCount' => $product->stockItems()->isReserved()->count(),
            'seo' => new SeoBuilder('Позиции товара #' . $product->id),
        ]);
    }

    public function create(Product $product): Response
    {
        return Inertia::render('my/products/stock/StockItemCreateModal', [
            'product' => ProductForListingData::from($product),
        ]);
    }

    public function store(
        Product $product,
        StockCreateDto $dto,
        ProductManager $manager,
    ): RedirectResponse {
        try {
            $manager->createStockItem($product, $dto);
            $this->toaster->success("Позиция успешно добавлена");

            return back();
        } catch (\Exception $exception) {
            return back()->withErrors(['content' => $exception->getMessage()]);
        }
    }

    public function edit(Product $product, StockItem $stockItem): Response
    {
        return Inertia::render('my/products/stock/StockItemEditModal', [
            'stockItem' => StockItemDetailedData::from($stockItem),
            'product' => ProductForListingData::from($product),
        ]);
    }

    public function update(
        Product $product,
        StockItem $stockItem,
        StockUpdateDto $dto,
        ProductManager $manager,
    ): RedirectResponse {
        try {
            $manager->updateStockItem($stockItem, $dto);
            $this->toaster->success("Позиция успешно изменена");

            return back();
        } catch (\Exception $exception) {
            return back()->withErrors(['content' => $exception->getMessage()]);
        }
    }
}
