<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     *
     * @return void
     */
    public function boot()
    {
        //
    }

    /**
     * Register any application services.
     *
     * @return void
     */
    public function register()
    {
        $this->app->singleton('App\Contracts\Action\Store', function($app) {
            return $this->app->make('App\Database\DynamoDb');
        });
        $this->app->singleton('App\Contracts\Action\Service', function($app) {
            return $app->make('App\Services\ActionService');
        });
    }
}
