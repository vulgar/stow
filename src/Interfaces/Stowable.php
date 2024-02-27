<?php

namespace Vulgar\Stow\Interfaces;

use Illuminate\Database\Eloquent\Relations\MorphTo;

interface Stowable
{
    public function basketItems(): MorphTo;

    public function getKey(): mixed;
}
