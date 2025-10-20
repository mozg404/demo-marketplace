<?php

namespace App\Http\Controllers\My;

use App\Data\Orders\OrderForListingData;
use App\Data\Orders\OrderItemForListingData;
use App\Exceptions\Balance\InsufficientFundsException;
use App\Exceptions\Order\CompletedOrderCannotBeCanceledException;
use App\Exceptions\Order\OrderAlreadyCanceledException;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Services\Order\OrderCancelService;
use App\Services\Order\OrderProcessor;
use App\Services\PaymentGateway\PaymentService;
use App\Services\Toaster;
use App\Support\SeoBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class MyOrderController extends Controller
{
    public function __construct(
        private readonly Toaster $toaster,
    ) {
    }

    public function index(): Response
    {
        $orders = Order::query()
            ->whereUser(Auth::id())
            ->withItemsCount()
            ->latest()
            ->paginate(config('cabinet.cabinet_orders_count'));

        return Inertia::render('my/orders/OrderIndexPage', [
            'orders' => OrderForListingData::collect($orders),
            'seo' => new SeoBuilder('Мои заказы'),
        ]);
    }

    public function show(Order $order): Response
    {
        $items = $order->items()
            ->withProduct()
            ->withSeller()
            ->withFeedback()
            ->get();

        return Inertia::render('my/orders/OrderShowPage', [
            'order' => OrderForListingData::from($order),
            'items' => OrderItemForListingData::collect($items),
            'totalAmount' => $items->getTotalAmount(),
            'totalCount' => $items->count(),
            'seo' => new SeoBuilder($order),
        ]);
    }

    public function pay(
        Order $order,
        OrderProcessor $processor,
        PaymentService $paymentService,
    ): RedirectResponse {
        try {
            $processor->process($order);
            $this->toaster->success('Заказ успешно оплачен');

            return redirect()->route('my.orders.show', $order->id);
        } catch (InsufficientFundsException $e) {
            return redirect($paymentService->getPaymentUrl(
                $paymentService->createForOrder($order)
            ));
        }
    }

    public function cancel(
        Order $order,
        OrderCancelService $cancelService,
    ): RedirectResponse {
        try {
            $cancelService->cancelOrder($order);
            $this->toaster->info('Заказ #'.$order->id.' отменен');

            return back();
        } catch (CompletedOrderCannotBeCanceledException|OrderAlreadyCanceledException $e) {
            $this->toaster->error($e->getMessage());

            return back();
        }
    }
}
