<?php

namespace Iateadonut\NPAPayProcessing\Providers;

use Illuminate\Support\ServiceProvider;

/**
* NPAPayProcessing service provider
*
*/

class NPAServiceProvider extends ServiceProvider
{
    /**
    * Bootstrap services.
    *
    * @return void
    */
    public function boot()
    {
    }

    /**
    * Register services.
    *
    * @return void
    */
    public function register()
    {
        $this->registerConfig();
    }

    /**
    * Register package config.
    *
    * @return void
    */
    protected function registerConfig()
    {
        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/paymentmethods.php', 'paymentmethods'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/system.php', 'core'
        );

        $this->mergeConfigFrom(
            dirname(__DIR__) . '/Config/npa.php', 'npa'
        );

        $this->loadRoutesFrom( dirname(__DIR__) . '/Http/routes.php' );

        $this->loadViewsFrom(  dirname(__DIR__) . '/Resources/views' , 'npa' );

        $this->loadMigrationsFrom(__DIR__ . '/../Database');
    }
}
