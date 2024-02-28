<?php

namespace Vulgar\Stow\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use Vulgar\Stow\Events\BasketDeletingEvent;
use Vulgar\Stow\Listeners\BasketDeletingListener;

class EventServiceProvider extends ServiceProvider
{
    protected $listen = [
        BasketDeletingEvent::class => [
            BasketDeletingListener::class,
        ],
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
