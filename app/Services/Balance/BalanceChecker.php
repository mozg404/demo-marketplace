<?php

namespace App\Services\Balance;

use Throwable;
use App\Exceptions\Balance\NegativeAmountException;
use App\Exceptions\Balance\ZeroAmountException;

class BalanceChecker
{
    /**
     * @throws Throwable
     */
    public function ensureNonNegativeAmount(int $amount): void
    {
        throw_if($amount < 0, new NegativeAmountException());
    }

    /**
     * @throws Throwable
     */
    public function ensureNonZeroAmount(int $amount): void
    {
        throw_if($amount === 0, new ZeroAmountException());
    }
}