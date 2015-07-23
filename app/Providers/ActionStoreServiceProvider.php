<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class ActionStoreServiceProvider extends ServiceProvider
{

    /**
     * Indicates if loading of the provider is deferred.
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Bootstrap the application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register the application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('App\Contracts\Action\Store', function($app) {
            return $this->app->make('App\Database\DynamoDb');
        });
    }

    public function provides()
    {
        return ['App\Contracts\Action\Store'];
    }
}
