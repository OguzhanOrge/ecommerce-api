<?php

namespace App\Observers;

use App\Models\Cart;

class CartObserver
{
    /**
     * Handle the Cart "created" event.
     */
    public function created(Cart $cart): void
    {
        activity()->log('Cart created with ID: ' . $cart->id);
    }

    /**
     * Handle the Cart "updated" event.
     */
    public function updated(Cart $cart): void
    {
        activity()->log('Cart updated with ID: ' . $cart->id);
    }

    /**
     * Handle the Cart "deleted" event.
     */
    public function deleted(Cart $cart): void
    {
        activity()->log('Cart deleted with ID: ' . $cart->id);
    }

    /**
     * Handle the Cart "restored" event.
     */
    public function restored(Cart $cart): void
    {
        activity()->log('Cart restored with ID: ' . $cart->id);
    }

    /**
     * Handle the Cart "force deleted" event.
     */
    public function forceDeleted(Cart $cart): void
    {
        activity()->log('Cart force deleted with ID: ' . $cart->id);
    }
}
