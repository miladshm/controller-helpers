<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithModel;

trait HasGetAvg
{
    use WithModel;

    /**
     * @param string $column
     * @return JsonResponse
     */
    public function getAvg(string $column): JsonResponse
    {
        $avg = Schema::connection($this->model()->getConnectionName())->hasColumn($this->model()->getTable(), $column)
            ? $this->model()
                ->query()
                ->when(true, function (Builder $builder) {
                    return $this->filters($builder);
                })
                ->avg($column)
            : 0;
        return ResponderFacade::setData(compact('avg'))->respond();

    }
}
