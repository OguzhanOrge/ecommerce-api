<?php

namespace App\Observers;

use App\Models\Order;

class OrderObserver
{
    /**
     * Handle the Order "created" event.
     */
    public function created(Order $order): void
    {
        activity()->log('Order created with ID: ' . $order->id);
    }

    /**
     * Handle the Order "updated" event.
     */
    public function updated(Order $order): void
    {
        activity()->log('Order updated with ID: ' . $order->id);
    }

    /**
     * Handle the Order "deleted" event.
     */
    public function deleted(Order $order): void
    {
        activity()->log('Order deleted with ID: ' . $order->id);
    }

    /**
     * Handle the Order "restored" event.
     */
    public function restored(Order $order): void
    {
        activity()->log('Order restored with ID: ' . $order->id);
    }

    /**
     * Handle the Order "force deleted" event.
     */
    public function forceDeleted(Order $order): void
    {
        activity()->log('Order force deleted with ID: ' . $order->id);
    }
}
