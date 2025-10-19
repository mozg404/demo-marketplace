<?php

namespace App\Services\Price;

use App\Contracts\HasQuantityPrice;
use App\ValueObjects\Price;
use Illuminate\Support\Collection;

class PriceService
{
    /**
     * @param array<HasQuantityPrice>|Collection<HasQuantityPrice> $items
     */
    public function calculateTotalQuantityPrice(array|Collection $items): Price
    {
        $total = new Price(0);

        foreach ($items as $item) {
            $total = $total->sumWith(
                $item->getPrice()->multiply($item->getQuantity())
            );
        }

        return $total;
    }
}