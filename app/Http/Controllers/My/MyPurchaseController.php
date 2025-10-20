<?php

namespace App\Http\Controllers\My;

use App\Data\Purchased\PurchasedItemContentData;
use App\Data\Purchased\PurchasedItemForListingData;
use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Services\Order\OrderItemQuery;
use App\Support\SeoBuilder;
use Inertia\Inertia;
use Inertia\Response;

class MyPurchaseController extends Controller
{
    public function index(OrderItemQuery $orderItemQuery): Response
    {
        $purchasedItems = $orderItemQuery->query()
            ->withOrder()
            ->withStockItem()
            ->withProduct()
            ->withSeller()
            ->withProductCategory()
            ->withFeedback()
            ->isCompleted()
            ->whereBuyer(auth()->user())
            ->paginate(config('project.cabinet_purchases_count'));

        return Inertia::render('my/purchases/PurchasedIndexPage', [
            'purchasedItems' => PurchasedItemForListingData::collect($purchasedItems),
            'seo' => new SeoBuilder('Мои покупки'),
        ]);
    }

    public function content(OrderItem $orderItem): Response
    {
        return Inertia::render('my/purchases/PurchasedItemContentModal', [
            'purchasedItem' => PurchasedItemContentData::from($orderItem),
        ]);
    }
}
