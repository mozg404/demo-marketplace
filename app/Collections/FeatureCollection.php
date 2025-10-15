<?php

namespace App\Collections;

use App\Models\Feature;
use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property Feature[] $items
 *
 * @method Feature[] all()
 * @method Feature|mixed find($key, $default = null)
 */
class FeatureCollection extends Collection
{
    public function toIdValuePairs(): array
    {
        return $this->mapWithKeys(function ($feature) {
            return [
                $feature->id => $feature->pivot->value
            ];
        })->all();
    }
}