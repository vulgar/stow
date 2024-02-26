<?php

namespace Vulgar\LaravelBasket\Listeners;
use Vulgar\LaravelBasket\Events\BasketDeletingEvent;

class BasketDeletingListener{
    public function handle(BasketDeletingEvent $event){
        $event->basket->items->each(function($item, $key){
            $item->delete();
        });
    }
}
