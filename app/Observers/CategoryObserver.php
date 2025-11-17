<?php

namespace App\Observers;

use App\Models\Category;

class CategoryObserver
{
    /**
     * Handle the Category "created" event.
     */
    public function created(Category $category): void
    {
        activity()->log('Category created: ' . $category->name);
    }

    /**
     * Handle the Category "updated" event.
     */
    public function updated(Category $category): void
    {
        activity()->log('Category updated: ' . $category->name);
    }

    /**
     * Handle the Category "deleted" event.
     */
    public function deleted(Category $category): void
    {
        activity()->log('Category deleted: ' . $category->name);
    }

    /**
     * Handle the Category "restored" event.
     */
    public function restored(Category $category): void
    {
        activity()->log('Category restored: ' . $category->name);
    }

    /**
     * Handle the Category "force deleted" event.
     */
    public function forceDeleted(Category $category): void
    {
        activity()->log('Category force deleted: ' . $category->name);
    }
}
