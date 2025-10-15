<?php

namespace App\Http\Controllers;

use Throwable;
use App\Exceptions\Balance\InsufficientFundsException;
use App\Exceptions\Product\ProductUnavailableException;
use App\Exceptions\Product\NotEnoughStockException;
use App\Models\Product;
use App\Services\Order\ExpressOrderCreator;
use App\Services\Order\OrderFromCartCreator;
use App\Services\Order\OrderProcessor;
use App\Services\PaymentGateway\PaymentService;
use App\Services\Toaster;
use Illuminate\Http\RedirectResponse;

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
            $user = auth()->user();
            $order = $creator->create($user);

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
        Product $product,
        ExpressOrderCreator $creator,
        OrderProcessor $processor,
        PaymentService $paymentService,
    ): RedirectResponse {
        try {
            $user = auth()->user();
            $order = $creator->create($user, $product);

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
