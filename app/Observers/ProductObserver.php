<?php

namespace App\Observers;

use App\Models\Product;

class ProductObserver
{
    /**
     * Handle the Product "created" event.
     */
    public function created(Product $product): void
    {
        activity()->log('Product created: ' . $product->name);
    }

    /**
     * Handle the Product "updated" event.
     */
    public function updated(Product $product): void
    {
        activity()->log('Product updated: ' . $product->name);
    }

    /**
     * Handle the Product "deleted" event.
     */
    public function deleted(Product $product): void
    {
        activity()->log('Product deleted: ' . $product->name);
    }

    /**
     * Handle the Product "restored" event.
     */
    public function restored(Product $product): void
    {
        activity()->log('Product restored: ' . $product->name);
    }

    /**
     * Handle the Product "force deleted" event.
     */
    public function forceDeleted(Product $product): void
    {
        activity()->log('Product force deleted: ' . $product->name);
    }
}
