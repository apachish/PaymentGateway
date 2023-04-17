<?php

namespace Apachish\PaymentGateway;


use Illuminate\Events\Dispatcher;
use Illuminate\Support\ServiceProvider;


class PaymentGatewayServiceProvider extends ServiceProvider
{
    protected $commands = [
    ];

    public function register()
    {
        $this->mergeConfigFrom(__DIR__.'/config/errors.php','errors');
        $this->mergeConfigFrom(__DIR__.'/config/gateway_payment.php','gateway_payment');
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

        $this->loadRoutesFrom(__DIR__ . '/routes/web.php');
        return $this;
    }

    private function publishDependencies(){
        $this->publishes([
            __DIR__.'/config/errors.php' => config_path('errors.php'),
        ],'apachish-config-error');

        $this->publishes([
            __DIR__.'/config/gateway_payment.php' => config_path('gateway_payment.php'),
        ],'apachish-config-gateway_payment');

    }



    private function callObserver(): void
    {

    }
}
