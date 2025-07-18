<?php

namespace Miladshm\ControllerHelpers\Tests;

use Miladshm\ControllerHelpers\Models\ParentModel;
use Miladshm\ControllerHelpers\Models\TestModel;
use Miladshm\ControllerHelpers\Models\TestRelModel;
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
        $tests = TestModel::factory(50)
            ->connection('testbench')
            ->for(ParentModel::factory(), 'parent')
            ->has(TestRelModel::factory()->count(3), 'rels')
            ->create();
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
        $app['config']->set('app.debug', 'true');
    }
}
