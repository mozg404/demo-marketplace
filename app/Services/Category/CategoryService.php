<?php

namespace App\Services\Category;

use App\Exceptions\Category\CategoryFullPathConflictException;
use App\Models\Category;
use Throwable;

class CategoryService
{
    /**
     * @throws Throwable
     */
    public function ensureUniqueFullPath(Category $category): void
    {
        $check = Category::query()
            ->where('full_path', $category->full_path)
            ->where('id', '!=', $category->id)
            ->exists();

        throw_if($check, new CategoryFullPathConflictException("Найден дубликат пути: {$category->full_path}"));
    }
}