<?php

namespace App\Http\Controllers;

use Throwable;
use App\Contracts\PaymentGateway;
use App\Exceptions\Payment\PaymentAlreadyCompletedException;
use App\Exceptions\Payment\PaymentCancelledException;
use App\Exceptions\Payment\PaymentFailedException;
use App\Models\Payment;
use App\Services\PaymentGateway\PaymentProcessor;
use App\Services\Toaster;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class PaymentCallbackController extends Controller
{
    public function __invoke(
        Request $request,
        PaymentGateway $gateway,
        PaymentProcessor $processor,
        Toaster $toaster
    ): RedirectResponse {
        try {
            $payment = $gateway->validateCallback($request->all());

            try {
                $processor->process($payment);

                if ($payment->isOrderSource()) {
                    $toaster->success('Заказ успешно оплачен');
                } else {
                    $toaster->success('Счет успешно пополнен');
                }

                return $this->redirectTo($payment);
            } catch (PaymentAlreadyCompletedException|PaymentCancelledException|PaymentFailedException $e) {
                $toaster->error($e->getMessage());

                return $this->redirectTo($payment);
            }
        } catch (Throwable $e) {
            report($e);
            $toaster->error('Ошибка при оплате');

            return back();
        }
    }

    private function redirectTo(Payment $payment): RedirectResponse
    {
        if ($payment->isOrderSource()) {
            return redirect()->route('my.orders.show', $payment->sourceable->id);
        }

        return redirect()->route('my.balance');
    }
}
