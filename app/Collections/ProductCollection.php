<?php

namespace App\Collections;

use App\Models\Product;
use Illuminate\Database\Eloquent\Collection;

/**
 * @property Product[] $items
 *
 * @method Product[] all()
 * @method Product|mixed find($key, $default = null)
 */
class ProductCollection extends Collection
{

}