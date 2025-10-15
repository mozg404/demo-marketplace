<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use App\Collections\OrderItemCollection;
use App\Contracts\Seoble;
use App\Contracts\Sourceable;
use App\Contracts\Transactionable;
use App\Enum\OrderStatus;
use App\Builders\OrderItemQueryBuilder;
use App\Builders\OrderQueryBuilder;
use App\Builders\UserQueryBuilder;
use App\Support\SeoBuilder;
use Carbon\Carbon;
use Database\Factories\OrderFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * /**
 *
 * @property int $id
 * @property int $user_id
 * @property int $amount
 * @property OrderStatus $status
 * @property \Illuminate\Support\Carbon|null $paid_at
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read OrderItemCollection<int, \App\Models\OrderItem> $items
 * @property-read int|null $items_count
 * @property-read \App\Models\User $user
 * @method static OrderQueryBuilder<static>|Order descOrder()
 * @method static \Database\Factories\OrderFactory factory($count = null, $state = [])
 * @method static OrderQueryBuilder<static>|Order isCompleted()
 * @method static OrderQueryBuilder<static>|Order isNew()
 * @method static OrderQueryBuilder<static>|Order newModelQuery()
 * @method static OrderQueryBuilder<static>|Order newQuery()
 * @method static OrderQueryBuilder<static>|Order query()
 * @method static OrderQueryBuilder<static>|Order whereAmount($value)
 * @method static OrderQueryBuilder<static>|Order whereCreatedAt($value)
 * @method static OrderQueryBuilder<static>|Order whereId($value)
 * @method static OrderQueryBuilder<static>|Order wherePaidAt($value)
 * @method static OrderQueryBuilder<static>|Order whereStatus($value)
 * @method static OrderQueryBuilder<static>|Order whereUpdatedAt($value)
 * @method static OrderQueryBuilder<static>|Order whereUser(int $id)
 * @method static OrderQueryBuilder<static>|Order whereUserId($value)
 * @method static OrderQueryBuilder<static>|Order withItems()
 * @method static OrderQueryBuilder<static>|Order withItemsCount()
 * @method static OrderQueryBuilder<static>|Order withProductSellers()
 * @method static OrderQueryBuilder<static>|Order withProducts()
 * @method static OrderQueryBuilder<static>|Order withStockItems()
 * @method static OrderQueryBuilder<static>|Order withUser()
 * @mixin \Eloquent
 */
class Order extends Model implements Transactionable, Sourceable, Seoble
{
    use HasFactory;

    protected $casts = [
        'status' => OrderStatus::class,
        'paid_at' => 'datetime',
    ];

    protected $fillable = [
        'user_id',
        'status',
        'amount',
    ];

    public function isPending(): bool
    {
        return $this->status === OrderStatus::PENDING;
    }

    public function isCompleted(): bool
    {
        return $this->status === OrderStatus::COMPLETED;
    }

    public function isCancelled(): bool
    {
        return $this->status === OrderStatus::CANCELLED;
    }

    /**
     * Помечает заказ, как оплаченный
     */
    public function markAsPaid(): static
    {
        $this->status = OrderStatus::COMPLETED;
        $this->paid_at = Carbon::now();
        $this->save();

        return $this;
    }

    public function seo(): SeoBuilder
    {
        return new SeoBuilder('Заказ #' . $this->id);
    }

    public function getTransactionableType(): string
    {
        return $this::class;
    }

    public function getTransactionableId(): int
    {
        return $this->id;
    }

    public function getSourceableType(): string
    {
        return $this::class;
    }

    public function getSourceableId(): int
    {
        return $this->id;
    }

    public function user(): BelongsTo|UserQueryBuilder
    {
        return $this->belongsTo(User::class);
    }

    public function items(): HasMany|OrderItemQueryBuilder
    {
        return $this->hasMany(OrderItem::class);
    }

    public function newEloquentBuilder($query): OrderQueryBuilder
    {
        return new OrderQueryBuilder($query);
    }

    protected static function newFactory(): OrderFactory|Factory
    {
        return OrderFactory::new();
    }
}
