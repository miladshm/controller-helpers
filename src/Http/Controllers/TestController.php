<?php

namespace Miladshm\ControllerHelpers\Http\Controllers;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Miladshm\ControllerHelpers\Http\Requests\StoreRequest;
use Miladshm\ControllerHelpers\Http\Traits\HasApiDatatable;
use Miladshm\ControllerHelpers\Http\Traits\HasDestroy;
use Miladshm\ControllerHelpers\Http\Traits\HasShow;
use Miladshm\ControllerHelpers\Http\Traits\HasStore;
use Miladshm\ControllerHelpers\Http\Traits\HasUpdate;
use Miladshm\ControllerHelpers\TestModel;

class TestController extends Controller
{
    use HasStore, HasApiDatatable, HasUpdate, HasDestroy, HasShow;

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

    /**
     * @param Model|null $item
     * @return array|null
     */
    private function extraData(Model $item = null): ?array
    {
        return [];
    }

    /**
     * @return array
     */
    private function relations(): array
    {
        return [];
    }
}