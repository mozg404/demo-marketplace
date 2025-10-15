<?php

namespace App\Data\Orders;

use App\Enum\OrderStatus;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class OrderForListingData extends Data
{
    public function __construct(
        public int $id,
        public int $amount,
        public OrderStatus $status,
        public ?int $items_count  = null,
        public Carbon $created_at,
    ) {
    }

}
