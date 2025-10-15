<?php

namespace App\Data\Orders;

use App\Data\Products\ProductForListingData;
use App\Data\User\UserData;
use App\Models\Feedback;
use App\ValueObjects\Price;
use Spatie\LaravelData\Data;

class OrderItemForListingData extends Data
{
    public function __construct(
        public int $id,
        public int $order_id,
        public Price $price,
        public ProductForListingData $product,
        public UserData $seller,
        public ?Feedback $feedback = null,
    ) {
    }
}
