<?php

namespace App\Providers;

use Illuminate\Auth\Events\Registered;
use Illuminate\Auth\Listeners\SendEmailVerificationNotification;
use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Event;
use App\Events\OrderConfirmed;
use App\Events\OrderCancelled;
use App\Listeners\UpdateProductQuantity;
use App\Listeners\RestoreProductQuantity;

class EventServiceProvider extends ServiceProvider
{
    /**
     * The event to listener mappings for the application.
     *
     * @var array<class-string, array<int, class-string>>
     */
    protected $listen = [
        OrderConfirmed::class => [
            UpdateProductQuantity::class,
        ],
        OrderCancelled::class => [
            RestoreProductQuantity::class,
        ],
    ];

    public function boot()
    {
        parent::boot();
    }


    /**
     * Determine if events and listeners should be automatically discovered.
     */
    public function shouldDiscoverEvents(): bool
    {
        return false;
    }
}
