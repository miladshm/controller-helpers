<?php

namespace Miladshm\ControllerHelpers\Tests\Unit;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Support\Facades\Config;
use Miladshm\ControllerHelpers\Http\Resources\TestModelResource;
use Miladshm\ControllerHelpers\Models\TestModel;
use Miladshm\ControllerHelpers\Tests\TestCase;
use Miladshm\ControllerHelpers\Traits\WithModel;

class WithApiResourceTest extends TestCase
{
    use WithModel;

    /**
     * @return JsonResource|null
     */
    public function getJsonResourceClass(): ?JsonResource
    {
        return new TestModelResource($this->model());
    }

    protected function model(): Model
    {
        return new TestModel();
    }

    public function test_should_return_null_when_resources_enabled_is_false()
    {
        Config::set('controller-helpers.resources.enabled', false);

        $result = $this->getApiResource();

        $this->assertNull($result);
    }

    public function test_should_return_result_of_getJsonResourceClass_when_not_null()
    {

        Config::set('controller-helpers.resources.enabled', true);


        $result = $this->getApiResource();

        $this->assertSame($result?->toJson(), (new TestModelResource($this->model()))->toJson());
    }


}
