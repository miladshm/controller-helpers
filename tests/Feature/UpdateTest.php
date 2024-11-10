<?php

namespace Miladshm\ControllerHelpers\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Miladshm\ControllerHelpers\Models\TestModel;

class UpdateTest extends \Miladshm\ControllerHelpers\Tests\TestCase
{
    use RefreshDatabase;

    public function test_data_update_json()
    {
        $res = $this->putJson("/testing/" . rand(1, 50), ['code' => $uuid = fake()->uuid, 'order' => 1, 'status' => 1]);

        $res->assertSuccessful();
        $res->assertJsonStructure(getConfigNames('response.field_names'));
        $res->assertJsonIsObject(getConfigNames('response.field_names.data'));
        $res->assertSeeText(__('responder::messages.success_update.status'));
        $res->assertJsonPath(getConfigNames('response.field_names.data') . ".code", $uuid);
        $this->assertDatabaseHas($table = (new TestModel)->getTable(), ['code' => $uuid, 'order' => 1, 'status' => 1]);
    }

    public function test_data_update_json_with_errors()
    {
        $res = $this->putJson('/testing/' . rand(1, 50));

        $res->assertStatus(422);
        $res->assertJsonStructure(getConfigNames('response.field_names'));
        $res->assertJsonIsObject(getConfigNames('response.field_names.data'));
    }

    public function test_data_update_redirect()
    {
        $res = $this->put('/testing/' . rand(1, 50), ['code' => $uuid = fake()->uuid, 'order' => 1, 'status' => 1]);

        $res->assertRedirect();
        $res->assertSessionHasNoErrors();
        $this->assertDatabaseHas($table = (new TestModel)->getTable(), ['code' => $uuid, 'order' => 1, 'status' => 1]);

    }

    public function test_data_update_redirect_with_errors()
    {
        $res = $this->put('/testing/' . rand(1, 50));
        $res->assertInvalid();
        $res->assertSessionHasErrors();
    }
}