<?php

namespace App\Services\Balance;

use InvalidArgumentException;
use Throwable;
use App\Enum\TransactionType;
use App\Models\Transaction;
use App\Models\User;

readonly class BalanceAdjuster
{
    public function __construct(
        private BalanceService $balanceService,
    )
    {}

    /**
     * @throws Throwable
     */
    public function setBalance(User $user, int $targetBalance): Transaction
    {
        if ($targetBalance < 0) {
            throw new InvalidArgumentException('Баланс не может быть отрицательным');
        }

        $difference = $targetBalance - $user->balance;

        if ($difference === 0) {
            throw new InvalidArgumentException('Баланс итак целевой');
        }

        if ($difference > 0) {
            return $this->balanceService->deposit($user, $difference, TransactionType::ADMIN_CORRECTION);
        }

        return $this->balanceService->withdraw($user, abs($difference), TransactionType::ADMIN_CORRECTION);
    }
}