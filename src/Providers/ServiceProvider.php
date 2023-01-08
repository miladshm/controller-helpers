<?php

namespace Miladshm\ControllerHelpers\Providers;

class ServiceProvider extends \Illuminate\Support\ServiceProvider
{

    public function boot()
    {
        $this->loadTranslationsFrom(__DIR__ . "/../../lang",'responder');
    }

}