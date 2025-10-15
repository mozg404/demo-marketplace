<?php

declare(strict_types=1);

namespace App\Data\Products;

use App\Enum\StockItemStatus;
use Illuminate\Support\Carbon;
use Spatie\LaravelData\Data;

class StockItemDetailedData extends Data
{
    public function __construct(
        public int $id,
        public int $product_id,
        public StockItemStatus $status,
        public string $content,
        public Carbon $created_at,
        public Carbon $updated_at,
    ){}
}
