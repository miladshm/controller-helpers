<?php

namespace Miladshm\ControllerHelpers\Tests\Feature;


use Illuminate\Database\Eloquent\Model;
use Miladshm\ControllerHelpers\Helpers\DatatableBuilder;
use Miladshm\ControllerHelpers\Http\Requests\ListRequest;
use Miladshm\ControllerHelpers\Http\Traits\HasApiDatatable;
use Miladshm\ControllerHelpers\TestModel;
use Miladshm\ControllerHelpers\Tests\TestCase;

class ApiDatatableTest extends TestCase
{

    use HasApiDatatable;

    public function test_datatable_response()
    {
        $response = $this->index(new ListRequest, new DatatableBuilder());

        $this->assertEquals(200, $response->getStatusCode());
    }

    public function test_datatable_message()
    {
        $response = $this->index(new ListRequest, new DatatableBuilder());
        __('responder::messages.success_status.status');
        $this->assertArrayHasKey('message', $response->original, 'Datatable response has message');
        $this->assertContainsEquals('message', $response->original, 'Datatable response has message');
    }

    /**
     * @param Model|null $item
     * @return array|null
     */
    private function extraData(Model $item = null): ?array
    {
        return [];
    }

    /**
     * @return Model
     */
    private function model(): Model
    {
        return new TestModel();
    }

    /**
     * @return array
     */
    private function relations(): array
    {
        return [];
    }
}