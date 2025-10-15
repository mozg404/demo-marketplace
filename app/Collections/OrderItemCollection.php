<?php

namespace App\Collections;

use App\Models\OrderItem;
use App\ValueObjects\Price;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property OrderItem[] $items
 *
 * @method OrderItem[] all()
 * @method OrderItem|mixed find($key, $default = null)
 */
class OrderItemCollection extends Collection
{
    /**
     * Возвращает общую цену заказа по позициям
     * @return Price
     */
    public function getTotalAmount(): Price
    {
        $price = new Price(0);

        foreach ($this->items as $item) {
            $price = $price->sumWith($item->price);
        }

        return $price;
    }
}