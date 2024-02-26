<?php

namespace Vulgar\LaravelBasket\Providers;

use Illuminate\Support\ServiceProvider;

class BasketProvider extends ServiceProvider
{
    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
       // Publish the config file to the installer's config directory
        $this->publishes([
            __DIR__ . '/../config/basket.php' => config_path('basket.php'),
        ]);

        // Register the migrations for next `php artisan migrate` execution
        $this->loadMigrationsFrom(__DIR__.'/../database/migrations');

        // Register the Event Listeners
        $this->app->register(EventServiceProvider::class);
    }
}
