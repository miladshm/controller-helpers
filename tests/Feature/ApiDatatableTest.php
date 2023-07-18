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
        $this->assertContainsEquals(200, $response->getData(true), 'Datatable response status is ok');
        $this->assertEquals(200, $response->getData(true)[getConfigNames('response.field_names.code')], 'status is 200');
    }

    public function test_datatable_message()
    {
        $response = $this->index(new ListRequest, new DatatableBuilder());
        $this->assertArrayHasKey(getConfigNames('response.field_names.message'), $response->getData(true), 'Datatable response has message');
        $this->assertContainsEquals(__('responder::messages.success_status.status'), $response->getData(true), 'Datatable response message is correct');
    }

    public function test_datatable_data()
    {
        $response = $this->index(new ListRequest, new DatatableBuilder());

        $this->assertArrayHasKey(getConfigNames('response.field_names.data'), $response->getData(true), 'Datatable response has data');
        $this->assertIsArray($response->getData(true)[getConfigNames('response.field_names.data')], 'Datatable response has data');
        $this->assertArrayHasKey('items', $response->getData(true)[getConfigNames('response.field_names.data')], 'Datatable data has items.');
        $this->assertArrayHasKey('data', $response->getData(true)[getConfigNames('response.field_names.data')]['items'], 'Datatable data.items has data');
        $this->assertIsArray($response->getData(true)[getConfigNames('response.field_names.data')]['items']['data'], 'Datatable data.items.data is array');

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