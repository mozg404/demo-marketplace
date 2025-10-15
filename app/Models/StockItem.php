<?php

namespace App\Models;

use App\Builders\UserQueryBuilder;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Carbon;
use App\Builders\OrderItemQueryBuilder;
use App\Builders\ProductQueryBuilder;
use App\Enum\StockItemStatus;
use App\Builders\StockItemQueryBuilder;
use Database\Factories\StockItemFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $product_id
 * @property StockItemStatus $status
 * @property string $content
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int|null $order_item_id
 * @property-read \App\Models\OrderItem|null $orderItem
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\User|null $seller
 * @method static StockItemQueryBuilder<static>|StockItem canByPurchased()
 * @method static \Database\Factories\StockItemFactory factory($count = null, $state = [])
 * @method static StockItemQueryBuilder<static>|StockItem forProduct(\App\Models\Product|int $id)
 * @method static StockItemQueryBuilder<static>|StockItem forUser(\App\Models\User|int $user)
 * @method static StockItemQueryBuilder<static>|StockItem isActiveProduct()
 * @method static StockItemQueryBuilder<static>|StockItem isAvailable()
 * @method static StockItemQueryBuilder<static>|StockItem isReserved()
 * @method static StockItemQueryBuilder<static>|StockItem newModelQuery()
 * @method static StockItemQueryBuilder<static>|StockItem newQuery()
 * @method static StockItemQueryBuilder<static>|StockItem query()
 * @method static StockItemQueryBuilder<static>|StockItem whereContent($value)
 * @method static StockItemQueryBuilder<static>|StockItem whereCreatedAt($value)
 * @method static StockItemQueryBuilder<static>|StockItem whereId($value)
 * @method static StockItemQueryBuilder<static>|StockItem whereNotBelongsToUser(\App\Models\User|int $user)
 * @method static StockItemQueryBuilder<static>|StockItem whereOrderItemId($value)
 * @method static StockItemQueryBuilder<static>|StockItem whereProductId($value)
 * @method static StockItemQueryBuilder<static>|StockItem whereStatus($value)
 * @method static StockItemQueryBuilder<static>|StockItem whereUpdatedAt($value)
 * @method static StockItemQueryBuilder<static>|StockItem withProduct()
 * @method static StockItemQueryBuilder<static>|StockItem withSeller()
 * @mixin \Eloquent
 */
class StockItem extends Model
{
    use HasFactory;

    protected $table = 'stock_items';

    protected $fillable = ['product_id', 'order_item_id', 'status', 'content'];

    protected $hidden = ['content'];

    protected function casts(): array
    {
        return [
            'status' => StockItemStatus::class,
            'pinned_at' => 'datetime',
        ];
    }

    public function isAvailable(): bool
    {
        return $this->status === StockItemStatus::AVAILABLE;
    }

    public function isReserved(): bool
    {
        return $this->status === StockItemStatus::RESERVED;
    }

    public function product(): BelongsTo|ProductQueryBuilder
    {
        return $this->belongsTo(Product::class);
    }

    public function orderItem(): BelongsTo|OrderItemQueryBuilder|null
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function seller(): HasOneThrough|UserQueryBuilder
    {
        return $this->hasOneThrough(
            User::class,
            Product::class,
            'id',
            'id',
            'product_id',
            'user_id'
        );
    }

    public function newEloquentBuilder($query): StockItemQueryBuilder
    {
        return new StockItemQueryBuilder($query);
    }

    protected static function newFactory(): StockItemFactory|Factory
    {
        return StockItemFactory::new();
    }
}