<?php

namespace Vulgar\Stow\Listeners;

use Vulgar\Stow\Events\BasketDeletingEvent;

class BasketDeletingListener
{
    public function handle(BasketDeletingEvent $event): void
    {
        $event->basket->items->each(function ($item) {
            $item->delete();
        });
    }
}
