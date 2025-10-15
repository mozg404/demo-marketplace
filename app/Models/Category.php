<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use App\Builders\CategoryQueryBuilder;
use App\Contracts\Seoble;
use App\Observers\CategoryObserver;
use App\Support\SeoBuilder;
use Database\Factories\CategoryFactory;
use Illuminate\Database\Eloquent\Attributes\ObservedBy;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Kalnoy\Nestedset\NodeTrait;

/**
 * @property int $id
 * @property string $name
 * @property string $slug
 * @property string $full_path
 * @property string $title
 * @property int $_lft
 * @property int $_rgt
 * @property int|null $parent_id
 * @property Carbon|null $created_at
 * @property Carbon|null $updated_at
 * @property-read \Kalnoy\Nestedset\Collection<int, Category> $children
 * @property-read int|null $children_count
 * @property-read \App\Collections\FeatureCollection<int, \App\Models\Feature> $features
 * @property-read int|null $features_count
 * @property-read Category|null $parent
 * @method static \Kalnoy\Nestedset\Collection<int, static> all($columns = ['*'])
 * @method static CategoryQueryBuilder<static>|Category ancestorsAndSelf($id, array $columns = [])
 * @method static CategoryQueryBuilder<static>|Category ancestorsOf($id, array $columns = [])
 * @method static CategoryQueryBuilder<static>|Category applyNestedSetScope(?string $table = null)
 * @method static CategoryQueryBuilder<static>|Category countErrors()
 * @method static CategoryQueryBuilder<static>|Category d()
 * @method static CategoryQueryBuilder<static>|Category defaultOrder(string $dir = 'asc')
 * @method static CategoryQueryBuilder<static>|Category descendantsAndSelf($id, array $columns = [])
 * @method static CategoryQueryBuilder<static>|Category descendantsOf($id, array $columns = [], $andSelf = false)
 * @method static \Database\Factories\CategoryFactory factory($count = null, $state = [])
 * @method static CategoryQueryBuilder<static>|Category fixSubtree($root)
 * @method static CategoryQueryBuilder<static>|Category fixTree($root = null)
 * @method static \Kalnoy\Nestedset\Collection<int, static> get($columns = ['*'])
 * @method static CategoryQueryBuilder<static>|Category getAncestorAndSelfIds(\App\Models\Category|int $id)
 * @method static CategoryQueryBuilder<static>|Category getDescendantsAndSelfIds(\App\Models\Category|int $id)
 * @method static CategoryQueryBuilder<static>|Category getIdByFullPath(string $fullPath)
 * @method static CategoryQueryBuilder<static>|Category getNodeData($id, $required = false)
 * @method static CategoryQueryBuilder<static>|Category getPlainNodeData($id, $required = false)
 * @method static CategoryQueryBuilder<static>|Category getTotalErrors()
 * @method static CategoryQueryBuilder<static>|Category hasChildren()
 * @method static CategoryQueryBuilder<static>|Category hasParent()
 * @method static CategoryQueryBuilder<static>|Category isBroken()
 * @method static CategoryQueryBuilder<static>|Category leaves(array $columns = [])
 * @method static CategoryQueryBuilder<static>|Category makeGap(int $cut, int $height)
 * @method static CategoryQueryBuilder<static>|Category moveNode($key, $position)
 * @method static CategoryQueryBuilder<static>|Category newModelQuery()
 * @method static CategoryQueryBuilder<static>|Category newQuery()
 * @method static CategoryQueryBuilder<static>|Category orWhereAncestorOf(bool $id, bool $andSelf = false)
 * @method static CategoryQueryBuilder<static>|Category orWhereDescendantOf($id)
 * @method static CategoryQueryBuilder<static>|Category orWhereNodeBetween($values)
 * @method static CategoryQueryBuilder<static>|Category orWhereNotDescendantOf($id)
 * @method static CategoryQueryBuilder<static>|Category query()
 * @method static CategoryQueryBuilder<static>|Category rebuildSubtree($root, array $data, $delete = false)
 * @method static CategoryQueryBuilder<static>|Category rebuildTree(array $data, $delete = false, $root = null)
 * @method static CategoryQueryBuilder<static>|Category reversed()
 * @method static CategoryQueryBuilder<static>|Category root(array $columns = [])
 * @method static CategoryQueryBuilder<static>|Category whereAncestorOf($id, $andSelf = false, $boolean = 'and')
 * @method static CategoryQueryBuilder<static>|Category whereAncestorOrSelf($id)
 * @method static CategoryQueryBuilder<static>|Category whereCreatedAt($value)
 * @method static CategoryQueryBuilder<static>|Category whereDescendantOf($id, $boolean = 'and', $not = false, $andSelf = false)
 * @method static CategoryQueryBuilder<static>|Category whereDescendantOrSelf(string $id, string $boolean = 'and', string $not = false)
 * @method static CategoryQueryBuilder<static>|Category whereFullPath($value)
 * @method static CategoryQueryBuilder<static>|Category whereId($value)
 * @method static CategoryQueryBuilder<static>|Category whereIsAfter($id, $boolean = 'and')
 * @method static CategoryQueryBuilder<static>|Category whereIsBefore($id, $boolean = 'and')
 * @method static CategoryQueryBuilder<static>|Category whereIsLeaf()
 * @method static CategoryQueryBuilder<static>|Category whereIsRoot()
 * @method static CategoryQueryBuilder<static>|Category whereLft($value)
 * @method static CategoryQueryBuilder<static>|Category whereName($value)
 * @method static CategoryQueryBuilder<static>|Category whereNodeBetween($values, $boolean = 'and', $not = false, $query = null)
 * @method static CategoryQueryBuilder<static>|Category whereNotDescendantOf($id)
 * @method static CategoryQueryBuilder<static>|Category whereParentId($value)
 * @method static CategoryQueryBuilder<static>|Category whereRgt($value)
 * @method static CategoryQueryBuilder<static>|Category whereSlug($value)
 * @method static CategoryQueryBuilder<static>|Category whereTitle($value)
 * @method static CategoryQueryBuilder<static>|Category whereUpdatedAt($value)
 * @method static CategoryQueryBuilder<static>|Category withDepth(string $as = 'depth')
 * @method static CategoryQueryBuilder<static>|Category withFeatures()
 * @method static CategoryQueryBuilder<static>|Category withoutRoot()
 * @mixin \Eloquent
 */
#[ObservedBy([CategoryObserver::class])]
class Category extends Model implements Seoble
{
    use HasFactory, NodeTrait;

    protected $fillable = ['name', 'slug', 'title', 'parent_id'];

    protected $casts = [
        '_lft' => 'integer',
        '_rgt' => 'integer',
    ];

    public function features(): BelongsToMany
    {
        return $this->belongsToMany(Feature::class);
    }

    public function newEloquentBuilder($query): CategoryQueryBuilder
    {
        return new CategoryQueryBuilder($query);
    }

    protected static function newFactory(): CategoryFactory|Factory
    {
        return CategoryFactory::new();
    }

    public function seo(): SeoBuilder
    {
        return new SeoBuilder()
            ->title($this->title)
            ->description($this->title);
    }

}
