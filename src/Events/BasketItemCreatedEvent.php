<?php

namespace Vulgar\Stow\Events;

use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;
use Vulgar\Stow\Models\BasketItem;

class BasketItemCreatedEvent
{
    use Dispatchable, SerializesModels;

    public BasketItem $basketItem;

    public function __construct(BasketItem $basketItem)
    {
        $this->basketItem = $basketItem;
    }
}
