
# Laravel Stow Package

## Overview
The Laravel Stow Package is designed to handle complex basket scenarios in a Laravel application. This includes managing baskets with various items, responding to basket lifecycle events, and optimizing basket storage for performance and user experience.


## Installation
To integrate the Stow package into your Laravel project, execute:
```bash
composer require vulgar/stow
```
Then, publish and migrate the database tables:
```bash
php artisan vendor:publish --provider="Vulgar\Stow\StowProvider"
php artisan migrate
```


## Configuration
Define basket instances and restrictions in `config/stow.php` to ensure type safety and integrity:
```php
return [
    'instances' => [
        'cart' => [Product::class, Item::class],
        // Additional instances...
    ],
];
```
Instances not listed will remain unrestricted, accepting any `Stowable` models.


### Creating Baskets
Instantiate a basket, specifying an optional type (instance name):
```php
$cart = new Basket('cart'); // restricted to Product and Item (see stow.php config)
$wishlist = new Basket(); // Unrestricted, any item implementing Stowable interface
```


### Adding Items to Baskets
Add Stowable interface to models to allow them to be added to baskets:
```php
class Product implements Stowable
{
    // ...
}
```

Add items respecting instance restrictions and options:
```php
$product = new Product();
$cart->add($product, 1, ['size' => 'M', 'color' => 'blue']);

$item = new Item();
$cart->add($item, 2);

// Service has not been explicitly allowed in the 'cart' instance (see stow.php config)
$service = new Service();
$cart->add($service); // Throws UnstowableObjectException

$wishlist->add($service); // instances without restrictions can accept any Stowable
```


### Updating Basket Items
Update item details within the basket:
```php
$cart->change($product, 3, ['size' => 'L']); // Update quantity and options
```


### Removing Items from Baskets
Remove items from the basket by item object or ID:
```php
$cart->remove($product);
```

### Retrieving Basket Items
Retrieve items from the basket, optionally filtering by type:
```php
$cart->items(); // All items
$cart->items(Product::class); // Only products
```


## Storing Baskets
Optimize basket storage using sessions or caching, naming dynamically:
```php
session(['basket_id' => $cart->id]);
Cache::put('basket_' . $cart->instance, $cart, now()->addHours(2));
```


## Events and Listeners
Listen and respond to basket and item lifecycle events:
```php
protected $listen = [
    'Vulgar\Stow\Events\BasketCreatedEvent' => ['App\Listeners\YourBasketCreatedListener'],
    // Other events...
];
```
Define listeners to perform actions such as logging, notifications, or updates.


## Best Practices and Possible Pitfalls
- Ensure correct instance configuration to prevent unintended item types.
- Utilize events for syncing basket states across the application.
- Monitor performance when using large baskets, and consider caching strategies.


## Conclusion
The Laravel Stow Package provides a comprehensive solution for managing baskets in Laravel applications, offering customization, event handling, and performance optimizations.
