<?php

namespace App\Services\Balance;

use Throwable;
use App\Contracts\Transactionable;
use App\Enum\TransactionType;
use App\Exceptions\Balance\InsufficientFundsException;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Support\Facades\DB;

readonly class BalanceService
{
    public function __construct(
        private BalanceChecker $balanceChecker,
    )
    {}

    /**
     * @throws Throwable
     */
    public function deposit(User $user, int $amount, TransactionType $type, ?Transactionable $transactionable = null): Transaction
    {
        $this->balanceChecker->ensureNonNegativeAmount($amount);
        $this->balanceChecker->ensureNonZeroAmount($amount);

        return DB::transaction(function () use ($user, $amount, $type, $transactionable) {
            $user->increment('balance', $amount);

            return $user->transactions()->create([
                'amount' => $amount,
                'type' => $type->value,
                'transactionable_type' => $transactionable?->getTransactionableType(),
                'transactionable_id' => $transactionable?->getTransactionableId(),
                'created_at' => now()->toDateTimeString(),
            ]);
        });
    }

    /**
     * @throws Throwable
     */
    public function withdraw(User $user, int $amount, TransactionType $type, ?Transactionable $transactionable = null): Transaction
    {
        $this->balanceChecker->ensureNonNegativeAmount($amount);
        $this->balanceChecker->ensureNonZeroAmount($amount);

        return DB::transaction(function () use ($user, $amount, $type, $transactionable) {
            $user->decrement('balance', $amount);

            return $user->transactions()->create([
                'amount' => -$amount,
                'type' => $type->value,
                'transactionable_type' => $transactionable?->getTransactionableType(),
                'transactionable_id' => $transactionable?->getTransactionableId(),
                'created_at' => now()->toDateTimeString(),
            ]);
        });
    }

    /**
     * Выбрасывает исключение, если баланса недостаточно
     */
    public function ensureSufficientFunds(User $user, int $amount): void
    {
        if (!$this->hasSufficientFunds($user, $amount)) {
            throw new InsufficientFundsException();
        }
    }
    
    /**
     * Проверка на достаточный баланс у пользователя
     */
    public function hasSufficientFunds(User $user, int $amount): bool
    {
        return $user->hasEnoughBalance($amount);
    }
}