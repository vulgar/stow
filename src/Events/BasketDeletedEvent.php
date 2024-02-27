<?php

namespace Vulgar\Stow\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Vulgar\Stow\Models\Basket;

class BasketDeletedEvent
{
    use Dispatchable, SerializesModels;

    public Basket $basket;

    public function __construct(Basket $basket)
    {
        $this->basket = $basket;
    }
}
