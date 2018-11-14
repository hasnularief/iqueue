<?php

namespace Hasnularief\Iqueue;

use Illuminate\Support\ServiceProvider;

class IqueueServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        $this->loadViewsFrom(__DIR__.'/views','iqueue');

        $this->publishes([
            __DIR__.'/views'               => base_path('resources/views/iqueue'),
            __DIR__.'/config/iqueue.php'   => config_path('iqueue.php'),
            __DIR__.'/migrations'          => base_path('database/migrations'),
            __DIR__.'/resources/assets/js' => public_path('iqueue/js'),
            __DIR__.'/resources/assets/css' => public_path('iqueue/css'),
            __DIR__.'/resources/assets/audio' => public_path('iqueue/audio'),
        ]);
    }

    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        $this->mergeConfigFrom(
            __DIR__.'/config/iqueue.php', 'iqueue'
        );

        include __DIR__.'/routes.php';

        $this->app->make('Hasnularief\Iqueue\IqueueController');
    }
}
