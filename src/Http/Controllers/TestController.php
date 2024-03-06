<?php

namespace Miladshm\ControllerHelpers\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Miladshm\ControllerHelpers\Http\Requests\StoreRequest;
use Miladshm\ControllerHelpers\Http\Traits\HasApiDatatable;
use Miladshm\ControllerHelpers\Http\Traits\HasChangePosition;
use Miladshm\ControllerHelpers\Http\Traits\HasChangeStatus;
use Miladshm\ControllerHelpers\Http\Traits\HasDestroy;
use Miladshm\ControllerHelpers\Http\Traits\HasGetCount;
use Miladshm\ControllerHelpers\Http\Traits\HasGetSum;
use Miladshm\ControllerHelpers\Http\Traits\HasShow;
use Miladshm\ControllerHelpers\Http\Traits\HasStore;
use Miladshm\ControllerHelpers\Http\Traits\HasUpdate;
use Miladshm\ControllerHelpers\TestModel;

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
}
