<?php

namespace App\Data\Cart;

use App\ValueObjects\Price;
use Illuminate\Support\Collection;
use Spatie\LaravelData\Data;

class CartData extends Data
{
    public Price $amount;
    public int $count = 0;
    public int $quantity = 0;

    public function __construct(
        /** @var CartItemData[] $items */
        public array|Collection $items = [],
    ) {
        $this->count = count($this->items);

        foreach ($items as $item) {
            if (!isset($this->amount)) {
                $this->amount = $item->amount;
            } else {
                $this->amount = $this->amount->sumWith($item->amount);
            }

            $this->quantity += $item->quantity;
        }

        if (!isset($this->amount)) {
            $this->amount = new Price(0);
        }
    }
}
