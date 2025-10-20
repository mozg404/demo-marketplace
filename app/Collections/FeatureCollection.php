<?php

namespace App\Collections;

use App\Models\Feature;
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
        return $this->mapWithKeys(fn (Feature $feature) => [$feature->id => $feature->name])->all();
    }
}