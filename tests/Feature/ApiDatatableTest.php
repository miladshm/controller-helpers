<?php

namespace Miladshm\ControllerHelpers\Tests\Feature;


use Illuminate\Support\Arr;
use Miladshm\ControllerHelpers\Http\Controllers\TestController;
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

    public function test_datatable_response_page_length()
    {
        $query = Arr::query([getConfigNames('params.page_length') => $length = fake()->randomNumber(3)]);
        $response = $this->get("/testing?$query");

        $response->assertSuccessful();
        $response->assertJsonStructure(getConfigNames('response.field_names'));
        $response->assertJsonIsObject(getConfigNames('response.field_names.data'));
        $response->assertSeeText(__('responder::messages.success_status.status'));
        $controller = new TestController;
        $path = $controller->getApiResource() ? ".items.meta.per_page" : ".items.per_page";
        $response->assertJsonPath(getConfigNames('response.field_names.data') . $path, $length);
    }

    public function test_datatable_response_default_page_length()
    {
        $response = $this->get("/testing");

        $response->assertSuccessful();
        $response->assertJsonStructure(getConfigNames('response.field_names'));
        $response->assertJsonIsObject(getConfigNames('response.field_names.data'));
        $response->assertSeeText(__('responder::messages.success_status.status'));
        $controller = new TestController;
        $path = $controller->getApiResource() ? ".items.meta.per_page" : ".items.per_page";
        $response->assertJsonPath(getConfigNames('response.field_names.data') . $path, getConfigNames("default_page_length"));
    }
}
