<?php

namespace App\Data;

use App\Enum\TransactionType;
use App\Models\Transaction;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class TransactionData extends Data
{
    public function __construct(
        public int $id,
        public TransactionType $type,
        public int $amount,
        public ?int $transactionable_id,
        public Carbon $created_at,
    ) {}

    public static function fromModel(Transaction $transaction): static
    {
        return new self(
            id: $transaction->id,
            type: $transaction->type,
            amount: $transaction->amount,
            transactionable_id: $transaction->transactionable_id,
            created_at: $transaction->created_at,
        );
    }
}
