<?php

namespace Miladshm\ControllerHelpers\Providers;

class TestServiceProvider extends ServiceProvider
{

    public function boot()
    {
        parent::boot();
        $this->loadMigrationsFrom(__DIR__ . '/../../database/migrations');

        $this->loadRoutesFrom(__DIR__ . '/../../routes/web.php');


    }

}