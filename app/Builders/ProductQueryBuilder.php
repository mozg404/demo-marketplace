<?php

declare(strict_types=1);

namespace App\Builders;

use App\Collections\ProductCollection;
use App\Enum\ProductStatus;
use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;

/**
 * @method ProductCollection get(array|string $column = ['*'])
 */
class ProductQueryBuilder extends Builder
{
    public function filterFromArray(array $data): self
    {
        if (isset($data['price_min'])) {
            $this->wherePriceMin((int) $data['price_min']);
        }

        if (isset($data['price_max'])) {
            $this->wherePriceMax((int) $data['price_max']);
        }

        if (isset($data['is_discounted']) && in_array($data['is_discounted'], ['true', '1', 1, true], true)) {
            $this->isDiscounted();
        }

        if (isset($data['features'])) {
            $this->whereFeatureValues($data['features']);
        }

        if (isset($data['status'])) {
            if ($data['status'] === 'available') {
                $this->isAvailable();
            }

            if ($data['status'] === 'sold_out') {
                $this->isSoldOut();
            }

            if ($data['status'] === 'draft') {
                $this->isDraft();
            }

            if ($data['status'] === 'paused') {
                $this->isPaused();
            }
        }

        if (isset($data['sort'])) {
            if ($data['sort'] === 'rating') {
                $this->orderByRating();
            }

            if ($data['sort'] === 'latest') {
                $this->latest();
            }

            if ($data['sort'] === 'oldest') {
                $this->oldest();
            }

            if ($data['sort'] === 'price_desc') {
                $this->orderByDesc('current_price');
            }

            if ($data['sort'] === 'price_asc') {
                $this->orderBy('current_price');
            }

            if ($data['sort'] === 'id_desc') {
                $this->orderByDesc('id');
            }

            if ($data['sort'] === 'id_asc') {
                $this->orderBy('id');
            }
        }

        if (!empty($data['search'])) {
            if (!isset($data['sort'])) {
                $this->searchAndSort($data['search']);
            } else {
                $this->search($data['search']);
            }
        }

        return $this;
    }

    public function isAvailable(): self
    {
        return $this->hasAvailableStock()->isActive();
    }

    public function isSoldOut(): self
    {
        return $this->hasNoAvailableStock()->isActive();
    }

    public function isActive(): self
    {
        return $this->where('status', ProductStatus::ACTIVE->value);
    }

    public function isDraft(): self
    {
        return $this->where('status', ProductStatus::DRAFT->value);
    }

    public function isPaused(): self
    {
        return $this->where('status', ProductStatus::PAUSED->value);
    }

    /**
     * Выполняет поиск по характеристикам
     * Формат: [feature_id => [value1, value2, ...]]
     * @param array $filters
     * @return $this
     */
    public function whereFeatureValues(array $filters): self
    {
        foreach ($filters as $featureId => $values) {
            if (empty($values)) {
                continue;
            }

            if (!is_array($values)) {
                $values = [$values];
            }

            $this->whereHas('features', function ($query) use ($featureId, $values) {
                $query->where('features.id', $featureId)->whereIn('product_feature_values.value', (array)$values);
            });
        }

        return $this;
    }

    /**
     * Фильтр по минимальной цене (current_price)
     */
    public function wherePriceMin(float $minPrice): self
    {
        return $this->where('current_price', '>=', $minPrice);
    }

    /**
     * Фильтр по максимальной цене (current_price)
     */
    public function wherePriceMax(float $maxPrice): self
    {
        return $this->where('current_price', '<=', $maxPrice);
    }

    /**
     * Только товары со скидкой (current_price < base_price)
     */
    public function isDiscounted(): self
    {
        return $this->whereColumn('current_price', '<', 'base_price');
    }

    /**
     * Имеет доступные для продажи позиции
     */
    public function hasAvailableStock(): self
    {
        return $this->whereHas('stockItems', fn (StockItemQueryBuilder $builder) => $builder->isAvailable());
    }

    /**
     * НЕ имеет доступных позиций для продажи
     */
    public function hasNoAvailableStock(): self
    {
        return $this->whereDoesntHave('stockItems', fn (StockItemQueryBuilder $builder) => $builder->isAvailable());
    }

    /**
     * Имеет позиции на складе
     */
    public function hasStockItems(): self
    {
        return $this->whereHas('stockItems');
    }

    public function whereSeller(User|int $user): self
    {
        if (is_object($user)) {
            $user = $user->id;
        }

        return $this->where('user_id', $user);
    }

    public function whereNotBelongsToUser(int|User $user): self
    {
        if (is_object($user)) {
            $user = $user->id;
        }

        return $this->where('user_id', '!=', $user);
    }

    public function whereCategoryAndDescendants(Category|int $id): self
    {
        if ($id instanceof Category) {
            $id = $id->id;
        }

        return $this->whereCategories(
            Category::query()->getDescendantsAndSelfIds($id)
        );
    }

    public function whereCategories(array $ids): self
    {
        return $this->whereIn('category_id', $ids);
    }

    public function whereCategory(Category|int $category): self
    {
        if (is_object($category)) {
            $category = $category->id;
        }

        return $this->where('category_id', $category);
    }

    /**
     * Выполняет поиск через Meilisearch
     */
    public function search(string $string): self
    {
        $ids = collect(Product::search($string)->raw()['hits'])
            ->pluck('id')
            ->all();

        return $this->whereIn('id', $ids);
    }

    /**
     * Выполняет поиск через Meilisearch + сортирует по релевантности
     */
    public function searchAndSort(string $string): self
    {
        $ids = collect(Product::search($string)->raw()['hits'])
            ->pluck('id')
            ->all();

        $builder = $this->whereIn('id', $ids);

        if (!empty($ids)) {
            $orderString = implode(',', $ids);
            $builder = $builder->orderByRaw("array_position(ARRAY[{$orderString}], id)");
        }

        return $builder;
    }

    /**
     * Возвращает перечень товаров массиву ID
     * @param array $ids
     * @return $this
     */
    public function whereIds(array $ids): self
    {
        return $this->whereIn('id', $ids);
    }

    public function withFeatures(): self
    {
        return $this->with('features');
    }

    /**
     * Включает количество позиций
     * @return $this
     */
    public function withStockItemsCount(): self
    {
        return $this->withCount('stockItems');
    }

    /**
     * Включает количество доступных позиций
     * @return $this
     */
    public function withAvailableCount(): self
    {
        return $this->withCount(['stockItems as available_count' => static function (StockItemQueryBuilder $builder) {
            return $builder->isAvailable();
        }]);
    }

    public function withMedia(): self
    {
        return $this->with('media');
    }

    // -----------------------------------------------
    // Order
    // -----------------------------------------------

    public function orderByRating(): self
    {
        return $this->orderByDesc('rating');
    }

    // -----------------------------------------------
    // Select
    // -----------------------------------------------

    public function selectForListing(): self
    {
        return $this->select(['id', 'user_id', 'category_id', 'name', 'status', 'current_price', 'base_price', 'rating', 'created_at']);
    }

    // -----------------------------------------------
    // Presets
    // -----------------------------------------------

    /**
     * Возвращает только активные опубликованные товары с доступными для продажи позициями
     */
    public function forListingPreset(): self
    {
        return $this
            ->selectForListing()
            ->isActive()
            ->withMedia()
            ->hasAvailableStock()
            ->withAvailableCount();
    }
}
