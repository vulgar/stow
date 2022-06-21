<?php

namespace Vulgar\LaravelBasket\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Vulgar\LaravelBasket\Models\BasketItem;

class BasketItemUpdated
{

    use Dispatchable, SerializesModels;

    public $postBasketItem;
    public $preBasketItem;

    public function __construct(BasketItem $postBasketItem, BasketItem $preBasketItem = null)
    {
        $this->postBasketItem = $postBasketItem;
        $this->preBasketItem = $preBasketItem;
    }

}
