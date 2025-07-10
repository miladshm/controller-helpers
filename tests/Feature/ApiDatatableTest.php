<?php

namespace Miladshm\ControllerHelpers\Tests\Feature;


use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Config;
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

    public function test_datatable_response_order()
    {

        $sortParam = getConfigNames('params.sort');
        $query = Arr::query([$sortParam . '[column]' => $order = fake()->randomElement(['id', 'code', 'order', 'status'])]);
        $response = $this->getJson("/testing?$query");

        $response->assertSuccessful();
        $response->assertJsonStructure(getConfigNames('response.field_names'));
        $response->assertJsonIsObject(getConfigNames('response.field_names.data'));
        $response->assertSeeText(__('responder::messages.success_status.status'));

        $path = ".filters.$sortParam.column";

        $response->assertJsonPath(getConfigNames('response.field_names.data') . $path, $order);
    }

    public function test_datatable_response_search()
    {

        $searchParam = getConfigNames('params.search');
        $query = Arr::query([$searchParam => $search = fake()->text(10)]);
        $response = $this->getJson("/testing?$query");

        $response->assertSuccessful();
        $response->assertJsonStructure(getConfigNames('response.field_names'));
        $response->assertJsonIsObject(getConfigNames('response.field_names.data'));
        $response->assertSeeText(__('responder::messages.success_status.status'));
        $path = ".filters.$searchParam";
        $response->assertJsonPath(getConfigNames('response.field_names.data') . $path, $search);
    }

    public function test_datatable_response_page_length()
    {
        $query = Arr::query([getConfigNames('params.page_length') => $length = min(fake()->randomNumber(3), getConfigNames('performance.max_page_length'))]);
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

    public function test_datatable_response_with_all_flag()
    {
        Config::set('controller-helpers.get_all_wrapping.enabled', true);
        Config::set('controller-helpers.get_all_wrapping.wrapper', 'data');

        $response = $this->getJson('/testing?all=1');

        $response->assertSuccessful();
        $response->assertJsonStructure(getConfigNames('response.field_names'));
        $response->assertJsonIsObject(getConfigNames('response.field_names.data'));
        $response->assertSeeText(__('responder::messages.success_status.status'));

        $response->assertJsonIsArray(getConfigNames('response.field_names.data') . ".items." . getConfigNames('get_all_wrapping.wrapper'));

    }

    public function test_datatable_response_with_all_flag_when_data_wrapper_is_disabled()
    {
        Config::set('controller-helpers.get_all_wrapping.enabled', false);
        $response = $this->getJson('/testing?all=1');

        $response->assertSuccessful();
        $response->assertJsonStructure(getConfigNames('response.field_names'));
        $response->assertJsonIsObject(getConfigNames('response.field_names.data'));
        $response->assertSeeText(__('responder::messages.success_status.status'));

        $response->assertJsonIsArray(getConfigNames('response.field_names.data') . ".items");
    }

    public function test_datatable_response_with_counts()
    {
        $response = $this->getJson('/testing?count[]=rels');

        $response->assertSuccessful();
        $response->assertJsonStructure(getConfigNames('response.field_names'));
        $response->assertJsonIsObject(getConfigNames('response.field_names.data'));
        $response->assertSeeText(__('responder::messages.success_status.status'));


        foreach ($response->json(getConfigNames('response.field_names.data') . '.items.data') as $item) {
            $this->assertArrayHasKey('rels_count', $item);
        }
    }
}
