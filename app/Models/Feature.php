<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use App\Builders\FeatureQueryBuilder;
use App\Collections\FeatureCollection;
use App\Enum\FeatureType;
use Database\Factories\FeatureFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;

/**
 * @property int $id
 * @property string $name
 * @property FeatureType $type
 * @property array<array-key, mixed>|null $options
 * @property bool $is_required
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Kalnoy\Nestedset\Collection<int, \App\Models\Category> $categories
 * @property-read int|null $categories_count
 * @method static FeatureCollection<int, static> all($columns = ['*'])
 * @method static \Database\Factories\FeatureFactory factory($count = null, $state = [])
 * @method static FeatureQueryBuilder<static>|Feature forCategories(\Illuminate\Database\Eloquent\Collection|\Illuminate\Support\Collection|array $ids)
 * @method static FeatureQueryBuilder<static>|Feature forCategoryAndAncestors(\App\Models\Category|int $id)
 * @method static FeatureCollection<int, static> get($columns = ['*'])
 * @method static FeatureQueryBuilder<static>|Feature newModelQuery()
 * @method static FeatureQueryBuilder<static>|Feature newQuery()
 * @method static FeatureQueryBuilder<static>|Feature query()
 * @method static FeatureQueryBuilder<static>|Feature whereCreatedAt($value)
 * @method static FeatureQueryBuilder<static>|Feature whereId($value)
 * @method static FeatureQueryBuilder<static>|Feature whereIsRequired($value)
 * @method static FeatureQueryBuilder<static>|Feature whereName($value)
 * @method static FeatureQueryBuilder<static>|Feature whereOptions($value)
 * @method static FeatureQueryBuilder<static>|Feature whereType($value)
 * @method static FeatureQueryBuilder<static>|Feature whereUpdatedAt($value)
 * @mixin \Eloquent
 */
class Feature extends Model
{
    use HasFactory;

    protected $fillable = [
        'category_id',
        'name',
        'key',
        'type',
        'options',
        'is_required'
    ];

    protected $casts = [
        'type'  => FeatureType::class,
        'options' => 'array',
    ];

    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(Category::class);
    }

    public function newCollection(array $models = []): FeatureCollection
    {
        return new FeatureCollection($models);
    }

    public function newEloquentBuilder($query): FeatureQueryBuilder
    {
        return new FeatureQueryBuilder($query);
    }

    protected static function newFactory(): FeatureFactory|Factory
    {
        return FeatureFactory::new();
    }
}
