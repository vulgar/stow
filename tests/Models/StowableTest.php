<?php

namespace Vulgar\Stow\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Vulgar\Stow\Interfaces\Stowable;
use Vulgar\Stow\Traits\StowMethods;

class StowableTest extends Model implements Stowable
{
    use StowMethods;

    /**
     * Return a basic key for the model.
     * @return mixed
     */
    public function getKey(): mixed
    {
        return 1;
    }
}
