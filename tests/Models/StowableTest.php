<?php

namespace Vulgar\LaravelBasket\Tests\Models;

use Illuminate\Database\Eloquent\Model;
use Vulgar\LaravelBasket\Interfaces\Stowable;
use Vulgar\LaravelBasket\Traits\Basketable;

class StowableTest extends Model implements Stowable {
    use Basketable;
    public function getKey(){ return 1; }
}
