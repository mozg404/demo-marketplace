<?php

declare(strict_types=1);

namespace App\Data\Products;

use App\Data\User\UserData;
use App\Enum\ProductStatus;
use App\ValueObjects\Price;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class ProductData extends Data
{
    public function __construct(
        public int $id,
        public string $name,
        public Price $price,
        public ?ProductPreviewData $preview,
        public ProductStatus $status,
        public ?UserData $user = null,
        public Carbon $created_at,
        public ?int $stock_items_count = null,
        public ?int $available_stock_items_count = null,
        public ?int $reserved_stock_items_count = null,
    ) {
    }
}
