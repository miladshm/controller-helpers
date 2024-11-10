<?php

namespace Miladshm\ControllerHelpers\Tests;

use Miladshm\ControllerHelpers\Models\TestModel;
use Miladshm\ControllerHelpers\Providers\TestServiceProvider;

class TestCase extends \Orchestra\Testbench\TestCase
{

    public function setUp(): void
    {
        parent::setUp();
        $this->artisan(
            'migrate',
            ['--database' => 'testbench']
        )->run();
        TestModel::factory(50)->connection('testbench')->create();
    }

    protected function getPackageProviders($app)
    {
        return [
            TestServiceProvider::class
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // Setup default database to use sqlite :memory:
        $app['config']->set('database.default', 'testbench');
        $app['config']->set('locale', 'fa');
        $app['config']->set('database.connections.testbench', [
            'driver' => 'sqlite',
            'database' => ':memory:',
            'prefix' => '',
        ]);
    }
}