<?php

namespace Vulgar\LaravelBasket\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Vulgar\LaravelBasket\Interfaces\Stowable;
use Illuminate\Support\Str;
use Vulgar\LaravelBasket\Events\BasketCreatedEvent;
use Vulgar\LaravelBasket\Events\BasketDeletedEvent;
use Vulgar\LaravelBasket\Events\BasketDeletingEvent;
use Vulgar\LaravelBasket\Exceptions\UnstowableObjectException;

class Basket extends Model
{
    use SoftDeletes;

    protected $fillable = ['instance'];

    protected $casts = ['options'=>'array'];

    /**
     * The event map for the model.
     *
     * @var array
     */
    protected $dispatchesEvents = [
        'created' => BasketCreatedEvent::class,
        'deleted' => BasketDeletedEvent::class,
        'deleting' => BasketDeletingEvent::class,
    ];

    public function __construct($instance = "basket")
    {
        $this->attributes['instance'] = $instance;
        $this->attributes['slug'] = Str::random(40);
        // change slug if exists
        while (self::where('slug', $this->attributes['slug'])->count() > 0) {
            $this->attributes['slug'] = Str::random(40);
        }
    }

    /*
    *   TODO: Add Exceptions and make them reportable if necessary https://laravel.com/docs/9.x/errors#reporting-exceptions
    */


    /**
     * Items
     * @return Relation Basket Item with this basket as their parent
     */
    public function items()
    {
        return $this->hasMany(BasketItem::class);
    }

    /**
     * Add to basket
     * @param Stowable object
     * @param int $qty Quantity of stowable
     * @param array $options List of options that make this product unique
     * @return BasketItem Model that is created or found/modified
     */
    public function add(Stowable $item, $qty = 1, array $options = [])
    {

        $this->verifyStowability($item::class);

        // Add basket to DB if not added
        if (!$this->getKey()) {
            $this->push();
        }

        $basketItem = $this->items()->where([
            "stowable_type" => $item::class,
            "stowable_id" => $item->getKey()
        ])->first();

        if ($basketItem && $basketItem->options == $options) {
            $basketItem->quantity += $qty;
            $basketItem->save();
        } else {
            $basketItem = new BasketItem();
            $basketItem->options = $options;
            $basketItem->quantity = $qty;
            $basketItem->stowable_type = $item::class;
            $basketItem->stowable_id = $item->getKey();
            $this->items()->save($basketItem);
        }

        return $basketItem->fresh();
    }

    /**
     * Update item in basket
     * @param int BasketItem object or id
     * @param int $qty Quantity to set
     * @param array $options List of options that make this product unique
     * @return int Count of items updates
     */
    public function change(BasketItem|int $basketItem, $qty = 1, array $options = [])
    {
        $basketItem = $basketItem instanceof BasketItem
            ? $basketItem
            : BasketItem::findOrFail($basketItem);
        $basketItem->quantity = $qty;
        $basketItem->options = $options;
        return $basketItem->update();
    }

    /**
     * Remove from basket
     * @param $id ID of BasketItem
     * @return int Count of items deleted
     */
    public function remove(BasketItem|int $basketItem)
    {
        return $basketItem instanceof BasketItem
            ? $basketItem->delete()
            : BasketItem::findOrFail($basketItem)->delete();
    }

    /**
     * Merge a basket with this basket
     * @param Basket object to merge with this
     */
    public function merge(Basket $toMerge)
    {
        if (!$toMerge->id == $this->id) {
            if ($toMerge->items()->count() > 0) {
                $toMergeItems = $toMerge->items;
                foreach ($toMergeItems as $item) {
                    $this->add($item->stowable, $item->quantity, $item->options);
                }
            }
            $this->save();
        }
    }

    /**
     * Clone a basket with a unique uri
     * @return Basket clone of this basket
     */
    public function clone()
    {
        $new = new Basket($this->instance);
        $new->push();
        $new->items()->sync($this->items);
        return $new;
    }




    /**
     * Verify the item type is within the allowed types for this instance
     * @param string class name to check compatibility
     * @return Boolean whether item is stowable with current config
     */

    public function verifyStowability($className)
    {
        $config = config("basket.instances.$this->instance");
        if (isset($config) && !in_array($className, $config)) {
            throw new UnstowableObjectException("$className not allowed in basket instance \"$this->instance\".");
        }
        return true;
    }

}
