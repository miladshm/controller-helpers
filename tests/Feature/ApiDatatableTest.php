<?php

namespace Miladshm\ControllerHelpers\Tests\Feature;


use Miladshm\ControllerHelpers\Tests\TestCase;

class ApiDatatableTest extends TestCase
{
    public function test_datatable_response()
    {
        $response = $this->getJson('/testing');

        $response->assertSuccessful();
        $response->assertJsonStructure(getConfigNames('response.field_names'));
        $response->assertJsonIsObject(getConfigNames('response.field_names.data'));
        $response->assertSeeText(__('responder::messages.success_status.status'));
    }
}