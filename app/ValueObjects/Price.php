<?php

namespace App\ValueObjects;

use Illuminate\Contracts\Support\Arrayable;
use InvalidArgumentException;
use JsonSerializable;
use Random\RandomException;

class Price implements Arrayable, JsonSerializable
{
    private int $base;
    private int $current;

    public function __construct(int $base, ?int $current = null)
    {
        $this->base = $base;
        $this->current = $current ?? $base;

        if ($this->current > $this->base) {
            throw new InvalidArgumentException('Current price cannot be higher than base');
        }
    }

    /**
     * Статическое создание объекта
     * @param int $base
     * @param int|null $current
     * @return static
     */
    public static function make(int $base, ?int $current = null): static
    {
        return new static($base, $current);
    }

    /**
     * Создает объект из базовой цены и цены по скидке
     * @param int $base
     * @param int|null $discount
     * @return Price
     */
    public static function fromBaseAndDiscount(int $base, int|null $discount = null): static
    {
        if (isset($discount) && $discount >= $base) {
            throw new InvalidArgumentException('Discount not must be greater than base price');
        }

        return new static($base, $discount ?? $base);
    }

    /**
     * Создаем случайную цену
     * @param int $minBase
     * @param int $maxBase
     * @param int $maxDiscountPercent
     * @return static
     * @throws RandomException
     */
    public static function random(int $minBase = 200, int $maxBase = 10000, int $maxDiscountPercent = 50): static
    {
        $original = random_int($minBase, $maxBase);

        // Применяем скидки с 30% вероятностью
        if (random_int(1, 100) <= 30) {
            $discountPercent = random_int(5, $maxDiscountPercent);
            $discount = $original - (int) round($original * ($discountPercent / 100));
            return new static($original, $discount);
        }

        return new static($original);
    }

    /**
     * Возвращает текущую цену
     * @return int
     */
    public function getCurrentPrice(): int
    {
        return $this->current;
    }

    /**
     * Возвращает оригинальную цену (без скидки)
     * @return int
     */
    public function getBasePrice(): int
    {
        return $this->base;
    }

    /**
     * Возвращает цену по скидке
     * @return int|null
     */
    public function getDiscountPrice(): ?int
    {
        return $this->hasDiscount() ? $this->current : null;
    }

    /**
     * Имеется ли скидка
     * @return bool
     */
    public function hasDiscount(): bool
    {
        return $this->current < $this->base;
    }

    /**
     * Возвращает размер скидки
     * @return int
     */
    public function getDiscountAmount(): int
    {
        return $this->base - $this->current;
    }

    /**
     * Возвращает размер скидки в процентах
     * @return int
     */
    public function getDiscountPercent(): int
    {
        return $this->hasDiscount()
            ? (int) round(100 - ($this->current / $this->base * 100))
            : 0;
    }

    /**
     * Выводит цену за $quantity раз
     * @param int $quantity
     * @return self
     */
    public function multiply(int $quantity): self
    {
        return new static(
            base: $this->base * $quantity,
            current: $this->current * $quantity
        );
    }

    /**
     * Суммирует с другой ценой
     * @param Price $price
     * @return self
     */
    public function sumWith(self $price): self
    {
        return new static(
            base: $this->base + $price->base,
            current: $this->current + $price->current
        );
    }

    public function toArray(): array
    {
        return [
            'current' => $this->getCurrentPrice(),
            'base' => $this->getBasePrice(),
            'discount' => $this->getDiscountPrice(),
            'discount_amount' => $this->getDiscountAmount(),
            'discount_percent' => $this->getDiscountPercent(),
            'has_discount' => $this->hasDiscount(),
        ];
    }

    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
}