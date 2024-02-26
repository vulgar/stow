<?php

namespace Vulgar\LaravelBasket\Tests\Feature;

use Illuminate\Database\Eloquent\Model;
use TypeError;
use Vulgar\LaravelBasket\Exceptions\UnstowableObjectException;
use Vulgar\LaravelBasket\Models\Basket;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Event;
use Vulgar\LaravelBasket\Events\BasketCreatedEvent;
use Vulgar\LaravelBasket\Events\BasketDeletedEvent;
use Vulgar\LaravelBasket\Events\BasketItemCreatedEvent;
use Vulgar\LaravelBasket\Events\BasketItemUpdatedEvent;
use Vulgar\LaravelBasket\Events\BasketItemDeletedEvent;
use Vulgar\LaravelBasket\Tests\Models\StowableTest;
use Vulgar\LaravelBasket\Tests\Models\UnstowableTest;
use Vulgar\LaravelBasket\Tests\TestCase;

class BasketTest extends TestCase
{

    use RefreshDatabase;


    /**
     * A test to ensure that objects implementing Stowable
     * are capable of being added to the basket.
     *
     * @return void
     */
    public function testStowableObjectsAreStowable()
    {
        $stowableObject = new StowableTest();
        $basket = new Basket();
        $basket->add($stowableObject);
        $this->assertEquals(1, $basket->items()->count());
    }


    /**
     * A test to ensure that objects not implementing Stowable
     * are not capable of being added to the basket.
     *
     * @return void
     */
    public function testUnstowableObjectsUnstowable()
    {
        $unstowableObject = new UnstowableTest();
        $basket = new Basket();
        $this->expectException(TypeError::class);
        $basket->add($unstowableObject);
    }


    /**
     * A test to ensure that objects not within instance restrictions
     * are not capable of being added to the basket, even when implementing
     * Stowable.
     *
     * @return void
     */
    public function testUnconfiguredInstancesRestricted()
    {
        $stowableObject = new StowableTest();
        Config::set('basket.instances.basket', [Model::class]);
        $basket = new Basket();
        $this->expectException(UnstowableObjectException::class);
        $basket->add($stowableObject);
    }


    /**
     * A test to ensure that objects whose class are within instance restrictions
     * are capable of being added to the basket.
     *
     * @return void
     */
    public function testConfiguredInstancesStowable()
    {
        $stowableObject = new StowableTest();
        Config::set('basket.instances.basket', [$stowableObject::class]);
        $basket = new Basket();
        $basket->add($stowableObject);
        $this->assertEquals(1, $basket->items()->count());
    }


    /**
     * A test to ensure that even objects whose class are within instance restrictions
     * are not capable of being added to the basket if they do not implement Stowable.
     *
     * @return void
     */
    public function testConfiguredInstancesUnstowableWhenNotImplementingStowable()
    {
        $unstowableObject = new UnstowableTest();
        Config::set('basket.instances.basket', [$unstowableObject::class]);
        $basket = new Basket();
        $this->expectException(TypeError::class);
        $basket->add($unstowableObject);
    }

    /**
     * A test to ensure that the same item added twice increments item quantity
     * and does not add as a new item
     *
     * @return void
     */
    public function testDuplicateIncrementsQuantity()
    {
        $stowableObject = new StowableTest();
        $basket = new Basket();
        $basket->add($stowableObject);
        $basketItem = $basket->add($stowableObject, 2);
        $this->assertEquals(3, $basketItem->quantity);
    }

    /**
     * A test to ensure that the same item, added twice, with different options
     * does not increment item quantity, and instead adds as a new item
     *
     * @return void
     */
    public function testSameItemDifferentOptionsAddsAsNew()
    {
        $stowableObject = new StowableTest();
        $basket = new Basket();
        $basket->add($stowableObject, options: ["loves" => "cats"]);
        $basket->add($stowableObject, options: ["loves" => "dogs"]);
        $this->assertEquals(2, $basket->items()->count());
    }

    /**
     * A test to ensure that items are properly deleted using the remove function
     * items will be removed whether the BasketItem or the BasketItem->id is passed
     * @return void
     */
    public function testBasketItemRemoval()
    {
        $stowableObject = new StowableTest();
        $basket = new Basket();

        $basketItem = $basket->add($stowableObject);
        $basket->remove($basketItem);
        $this->assertEquals(0, $basket->items()->count());

        $basketItem = $basket->add($stowableObject);
        $basket->remove($basketItem->id);
        $this->assertEquals(0, $basket->items()->count());
    }


    /**
     * A test to ensure that basket creation emits appropriate event
     * @return void
     */
    public function testBasketCreatedEventEmitted()
    {
        Event::fake();

        $basket = new Basket();
        $basket->push();

        Event::assertDispatched(BasketCreatedEvent::class, function ($event) use ($basket) {
            return $event->basket->id === $basket->id;
        });
    }


    /**
     * A test to ensure that basket creation emits appropriate event
     * @return void
     */
    public function testBasketDeletedEventEmitted()
    {
        Event::fake();

        $basket = new Basket();
        $basket->push();
        $basket->delete();

        Event::assertDispatched(BasketDeletedEvent::class, function ($event) use ($basket) {
            return $event->basket->id === $basket->id;
        });
    }

    /**
     * A test to ensure that basket item add emits appropriate event
     * @return void
     */
    public function testBasketItemCreatedEventEmittedThroughBasketAdd()
    {
        Event::fake();

        $stowableObject = new StowableTest();
        $basket = new Basket();
        $basket->push();
        $basketItem = $basket->add($stowableObject);

        Event::assertDispatched(BasketItemCreatedEvent::class, function ($event) use ($basketItem) {
            return $event->basketItem->id === $basketItem->id;
        });
    }

    /**
     * A test to ensure that basket item change emits appropriate event
     * @return void
     */
    public function testBasketItemUpdatedEventEmittedThroughBasketChange()
    {
        Event::fake();

        $stowableObject = new StowableTest();
        $basket = new Basket();
        $basket->push();
        $basketItem = $basket->add($stowableObject);
        $basket->change($basketItem, 6, ["cat"=>"burglar"]);

        Event::assertDispatched(BasketItemUpdatedEvent::class, function ($event) use ($basketItem) {
            return $event->basketItem->id === $basketItem->id;
        });
    }

    /**
     * A test to ensure that basket item remove emits appropriate event
     * @return void
     */
    public function testBasketItemDeletedEventEmittedThroughBasketRemove()
    {
        Event::fake();

        $stowableObject = new StowableTest();
        $basket = new Basket();
        $basket->push();
        $basketItem = $basket->add($stowableObject);
        $basket->remove($basketItem);

        Event::assertDispatched(BasketItemDeletedEvent::class, function ($event) use ($basketItem) {
            return $event->basketItem->id === $basketItem->id;
        });
    }

    /**
     * A test to ensure that Basket::deleting emits appropriate event
     * @return void
     */
    public function testBasketItemDeletedEventEmittedThroughBasketDeleting()
    {
        Event::fake([BasketItemDeletedEvent::class]);

        $stowableObject = new StowableTest();
        $basket = new Basket();
        $basket->push();
        $basketItem = $basket->add($stowableObject);
        $basket->delete();

        $this->assertTrue($basketItem->fresh()->trashed());

        Event::assertDispatched(BasketItemDeletedEvent::class, function ($event) use ($basketItem) {
            return $event->basketItem->id === $basketItem->id;
        });
    }


    /**
     * Perform any work that should take place before the database has started refreshing.
     *
     * @return void
     */
    protected function beforeRefreshingDatabase(){}

}
