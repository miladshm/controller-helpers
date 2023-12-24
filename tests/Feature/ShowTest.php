<?php

namespace Miladshm\ControllerHelpers\Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;

class ShowTest extends \Miladshm\ControllerHelpers\Tests\TestCase
{
    use RefreshDatabase;

    public function test_show_json()
    {
        $id = rand(1, 50);
        $res = $this->getJson("/testing/$id");

        $res->assertSuccessful();
        $res->assertJsonStructure(getConfigNames('response.field_names'));
        $res->assertJsonIsObject(getConfigNames('response.field_names.data'));
        $res->assertJsonPath(getConfigNames('response.field_names.message'), __('responder::messages.success_status.status'));
        $res->assertJsonPath(getConfigNames('response.field_names.data') . ".id", $id);
    }
}