<?php

namespace App\Collections;

use App\Data\Orders\CreatableOrderItemData;
use App\Models\Product;
use App\ValueObjects\Price;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property Product[] $items
 *
 * @method Product[] all()
 * @method Product first(?callable $callback = null, $default = null)()
 * @method Product|mixed find($key, $default = null)
 */
class ProductCollection extends Collection
{
    public function getTotalPrice(): Price
    {
        $price = new Price(0);

        $this->each(function (Product $product) use (&$price) {
            $price = $price->sumWith($product->price)->multiply($item->quantity);
        });

        return $price;
    }
}