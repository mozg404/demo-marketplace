<?php

namespace App\Services\Cart;

use InvalidArgumentException;
use App\Contracts\Cart;
use Illuminate\Contracts\Session\Session;

class SessionCart implements Cart
{
    public const SESSION_KEY = 'cart';

    private Session $session;

    public function __construct(Session $session)
    {
        $this->session = $session;
    }

    public function add(int $id, int $quantity = 1): void
    {
        $this->ensureCorrectId($id);
        $this->ensureCorrectQuantity($quantity);

        $cart = $this->getItems();
        $cart[$id] = ($cart[$id] ?? 0) + $quantity;
        $this->session->put(self::SESSION_KEY, $cart);
    }

    public function has(int $id): bool
    {
        $this->ensureCorrectId($id);

        return $this->session->has(self::SESSION_KEY . '.' . $id);
    }

    public function remove(int $id, int $quantity = 1): void
    {
        $this->ensureCorrectId($id);
        $this->ensureCorrectQuantity($quantity);
        $cart = $this->getItems();

        if (isset($cart[$id])) {
            if ($cart[$id] > 1 && $cart[$id] > $quantity) {
                $cart[$id] -= $quantity;
                $this->session->put(self::SESSION_KEY, $cart);
            } else {
                $this->removeItem($id);
            }
        }
    }

    public function isEmpty(): bool
    {
        return empty($this->getItems());
    }

    public function clear(): void
    {
        $this->session->forget(self::SESSION_KEY);
    }

    public function getQuantityFor(int $id): int
    {
        // (!) Проверка на валидность $productId уже есть в has
        if (!$this->has($id)) {
            return 0;
        }

        return $this->session->get(self::SESSION_KEY . '.' . $id);
    }

    public function removeItem(int $id): void
    {
        $this->ensureCorrectId($id);
        $this->session->forget(self::SESSION_KEY.'.'.$id);
    }

    public function getItems(): array
    {
        return $this->session->get(self::SESSION_KEY, []);
    }

    public function getIds(): array
    {
        return array_keys($this->session->get(self::SESSION_KEY, []));
    }

    private function ensureCorrectQuantity(int $quantity): void
    {
        if ($quantity < 0) {
            throw new InvalidArgumentException('Quantity cannot be negative');
        }

        if ($quantity === 0) {
            throw new InvalidArgumentException('Quantity cannot be zero');
        }
    }

    private function ensureCorrectId(int $productId): void
    {
        if ($productId < 0) {
            throw new InvalidArgumentException('ID cannot be negative');
        }

        if ($productId === 0) {
            throw new InvalidArgumentException('ID cannot be zero');
        }
    }
}