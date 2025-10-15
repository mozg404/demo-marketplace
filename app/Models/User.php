<?php

namespace App\Models;

use Illuminate\Notifications\DatabaseNotificationCollection;
use Illuminate\Notifications\DatabaseNotification;
use Illuminate\Database\Eloquent\Collection;
use App\Collections\ProductCollection;
use Database\Factories\UserFactory;
use App\Builders\UserQueryBuilder;
use App\Contracts\Seoble;
use App\Support\SeoBuilder;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Carbon;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Media;


/**
 * @property int $id
 * @property string $name
 * @property string $email
 * @property Carbon|null $email_verified_at
 * @property string $password
 * @property string|null $remember_token
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property int $balance
 * @property bool $is_admin
 * @property int $positive_feedbacks_count
 * @property int $negative_feedbacks_count
 * @property float $seller_rating
 * @property-read mixed $avatar
 * @property-read \Spatie\MediaLibrary\MediaCollections\Models\Collections\MediaCollection<int, Media> $media
 * @property-read int|null $media_count
 * @property-read DatabaseNotificationCollection<int, DatabaseNotification> $notifications
 * @property-read int|null $notifications_count
 * @property-read Collection<int, \App\Models\Payment> $payments
 * @property-read int|null $payments_count
 * @property-read ProductCollection<int, \App\Models\Product> $products
 * @property-read int|null $products_count
 * @property-read Collection<int, \App\Models\Transaction> $transactions
 * @property-read int|null $transactions_count
 * @method static UserQueryBuilder<static>|User checkExistsByEmail(string $email)
 * @method static \Database\Factories\UserFactory factory($count = null, $state = [])
 * @method static UserQueryBuilder<static>|User findByEmail(string $email)
 * @method static UserQueryBuilder<static>|User hasAvailableProducts()
 * @method static UserQueryBuilder<static>|User newModelQuery()
 * @method static UserQueryBuilder<static>|User newQuery()
 * @method static UserQueryBuilder<static>|User query()
 * @method static UserQueryBuilder<static>|User whereBalance($value)
 * @method static UserQueryBuilder<static>|User whereCreatedAt($value)
 * @method static UserQueryBuilder<static>|User whereEmail($value)
 * @method static UserQueryBuilder<static>|User whereEmailVerifiedAt($value)
 * @method static UserQueryBuilder<static>|User whereId($value)
 * @method static UserQueryBuilder<static>|User whereIsAdmin($value)
 * @method static UserQueryBuilder<static>|User whereName($value)
 * @method static UserQueryBuilder<static>|User whereNegativeFeedbacksCount($value)
 * @method static UserQueryBuilder<static>|User wherePassword($value)
 * @method static UserQueryBuilder<static>|User wherePositiveFeedbacksCount($value)
 * @method static UserQueryBuilder<static>|User whereRememberToken($value)
 * @method static UserQueryBuilder<static>|User whereSellerRating($value)
 * @method static UserQueryBuilder<static>|User whereUpdatedAt($value)
 * @method static UserQueryBuilder<static>|User withAvailableProductsCount()
 * @method static UserQueryBuilder<static>|User withMedia()
 * @method static UserQueryBuilder<static>|User withoutAdmin()
 * @mixin \Eloquent
 */
class User extends Authenticatable implements Seoble, MustVerifyEmail, HasMedia
{
    public const string MEDIA_COLLECTION_AVATAR = 'avatar';

    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable, InteractsWithMedia;

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    public function registerMediaCollections(): void
    {
        $this->addMediaCollection(self::MEDIA_COLLECTION_AVATAR)
            ->singleFile()
            ->registerMediaConversions(function (Media $media) {
                $this->addMediaConversion('thumb')
                    ->width(36)
                    ->height(36)
                    ->format('webp')
                    ->quality(75)
                    ->optimize();

                $this->addMediaConversion('thumb_2')
                    ->width(72)
                    ->height(72)
                    ->format('webp')
                    ->quality(75)
                    ->optimize();

                // Создается сразу
                $this->addMediaConversion('medium')
                    ->width(150)
                    ->height(150)
                    ->format('webp')
                    ->quality(80)
                    ->optimize()
                    ->nonQueued();

                $this->addMediaConversion('large')
                    ->width(300)
                    ->height(300)
                    ->format('webp')
                    ->quality(85)
                    ->optimize();
            });
    }

    protected function casts(): array
    {
        return [
            'is_admin' => 'bool',
            'balance' => 'int',
            'email_verified_at' => 'datetime',
//            'password' => 'hashed', // Убрано, так как хэш создает сервис регистрации
        ];
    }

    protected function avatar(): Attribute
    {
        return Attribute::get(function () {
            $media = $this->getFirstMedia(self::MEDIA_COLLECTION_AVATAR);

            if (!$media) {
                return null;
            }

            $mediumUrl = $media->getUrl('medium');

            return [
                'thumb' => $media->hasGeneratedConversion('thumb') ? $media->getUrl('thumb') : $mediumUrl,
                'thumb_2' => $media->hasGeneratedConversion('thumb_2') ? $media->getUrl('thumb_2') : $mediumUrl,
                'medium' => $mediumUrl,
                'large' => $media->hasGeneratedConversion('large') ? $media->getUrl('large') : $mediumUrl,
                'original' => $media->getUrl(),
            ];
        });
    }

    public function toArray(): array
    {
        $arr = parent::toArray();
        $arr['avatar'] = $this->avatar;

        return $arr;
    }

    public function hasEnoughBalance(int $amount): bool
    {
        return $this->balance >= $amount;
    }

    public function seo(): SeoBuilder
    {
        return new SeoBuilder()
            ->title('Пользователь ' . $this->name)
            ->image($this->avatar_url);
    }

    public function transactions(): HasMany
    {
        return $this->hasMany(Transaction::class);
    }

    public function payments(): HasMany
    {
        return $this->hasMany(Payment::class);
    }

    public function products(): HasMany
    {
        return $this->hasMany(Product::class);
    }

    public function newEloquentBuilder($query): UserQueryBuilder
    {
        return new UserQueryBuilder($query);
    }
}
