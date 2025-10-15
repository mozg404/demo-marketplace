<?php

namespace App\Models;

use App\Builders\FeedbackQueryBuilder;
use App\Builders\OrderItemQueryBuilder;
use App\Builders\ProductQueryBuilder;
use App\Builders\UserQueryBuilder;
use App\Observers\FeedbackObserver;
use Database\Factories\FeedbackFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $order_item_id
 * @property int $product_id
 * @property int $seller_id
 * @property bool $is_positive
 * @property string|null $comment
 * @property \Illuminate\Support\Carbon|null $created_at
 * @property \Illuminate\Support\Carbon|null $updated_at
 * @property-read \App\Models\OrderItem $orderItem
 * @property-read \App\Models\Product $product
 * @property-read \App\Models\User $seller
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\FeedbackFactory factory($count = null, $state = [])
 * @method static FeedbackQueryBuilder<static>|Feedback hasComments()
 * @method static FeedbackQueryBuilder<static>|Feedback newModelQuery()
 * @method static FeedbackQueryBuilder<static>|Feedback newQuery()
 * @method static FeedbackQueryBuilder<static>|Feedback query()
 * @method static FeedbackQueryBuilder<static>|Feedback whereComment($value)
 * @method static FeedbackQueryBuilder<static>|Feedback whereCreatedAt($value)
 * @method static FeedbackQueryBuilder<static>|Feedback whereId($value)
 * @method static FeedbackQueryBuilder<static>|Feedback whereIsPositive($value)
 * @method static FeedbackQueryBuilder<static>|Feedback whereOrderItemId($value)
 * @method static FeedbackQueryBuilder<static>|Feedback whereProductId($value)
 * @method static FeedbackQueryBuilder<static>|Feedback whereSellerId($value)
 * @method static FeedbackQueryBuilder<static>|Feedback whereUpdatedAt($value)
 * @method static FeedbackQueryBuilder<static>|Feedback whereUserId($value)
 * @method static FeedbackQueryBuilder<static>|Feedback withUser()
 * @mixin \Eloquent
 */
#[ObservedBy([FeedbackObserver::class])]
class Feedback extends Model
{
    use HasFactory;

    public $table = 'feedbacks';

    protected $fillable = [
        'user_id',
        'order_item_id',
        'product_id',
        'seller_id',
        'is_positive',
        'comment',
        'created_at',
    ];

    protected $casts = [
        'is_positive' => 'boolean',
    ];

    public function isNegative(): bool
    {
        return !$this->is_positive;
    }

    public function isPositive(): bool
    {
        return $this->is_positive;
    }

    public function hasComment(): bool
    {
        return isset($this->comment);
    }

    public function user(): BelongsTo|UserQueryBuilder
    {
        return $this->belongsTo(User::class);
    }

    public function orderItem(): BelongsTo|OrderItemQueryBuilder
    {
        return $this->belongsTo(OrderItem::class);
    }

    public function product(): BelongsTo|ProductQueryBuilder
    {
        return $this->belongsTo(Product::class);
    }

    public function seller(): BelongsTo|UserQueryBuilder
    {
        return $this->belongsTo(User::class, 'seller_id');
    }

    public function newEloquentBuilder($query): FeedbackQueryBuilder
    {
        return new FeedbackQueryBuilder($query);
    }

    protected static function newFactory(): FeedbackFactory
    {
        return FeedbackFactory::new();
    }
}
