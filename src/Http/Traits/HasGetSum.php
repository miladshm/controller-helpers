<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\HasFilters;
use Miladshm\ControllerHelpers\Traits\HasModel;

trait HasGetSum
{
    use HasModel, HasFilters;

    /**
     * @param string $column
     * @return JsonResponse
     */
    public function getSum(string $column): JsonResponse
    {
        $sum = Schema::connection($this->model()->getConnectionName())->hasColumn($this->model()->getTable(), $column)
            ? $this->model()
                ->query()
                ->when(true, function (Builder $builder) {
                    return $this->filters($builder);
                })
                ->sum($column)
            : 0;
        return ResponderFacade::setData(compact('sum'))->respond();

    }
}
