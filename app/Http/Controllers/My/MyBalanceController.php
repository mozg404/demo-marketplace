<?php

namespace App\Http\Controllers\My;

use App\Data\TransactionData;
use App\Http\Controllers\Controller;
use App\Models\Transaction;
use App\Services\PaymentGateway\PaymentService;
use App\Support\SeoBuilder;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Inertia\Inertia;
use Inertia\Response;

class MyBalanceController extends Controller
{
    public function index(): Response
    {
        $transactions = Transaction::query()
            ->whereUser(Auth::id())
            ->descOrder()
            ->paginate(config('project.cabinet_transactions_count'));

        return Inertia::render('my/balance/BalanceIndexPage', [
            'pagination' => TransactionData::collect($transactions),
            'seo' => new SeoBuilder('Мой баланс'),
        ]);
    }

    public function deposit(Request $request, PaymentService $paymentService): RedirectResponse
    {
        $data = $request->validate([
            'amount' => 'required|numeric'
        ]);

        return redirect($paymentService->getPaymentUrl(
            $paymentService->createForDeposit(auth()->user(), $data['amount'])
        ));
    }
}
