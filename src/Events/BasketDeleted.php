<?php

namespace Vulgar\LaravelBasket\Events;

use Illuminate\Queue\SerializesModels;
use Illuminate\Foundation\Events\Dispatchable;
use Vulgar\LaravelBasket\Models\Basket;

class BasketDeleted
{

    use Dispatchable, SerializesModels;

    public $basket;

    public function __construct(Basket $basket)
    {
        $this->basket = $basket;
    }

}
