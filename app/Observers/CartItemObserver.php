<?php

namespace App\Observers;

use App\Models\CartItem;

class CartItemObserver
{
    /**
     * Handle the CartItem "created" event.
     */
    public function created(CartItem $cartItem): void
    {
        activity()->log('CartItem created: ID ' . $cartItem->id);
    }

    /**
     * Handle the CartItem "updated" event.
     */
    public function updated(CartItem $cartItem): void
    {
        activity()->log('CartItem updated: ID ' . $cartItem->id);
    }

    /**
     * Handle the CartItem "deleted" event.
     */
    public function deleted(CartItem $cartItem): void
    {
        activity()->log('CartItem deleted: ID ' . $cartItem->id);
    }

    /**
     * Handle the CartItem "restored" event.
     */
    public function restored(CartItem $cartItem): void
    {
        activity()->log('CartItem restored: ID ' . $cartItem->id);
    }

    /**
     * Handle the CartItem "force deleted" event.
     */
    public function forceDeleted(CartItem $cartItem): void
    {
        activity()->log('CartItem force deleted: ID ' . $cartItem->id);
    }
}
