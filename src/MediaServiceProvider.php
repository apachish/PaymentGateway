<?php

namespace Apachish\Media;


use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;


class MediaServiceProvider extends ServiceProvider
{
    protected $commands = [
    ];

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/errors.php','errors');
        $this->commands($this->commands);
    }

    public function boot()
    {
        $this->loadDependencies()
            ->publishDependencies();
        $this->callObserver();

    }

    private function loadDependencies()
    {
        $this->loadMigrationsFrom(__DIR__ . '/database/migrations');

        $this->loadRoutesFrom(__DIR__ . '/routes/api.php');
        return $this;
    }

    private function publishDependencies(){
        $this->publishes([
            __DIR__.'/database/migrations' => database_path('/migrations')
        ], 'media-apachish-migration');

        $this->publishes([
            __DIR__ . '/database/Seeds' => database_path('/seeders'),
        ], 'media-apachish-seeds');
        $this->publishes([
            __DIR__.'/config/errors.php' => config_path('errors.php'),
        ],'media-apachish-config-error');



    }



    private function callObserver(): void
    {

    }
}
