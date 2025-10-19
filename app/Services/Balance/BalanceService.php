<?php

namespace App\Services\Balance;

use App\Exceptions\Balance\InsufficientFundsException;
use App\Exceptions\Balance\NegativeAmountException;
use App\Exceptions\Balance\ZeroAmountException;
use Throwable;
use App\Contracts\Transactionable;
use App\Enum\TransactionType;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

readonly class BalanceService
{
    /**
     * @throws Throwable
     */
    public function deposit(User $user, int $amount, TransactionType $type, ?Transactionable $transactionable = null): Transaction
    {
        $this->validateAmount($amount);

        return DB::transaction(function () use ($user, $amount, $type, $transactionable) {
            $user->increment('balance', $amount);

            return $this->createTransaction($user, $amount, $type, $transactionable);
        });
    }

    /**
     * @throws Throwable
     */
    public function withdraw(User $user, int $amount, TransactionType $type, ?Transactionable $transactionable = null): Transaction
    {
        $this->validateAmount($amount);

        return DB::transaction(function () use ($user, $amount, $type, $transactionable) {
            if ($user->balance < $amount) {
                throw new InsufficientFundsException();
            }

            $user->decrement('balance', $amount);

            return $this->createTransaction($user, -$amount, $type, $transactionable);
        });
    }

    private function createTransaction(User $user, int $amount, TransactionType $type, ?Transactionable $transactionable = null): Transaction
    {
        return $user->transactions()->create([
            'amount' => $amount,
            'type' => $type->value,
            'transactionable_type' => $transactionable?->getTransactionableType(),
            'transactionable_id' => $transactionable?->getTransactionableId(),
            'created_at' => now()->toDateTimeString(),
        ]);
    }

    /**
     * @param int $amount
     * @throws Throwable
     */
    private function validateAmount(int $amount): void
    {
        throw_if($amount < 0, new NegativeAmountException());
        throw_if($amount === 0, new ZeroAmountException());
    }
}