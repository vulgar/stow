<?php

namespace Vulgar\LaravelBasket\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Vulgar\LaravelBasket\Interfaces\Stowable;
use Illuminate\Support\Str;
use Exception;
use Session;

class Basket extends Model
{
    use SoftDeletes;

	protected $fillable = ['user_id', 'instance', 'slug'];

    public function __construct($instance = "basket", $session = true)
    {
        $this->attributes['instance'] = $instance;

        $this->attributes['slug'] = Str::random(40);
    }

    /*
    *   TODO: Add Exceptions and make them reportable if necessary https://laravel.com/docs/9.x/errors#reporting-exceptions
    */


	/**
	 * Items
	 * @return Relation Basket Item with this basket as their parent
	 */
	public function items(){
		return $this->hasMany(BasketItem::class);
	}

	/**
	 * Add to basket
	 * @param Stowable object
	 * @param int $qty Quantity of stowable
	 * @param array $options List of options that make this product unique
	 * @return BasketItem Model that is created or found/modified
	 */
	public function add(Stowable $item, $qty = 1, array $options = []) {

        // Add basket to DB if not added
        if(!$this->getKey()){
            $this->push();
        }

        $basketItems = $this->items;


        if(!$this->verifyStowability($item::class)){
            throw new Exception("Damnit, Bobby!");
        }


		$createNew = true;

        foreach($basketItems as $basketItem){
            if($basketItem->stowable == $item && $basketItem->options == $options){
                $basketItem->quantity += $qty;
                $basketItem->save();
                $createNew = false;
            }
        }

        if($createNew){
            $basketItem = new BasketItem();
            $basketItem->options = $options;
            $basketItem->quantity = $qty;
            $basketItem->stowable_type = $item::class;
            $basketItem->stowable_id = $item->getKey();
            // $basketItem->push();
            $this->items()->save($basketItem);
            $this->load('items.stowable');
        }

        return $basketItem;

    }

	/**
	 * Update item in basket
	 * @param int BasketItem id
	 * @param int $qty Quantity to set
	 * @param array $options List of options that make this product unique
	 * @return int Count of items updates
	 */
    public function change($id, $qty = 1, array $options = [])
    {
        $basketItem = $this->items()->find($id);
		$basketItem->quantity = $qty;
		$basketItem->options = $options;
		return $basketItem->update();
    }

	/**
	 * Remove from basket from the session
	 * @param Basket $basket
	 * @return int Count of items deleted
	 */
	public function clear($basket) {
		if(Session::has($basket->instance)){
			Session::forget($basket->instance);
		}
	}

	/**
	 * Remove from basket
	 * @param $id ID of BasketItem
	 * @return int Count of items deleted
	 */
    public function remove($id) {
        $basketItem = $this->items()->find($id);
		return $basketItem->delete();
    }

	/**
	 * Merge a basket with this basket
	 * @param Basket object to merge with this
	 */
    public function merge(Basket $toMerge)
    {
		if(!$toMerge->id == $this->id){
			if($toMerge->items()->count() > 0){
				$toMergeItems = $toMerge->items;
				foreach($toMergeItems as $item){
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
	public function clone(){
        $new = $this->replicate();
        $new->push();
        $new->items()->sync($this->items);
        return $new;
	}




	/**
	 * Verify the item type is within the allowed types for this instance
     * @param string class name to check compatibility
	 * @return Boolean whether item is stowable with current config
	 */

    public function verifyStowability($className){
        if(($config = config("basket.$this->instance")) && !in_array($className, $config))
                return false;

        return true;
    }



	public static function boot()
	{
		parent::boot();

		/**
		 * Remove from basket
		 * @param Basket $basket (this)
		 * Delete items in DB, remove basket from Session
		 */
		self::deleting(function($basket) {
			$basketItems = $basket->items()->get();
			foreach($basketItems as $basketItem){
				$basketItem->delete();
			}
			if(Session::has($basket->instance)){
				Session::forget($basket->instance);
			}
		});

	}



}
