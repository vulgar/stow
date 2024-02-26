<?php

namespace Vulgar\LaravelBasket\Traits;

trait Basketable{
    public function basketItem(){
        return $this->morphTo();
    }
}
