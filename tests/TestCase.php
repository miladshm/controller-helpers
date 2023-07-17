<?php

namespace Miladshm\ControllerHelpers\Tests;

use Miladshm\ControllerHelpers\Providers\ServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan(
            'migrate',
            ['--database' => 'testbench']
        )->run();
    }

    protected function getPackageProviders($app)
    {
        return [
            ServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}