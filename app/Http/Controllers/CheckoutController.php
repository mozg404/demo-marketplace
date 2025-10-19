<?php

namespace App\Http\Controllers;

use App\Exceptions\Balance\InsufficientFundsException;
use App\Exceptions\Product\NotEnoughStockException;
use App\Exceptions\Product\ProductUnavailableException;
use App\Services\Order\OrderCreator;
use App\Services\Order\OrderFromCartCreator;
use App\Services\Order\OrderProcessor;
use App\Services\PaymentGateway\PaymentService;
use App\Services\Product\DTO\PurchasableItem;
use App\Services\Toaster;
use Illuminate\Http\RedirectResponse;
use Throwable;

class CheckoutController extends Controller
{
    public function __construct(
        private readonly Toaster $toaster,
    ) {
    }

    public function cart(
        OrderFromCartCreator $creator,
        OrderProcessor $processor,
        PaymentService $paymentService,
    ): RedirectResponse {
        try {
            $order = $creator->create(auth()->id());

            try {
                $processor->process($order);
                $this->toaster->success('Заказ успешно оплачен');

                return redirect()->route('my.orders.show', $order->id);
            } catch (InsufficientFundsException $e) {
                return redirect($paymentService->getPaymentUrl(
                    $paymentService->createForOrder($order)
                ));
            }
        } catch (NotEnoughStockException|ProductUnavailableException $e) {
            $this->toaster->error($e->getMessage());

            return back();
        } catch (Throwable $e) {
            report($e);
            $this->toaster->error('Ошибка при оформлении заказа');

            return back();
        }
    }

    public function express(
        int $productId,
        OrderCreator $creator,
        OrderProcessor $processor,
        PaymentService $paymentService,
    ): RedirectResponse {
        try {
            $order = $creator->createExpress(auth()->id(), new PurchasableItem($productId));

            try {
                $processor->process($order);
                $this->toaster->success('Заказ успешно оплачен');

                return redirect()->route('my.orders.show', $order->id);
            } catch (InsufficientFundsException $e) {
                return redirect($paymentService->getPaymentUrl(
                    $paymentService->createForOrder($order)
                ));
            }
        } catch (NotEnoughStockException|ProductUnavailableException $e) {
            $this->toaster->error($e->getMessage());

            return back();
        } catch (Throwable $e) {
            report($e);
            $this->toaster->error('Ошибка при оформлении заказа');

            return back();
        }
    }
}
