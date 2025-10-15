<?php

namespace App\Models;

use Illuminate\Support\Carbon;
use App\Enum\TransactionType;
use App\Builders\TransactionQueryBuilder;
use App\Builders\UserQueryBuilder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/**
 * @property int $id
 * @property int $user_id
 * @property int $amount
 * @property TransactionType $type
 * @property string|null $transactionable_type
 * @property int|null $transactionable_id
 * @property Carbon|null $created_at
 * @property-read Model|\Eloquent|null $transactionable
 * @property-read \App\Models\User $user
 * @method static TransactionQueryBuilder<static>|Transaction descOrder()
 * @method static TransactionQueryBuilder<static>|Transaction newModelQuery()
 * @method static TransactionQueryBuilder<static>|Transaction newQuery()
 * @method static TransactionQueryBuilder<static>|Transaction query()
 * @method static TransactionQueryBuilder<static>|Transaction whereAmount($value)
 * @method static TransactionQueryBuilder<static>|Transaction whereCreatedAt($value)
 * @method static TransactionQueryBuilder<static>|Transaction whereId($value)
 * @method static TransactionQueryBuilder<static>|Transaction whereTransactionableId($value)
 * @method static TransactionQueryBuilder<static>|Transaction whereTransactionableType($value)
 * @method static TransactionQueryBuilder<static>|Transaction whereType($value)
 * @method static TransactionQueryBuilder<static>|Transaction whereUser(int $id)
 * @method static TransactionQueryBuilder<static>|Transaction whereUserId($value)
 * @mixin \Eloquent
 */
class Transaction extends Model
{
    public $timestamps = false;

    protected $casts = [
        'amount' => 'integer',
        'type' => TransactionType::class,
        'created_at' => 'datetime',
    ];

    protected $fillable = ['amount', 'type', 'transactionable_type', 'transactionable_id', 'created_at'];

    public function user(): BelongsTo|UserQueryBuilder
    {
        return $this->belongsTo(User::class);
    }

    public function transactionable(): MorphTo
    {
        return $this->morphTo();
    }

    public function newEloquentBuilder($query): TransactionQueryBuilder
    {
        return new TransactionQueryBuilder($query);
    }
}
