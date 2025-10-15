<?php

namespace App\Services\Product;

use App\Models\Product;
use Illuminate\Support\Facades\DB;

class ProductFeatureAttacher
{
    public function attachAllFromArray(Product $product, array $features): void
    {
        DB::transaction(function () use ($product, $features) {
            $this->detachAll($product);

            foreach ($features as $id => $value) {
                if (!empty($value)) {
                    $product->features()->attach($id, ['value' => $value]);
                }
            }
        });
    }

    public function detachAll(Product $product): void
    {
        $product->features()->detach();
    }
}