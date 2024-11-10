<?php

namespace Miladshm\ControllerHelpers\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Miladshm\ControllerHelpers\Models\TestModel;

class DeleteTest extends \Miladshm\ControllerHelpers\Tests\TestCase
{
    use RefreshDatabase;

    public function test_delete_json()
    {
        $id = rand(1, 50);
        $code = $this->get("/testing/$id")->json(getConfigNames('response.field_names.data') . '.code');
        $res = $this->deleteJson("/testing/$id");

        $res->assertSuccessful();
        $res->assertJsonStructure(getConfigNames('response.field_names'));
        $res->assertJsonIsObject(getConfigNames('response.field_names.data'));
        $res->assertSeeText(__('responder::messages.success_delete.status'));
        $this->assertDatabaseMissing($table = (new TestModel)->getTable(), ['code' => $code]);
    }

    public function test_delete_redirect()
    {
        $id = rand(1, 50);
        $code = $this->get("/testing/$id")->json(getConfigNames('response.field_names.data') . '.code');
        $res = $this->delete("/testing/$id");

        $res->assertRedirect();
        $this->assertDatabaseMissing($table = (new TestModel)->getTable(), ['code' => $code]);
    }
}