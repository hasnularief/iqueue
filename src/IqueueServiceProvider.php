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
            __DIR__.'/config/iqueue.php'   => config_path('iqueue.php'),
            __DIR__.'/migrations'          => base_path('database/migrations'),
            __DIR__.'/resources/assets/js' => public_path('iqueue/js'),
            __DIR__.'/resources/assets/css' => public_path('iqueue/css'),
            __DIR__.'/resources/assets/audio' => public_path('iqueue/audio'),
            __DIR__.'/resources/assets/fonts' => public_path('iqueue/fonts'),
            __DIR__.'/resources/assets/images' => public_path('iqueue/images'),
        ], 'iqueue');

        $this->publishes([
            __DIR__.'/views/publish' => base_path('resources/views/iqueue'),
        ], 'iqueue-view');
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
