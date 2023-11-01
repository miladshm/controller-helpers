<?php

namespace Miladshm\ControllerHelpers\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Miladshm\ControllerHelpers\TestModel;

class StoreTest extends \Miladshm\ControllerHelpers\Tests\TestCase
{
    use RefreshDatabase;

    public function test_data_store_json()
    {
        $res = $this->postJson('/testing', ['code' => $uuid = fake()->uuid, 'order' => 1, 'status' => 1]);

        $res->assertSuccessful();
        $res->assertJsonStructure(getConfigNames('response.field_names'));
        $res->assertJsonIsObject(getConfigNames('response.field_names.data'));
        $res->assertSeeText(__('responder::messages.success_store.status'));
        $this->assertDatabaseHas($table = (new TestModel)->getTable(), ['code' => $uuid, 'order' => 1, 'status' => 1]);
        $this->assertDatabaseCount($table, 51);
    }

    public function test_data_store_json_with_errors()
    {
        $res = $this->postJson('/testing');

        $res->assertStatus(422);
        $res->assertJsonStructure(getConfigNames('response.field_names'));
        $res->assertJsonIsObject(getConfigNames('response.field_names.data'));
    }

    public function test_data_store_redirect()
    {
        $res = $this->post('/testing', ['code' => $uuid = fake()->uuid, 'order' => 1, 'status' => 1]);

        $res->assertRedirect();
        $res->assertSessionHasNoErrors();
        $this->assertDatabaseHas($table = (new TestModel)->getTable(), ['code' => $uuid, 'order' => 1, 'status' => 1]);
        $this->assertDatabaseCount($table, 51);
    }

    public function test_data_store_redirect_with_errors()
    {
        $res = $this->post('/testing');
        $res->assertInvalid();
        $res->assertSessionHasErrors();
    }
}