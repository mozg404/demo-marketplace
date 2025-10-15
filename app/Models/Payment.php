<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Illuminate\Database\Eloquent\Builder;
use App\Contracts\Sourceable;
use App\Contracts\Transactionable;
use App\Enum\PaymentSource;
use App\Enum\PaymentStatus;
use App\Builders\UserQueryBuilder;
use Database\Factories\PaymentFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int $user_id
 * @property string|null $external_id
 * @property int $amount
 * @property PaymentStatus $status
 * @property PaymentSource|null $source
 * @property string|null $sourceable_type
 * @property int|null $sourceable_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read Model|\Eloquent|null $sourceable
 * @property-read \App\Models\User $user
 * @method static \Database\Factories\PaymentFactory factory($count = null, $state = [])
 * @method static Builder<static>|Payment newModelQuery()
 * @method static Builder<static>|Payment newQuery()
 * @method static Builder<static>|Payment query()
 * @method static Builder<static>|Payment whereAmount($value)
 * @method static Builder<static>|Payment whereCreatedAt($value)
 * @method static Builder<static>|Payment whereExternalId($value)
 * @method static Builder<static>|Payment whereId($value)
 * @method static Builder<static>|Payment whereSource($value)
 * @method static Builder<static>|Payment whereSourceableId($value)
 * @method static Builder<static>|Payment whereSourceableType($value)
 * @method static Builder<static>|Payment whereStatus($value)
 * @method static Builder<static>|Payment whereUpdatedAt($value)
 * @method static Builder<static>|Payment whereUserId($value)
 * @mixin \Eloquent
 */
class Payment extends Model implements Transactionable
{
    use HasFactory;

    protected $casts = [
        'amount' => 'integer',
        'status' => PaymentStatus::class,
        'source' => PaymentSource::class,
    ];

    protected $fillable = ['user_id', 'amount', 'status', 'source', 'sourceable_type', 'sourceable_id'];

    /**
     * Создает новый платеж
     * @param User $user
     * @param int $amount
     * @param PaymentSource $source
     * @param Sourceable|null $sourceable
     * @return static
     */
    public static function new(User $user, int $amount, PaymentSource $source, Sourceable $sourceable = null): static
    {
        return self::create([
            'user_id' => $user->id,
            'amount' => $amount,
            'status' => PaymentStatus::NEW,
            'source' => $source->value,
            'sourceable_type' => $sourceable?->getSourceableType(),
            'sourceable_id' => $sourceable?->getSourceableId(),
        ]);
    }

    public function isReplenishmentSource(): bool
    {
        return $this->source === PaymentSource::REPLENISHMENT;
    }

    public function isOrderSource(): bool
    {
        return $this->source === PaymentSource::ORDER;
    }

    public function markAsCompleted(): self
    {
        $this->status = PaymentStatus::COMPLETED;
        $this->save();

        return $this;
    }

    public function isCompleted(): bool
    {
        return $this->status === PaymentStatus::COMPLETED;
    }

    public function markAsSuccess(): self
    {
        $this->status = PaymentStatus::SUCCESS;
        $this->save();

        return $this;
    }

    public function isSuccess(): bool
    {
        return $this->status === PaymentStatus::SUCCESS;
    }

    public function markAsCancelled(): self
    {
        $this->status = PaymentStatus::CANCELLED;
        $this->save();

        return $this;
    }

    public function isCancelled(): bool
    {
        return $this->status === PaymentStatus::CANCELLED;
    }

    public function markAsFailed(): self
    {
        $this->status = PaymentStatus::FAILED;
        $this->save();

        return $this;
    }

    public function isFailed(): bool
    {
        return $this->status === PaymentStatus::FAILED;
    }

    public static function findByExternalId(string $hash): static|null
    {
        return self::where('external_id', $hash)->first();
    }

    public function getTransactionableType(): string
    {
        return $this::class;
    }

    public function getTransactionableId(): int
    {
        return $this->id;
    }

    public function sourceable(): MorphTo|Order
    {
        return $this->morphTo();
    }

    public function user(): BelongsTo|UserQueryBuilder
    {
        return $this->belongsTo(User::class);
    }

    protected static function newFactory(): PaymentFactory|Factory
    {
        return PaymentFactory::new();
    }
}
