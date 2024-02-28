<?php

namespace Vulgar\Stow\Listeners;

use Vulgar\Stow\Events\BasketDeletingEvent;

class BasketDeletingListener
{
    public function handle(BasketDeletingEvent $event): void
    {
        $event->basket->basketItems->each(function ($item) {
            $item->delete();
        });
    }
}
