<?php

namespace App\Listeners;
use App\Models\Product;

use App\Events\OrderConfirmed;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Queue\InteractsWithQueue;

class UpdateProductQuantity
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
    public function handle(OrderConfirmed $event)
    {
        $order = $event->order;

        // تحديث كمية المنتجات
        foreach ($order->products as $product) {
            $product->quantity -= $product->pivot->quantity_want;
            $product->save();
        }
    }
}
