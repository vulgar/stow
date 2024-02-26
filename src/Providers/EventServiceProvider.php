<?php

namespace Vulgar\LaravelBasket\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Vulgar\LaravelBasket\Events\BasketDeletingEvent;
use Vulgar\LaravelBasket\Listeners\BasketDeletingListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BasketDeletingEvent::class => [
            BasketDeletingListener::class,
        ]
    ];

    /**
     * Register any events for your application.
     *
     * @return void
     */
    public function boot()
    {
        parent::boot();
    }
}
