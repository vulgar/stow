<?php

namespace Vulgar\LaravelBasket\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Vulgar\LaravelBasket\Traits\Stowable;
use Session;

class BasketItem extends Model
{
    use SoftDeletes;

	protected $fillable = ['stowable_type', 'stowable_id', 'options'];

    protected $casts = [
        'options' => 'array'
    ];

    public function basket()
    {
        return $this->belongsTo(Basket::class);
    }

    public function stowable(){
        return $this->morphTo('stowable');
    }
}
