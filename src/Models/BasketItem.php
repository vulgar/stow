<?php

namespace Vulgar\LaravelBasket\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Vulgar\LaravelBasket\Events\BasketItemCreatedEvent;
use Vulgar\LaravelBasket\Events\BasketItemDeletedEvent;
use Vulgar\LaravelBasket\Events\BasketItemUpdatedEvent;

class BasketItem extends Model
{
    use SoftDeletes;

    protected $fillable = ['stowable_type', 'stowable_id', 'options'];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => BasketItemCreatedEvent::class,
        'updated' => BasketItemUpdatedEvent::class,
        'deleted' => BasketItemDeletedEvent::class,
    ];

    protected $casts = [
        'options' => 'array'
    ];

    public function basket()
    {
        return $this->belongsTo(Basket::class);
    }

    public function stowable()
    {
        return $this->morphTo('stowable');
    }
}
