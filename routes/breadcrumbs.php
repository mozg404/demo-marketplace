<?php

use App\Models\Category;
use App\Models\Order;
use App\Models\Product;
use App\Models\User;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

// -------------------------------
// Мои товары
// -------------------------------

Breadcrumbs::for('my.products', function (BreadcrumbTrail $trail) {
    $trail->push('Мои товары', route('my.products'));
});
Breadcrumbs::for('my.products.create', function (BreadcrumbTrail $trail) {
    $trail->parent('my.products');
    $trail->push('Новый товар');
});
Breadcrumbs::for('my.products.edit', function (BreadcrumbTrail $trail, Product $product) {
    $trail->parent('my.products');
    $trail->push('Редактирование товара №' . $product->id, route('my.products.edit', $product));
});
Breadcrumbs::for('my.products.stock', function (BreadcrumbTrail $trail, Product $product) {
    $trail->parent('my.products.edit', $product);
    $trail->push('Позиции на складе', route('my.products.stock', $product->id));
});


// -------------------------------
// Мои заказы
// -------------------------------

Breadcrumbs::for('my.orders', function (BreadcrumbTrail $trail) {
    $trail->push('Мои заказы', route('my.orders'));
});
Breadcrumbs::for('my.orders.show', function (BreadcrumbTrail $trail, Order $order) {
    $trail->parent('my.orders');
    $trail->push('Заказ #'.$order->id, route('my.orders.show', $order->id));
});

// -------------------------------
// Каталог
// -------------------------------

Breadcrumbs::for('catalog', function (BreadcrumbTrail $trail) {
    $trail->push('Товары', route('catalog'));
});
Breadcrumbs::for('catalog.category', function (BreadcrumbTrail $trail, Category $category) {
    if ($category->parent) {
        $trail->parent('catalog.category', $category->parent);
    } else {
        $trail->parent('catalog');
    }

    $trail->push($category->name, route('catalog.category', $category->full_path));
});
Breadcrumbs::for('catalog.product', function (BreadcrumbTrail $trail, Product $product) {
    if (isset($product->category)) {
        $trail->parent('catalog.category', $product->category);
    } else {
        $trail->parent('catalog');
    }

    $trail->push($product->name, route('catalog.product', $product->id));
});

// -------------------------------
// Продавцы
// -------------------------------

Breadcrumbs::for('users', function (BreadcrumbTrail $trail) {
    $trail->push('Продавцы', route('users'));
});
Breadcrumbs::for('users.show', function (BreadcrumbTrail $trail, User $user) {
    $trail->parent('users');
    $trail->push($user->name, route('users.show', $user->id));
});