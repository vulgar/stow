<?php

namespace Vulgar\LaravelBasket\Traits;

trait Basketable{
    public function basket(){
        return $this->morph();
    }
}
