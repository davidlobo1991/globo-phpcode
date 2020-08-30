<?php

namespace Globobalear\Products;

use Illuminate\Support\ServiceProvider;

class ProductsServiceProvider extends ServiceProvider
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
        
        /* MIGRATIONS */
        $this->loadMigrationsFrom([__DIR__ . '/../database/migrations']);
        
        /* ROUTES */
        $this->loadRoutesFrom(__DIR__ . '/../routes/web.php');
       
        /* VIEWS */
        $this->loadViewsFrom(__DIR__ . '/../views', 'products');
        //$this->publishes([__DIR__.'/../views' => resource_path('views/vendor/shows')], 'views');
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
