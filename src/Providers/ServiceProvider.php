<?php

namespace Miladshm\ControllerHelpers\Providers;

use Illuminate\Support\Facades\Config;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . "/../../lang", 'responder');
        $this->loadJsonTranslationsFrom(__DIR__ . "/../../lang");
        $this->publishes([
            __DIR__ . "/../../config/controller-helpers.php" => config_path('controller-helpers.php')
        ], "controller-helpers-config");

        date_default_timezone_set(Config::get('app.timezone'));
    }

    public function register()
    {
        $this->mergeConfigFrom(__DIR__ . "/../../config/controller-helpers.php", 'controller-helpers');

    }

}
