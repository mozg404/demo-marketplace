<?php

namespace App\Services;

use Diglactic\Breadcrumbs\Breadcrumbs;
use Illuminate\Http\Request;

class BreadcrumbsManager
{
    public function generate(string $name, ...$params): array
    {
        if (!Breadcrumbs::exists($name)) {
            return [];
        }

        return Breadcrumbs::generate($name, ...$params)->toArray();
    }

    public function generateFromRequest(Request $request): array
    {
        $route = $request->route();

        if (!$route || !Breadcrumbs::exists($route->getName())) {
            return [];
        }

        // Автоматически получаем модели из параметров маршрута
        $parameters = $route->parameters();

        return $this->generate($route->getName(), ...array_values($parameters));
    }
}