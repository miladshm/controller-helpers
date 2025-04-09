<?php

namespace Miladshm\ControllerHelpers\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Miladshm\ControllerHelpers\Models\TestModel;
use Miladshm\ControllerHelpers\Tests\TestCase;

class HasDuplicateTest extends TestCase
{

    public function testDuplicate()
    {
        $model = TestModel::factory()->create();
        $response = $this->post("test/duplicate/$model->id");


        $response->assertStatus(200);

        $this->assertModelExists($model);
    }
}
