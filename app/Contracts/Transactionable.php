<?php

namespace App\Contracts;

interface Transactionable
{
    public function getTransactionableType(): string;
    public function getTransactionableId(): int;
}