<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Database\Factories\ProductFeatureValueFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * @property int $id
 * @property int $product_id
 * @property int $feature_id
 * @property string $value
 * @property-read \App\Models\Feature $feature
 * @property-read \App\Models\Product $product
 * @method static \Database\Factories\ProductFeatureValueFactory factory($count = null, $state = [])
 * @method static Builder<static>|ProductFeatureValue newModelQuery()
 * @method static Builder<static>|ProductFeatureValue newQuery()
 * @method static Builder<static>|ProductFeatureValue query()
 * @method static Builder<static>|ProductFeatureValue whereFeatureId($value)
 * @method static Builder<static>|ProductFeatureValue whereId($value)
 * @method static Builder<static>|ProductFeatureValue whereProductId($value)
 * @method static Builder<static>|ProductFeatureValue whereValue($value)
 * @mixin \Eloquent
 */
class ProductFeatureValue extends Pivot
{
    use HasFactory;

    public const string TABLE = 'product_feature_values';
    protected $table = self::TABLE;
    public $timestamps = false;
    protected $fillable = ['value'];

    public function feature(): BelongsTo
    {
        return $this->belongsTo(Feature::class);
    }

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    protected static function newFactory(): ProductFeatureValueFactory
    {
        return ProductFeatureValueFactory::new();
    }
}
