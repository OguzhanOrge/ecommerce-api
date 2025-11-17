<?php

namespace App\Observers;

use App\Models\OrderItem;

class OrderItemObserver
{
    /**
     * Handle the OrderItem "created" event.
     */
    public function created(OrderItem $orderItem): void
    {
        activity()->log('OrderItem created with ID: ' . $orderItem->id);
    }

    /**
     * Handle the OrderItem "updated" event.
     */
    public function updated(OrderItem $orderItem): void
    {
        activity()->log('OrderItem updated with ID: ' . $orderItem->id);
    }

    /**
     * Handle the OrderItem "deleted" event.
     */
    public function deleted(OrderItem $orderItem): void
    {
        activity()->log('OrderItem deleted with ID: ' . $orderItem->id);
    }

    /**
     * Handle the OrderItem "restored" event.
     */
    public function restored(OrderItem $orderItem): void
    {
        activity()->log('OrderItem restored with ID: ' . $orderItem->id);
    }

    /**
     * Handle the OrderItem "force deleted" event.
     */
    public function forceDeleted(OrderItem $orderItem): void
    {
        activity()->log('OrderItem force deleted with ID: ' . $orderItem->id);
    }
}
