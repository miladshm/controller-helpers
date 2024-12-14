<?php

namespace Miladshm\ControllerHelpers\Http\Controllers;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\Resources\Json\JsonResource;
use Miladshm\ControllerHelpers\Http\Requests\StoreRequest;
use Miladshm\ControllerHelpers\Http\Resources\TestModelResource;
use Miladshm\ControllerHelpers\Http\Traits\HasApiDatatable;
use Miladshm\ControllerHelpers\Http\Traits\HasChangePosition;
use Miladshm\ControllerHelpers\Http\Traits\HasChangeStatus;
use Miladshm\ControllerHelpers\Http\Traits\HasDestroy;
use Miladshm\ControllerHelpers\Http\Traits\HasGetCount;
use Miladshm\ControllerHelpers\Http\Traits\HasGetSum;
use Miladshm\ControllerHelpers\Http\Traits\HasShow;
use Miladshm\ControllerHelpers\Http\Traits\HasStore;
use Miladshm\ControllerHelpers\Http\Traits\HasUpdate;
use Miladshm\ControllerHelpers\Models\TestModel;

class TestController extends Controller
{
    use HasStore, HasApiDatatable, HasUpdate, HasDestroy, HasShow, HasChangePosition, HasChangeStatus, HasGetCount, HasGetSum;

    /**
     * @return Model
     */
    private function model(): Model
    {
        return new TestModel;
    }

    /**
     * @return FormRequest
     */
    private function requestClass(): FormRequest
    {
        return new StoreRequest;
    }

    public function getJsonResourceClass(): ?JsonResource
    {
        return new TestModelResource($this->model());
    }

    protected function relations(): array
    {
        return [];
    }

    protected function filters(Builder $builder): null|Builder
    {
        return $builder;
    }
}
