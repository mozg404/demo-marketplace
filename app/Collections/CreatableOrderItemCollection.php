<?php

namespace App\Collections;

use App\Data\Orders\CreatableOrderItemData;
use App\Models\Product;
use App\ValueObjects\Price;
use Illuminate\Support\Collection;

/**
 * @property CreatableOrderItemData[] $items
 */
class CreatableOrderItemCollection extends Collection
{
    public function getTotalPrice(): Price
    {
        $price = new Price(0);

        $this->each(function (CreatableOrderItemData $item) use (&$price) {
            $price = $price->sumWith($item->product->price)->multiply($item->quantity);
        });

        return $price;
    }

    public static function fromProductCollection(ProductCollection $products): self
    {
        $items = new self();

        $products->each(function (Product $product) use ($items) {
            $items->push(CreatableOrderItemData::from($product));
        });

        return $items;
    }
}