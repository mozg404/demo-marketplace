<?php

namespace App\Contracts;

interface Cart
{
    public function add(int $id, int $quantity = 1): void;

    public function has(int $id): bool;

    public function remove(int $id, int $quantity = 1): void;

    public function getQuantityFor(int $id): int;

    public function removeItem(int $id): void;

    public function getItems(): array;

    public function getIds(): array;

    public function isEmpty(): bool;

    public function clear(): void;
}