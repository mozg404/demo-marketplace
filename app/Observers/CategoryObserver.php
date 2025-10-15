<?php

namespace App\Observers;

use App\Models\Category;
use App\Services\Category\CategoryService;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Str;
use Throwable;

class CategoryObserver
{
    public function __construct(
        private CategoryService $categoryService,
    ) {
    }

    /**
     * @throws Throwable
     */
    public function creating(Category $category): void
    {
        if (empty($category->slug)) {
            $category->slug = Str::slug($category->name);
        }

        if (empty($category->title)) {
            $category->title = $category->name;
        }

        $this->generateFullPath($category);
        $this->categoryService->ensureUniqueFullPath($category);
    }

    public function created(Category $category): void
    {
        $category->saveQuietly();
    }

    /**
     * @throws Throwable
     */
    public function updating(Category $category): void
    {
        if ($category->isDirty('name') && empty($category->slug)) {
            $category->slug = Str::slug($category->name);
        }

        if ($category->isDirty('title') && empty($category->title)) {
            $category->title = $category->name;
        }

        if ($category->isDirty(['slug', 'parent_id'])) {
            $this->generateFullPath($category);
            $this->categoryService->ensureUniqueFullPath($category);
        }
    }

    /**
     * @throws Throwable
     */
    public function updated(Category $category): void
    {
        if ($category->isDirty('title')) {
            $category->saveQuietly();
        }

        // Если изменился slug или переместилась в другого родителя
        if ($category->isDirty(['slug', 'parent_id'])) {
            DB::transaction(function () use ($category) {
                $oldFullPath = $category->getOriginal('full_path');

                // Если full_path изменился - обновим всех потомков
                if ($category->isDirty('full_path')) {
                    $this->updateDescendantsPaths($category, $oldFullPath);
                }

                $category->saveQuietly();
            });
        }
    }

    protected function generateFullPath(Category $category): void
    {
        if ($category->parent_id) {
            $category->full_path = $category->parent->full_path . '/' . $category->slug;
        } else {
            $category->full_path = $category->slug;
        }
    }

    /**
     * @throws Throwable
     */
    protected function updateDescendantsPaths(Category $category, string $oldFullPath): void
    {
        $newFullPath = $category->full_path;

        $category->descendants()
            ->get()
            ->each(function (Category $descendant) use ($oldFullPath, $newFullPath) {
                $descendant->full_path = str_replace(
                    $oldFullPath,
                    $newFullPath,
                    $descendant->full_path
                );

                $this->categoryService->ensureUniqueFullPath($descendant);

                $descendant->saveQuietly();
            });
    }
}
