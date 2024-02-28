<?php

namespace Vulgar\Stow\Traits;

use Illuminate\Database\Eloquent\Relations\MorphTo;

trait StowMethods
{
    /**
     * @return MorphTo
     */
    public function basketItems(): MorphTo
    {
        return $this->morphTo();
    }
}
