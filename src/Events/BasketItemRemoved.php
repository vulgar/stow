<?php

namespace Vulgar\LaravelBasket\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Vulgar\LaravelBasket\Models\BasketItem;

class BasketItemRemoved
{

    use Dispatchable, SerializesModels;

    public $basketItem;

    public function __construct(BasketItem $basketItem)
    {
        $this->basketItem = $basketItem;
    }

}