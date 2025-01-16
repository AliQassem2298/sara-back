<?php

namespace App\Listeners;

use App\Events\OrderCancelled;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class RestoreProductQuantity
{
    /**
     * Create the event listener.
     */
    public function __construct()
    {
        //
    }

    /**
     * Handle the event.
     */
    public function handle(OrderCancelled $event)
    {
        $order = $event->order;

        // إعادة كمية المنتجات
        foreach ($order->products as $product) {
            $product->quantity += $product->pivot->quantity_want;
            $product->save();
        }
    }
}
