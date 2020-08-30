<?php

namespace Globobalear\Customers;

use Illuminate\Support\ServiceProvider;

class CustomersServiceProvider extends ServiceProvider
{
    /**
     * Perform post-registration booting of services.
     *
     * @return void
     */
    public function boot()
    {
        /* CONFIG */
        $this->publishes([__DIR__ . '/../config/crs.php' => config_path('crs.php')], 'config');

        /* MIGRACIONES */
        $this->loadMigrationsFrom([__DIR__.'/../database/migrations']);

        /* ROUTES */
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');

        /* VIEWS */
        $this->loadViewsFrom(__DIR__.'/../views', 'customers');
        //$this->publishes([__DIR__.'/../views' => resource_path('views/vendor/customers')], 'views');
    }

    /**
     * Register any package services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__ . '/../config/crs.php', 'crs'
        );
    }
}
