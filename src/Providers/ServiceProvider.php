<?php

namespace Miladshm\ControllerHelpers\Providers;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . "/../../lang", 'responder');

        $this->publishes([
            __DIR__ . "/../../config/controller-helpers.php" => config_path('courier.php')
        ]);
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . "/../../config/controller-helpers.php", 'controller-helpers');

    }

}