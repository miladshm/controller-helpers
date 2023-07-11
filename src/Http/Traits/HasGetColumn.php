<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\HasFilters;
use Miladshm\ControllerHelpers\Traits\HasModel;

trait HasGetColumn
{
    use HasModel, HasFilters;

    /**
     * @param string $col
     * @return JsonResponse
     */
    public function getColumn(string $col): JsonResponse
    {
        $res = $this->model()
            ->query()
            ->select($col)
            ->when(true, function (Builder $builder) {
                return $this->filters($builder);
            })
            ->get()->map->{$col}->unique()->toArray();
        return ResponderFacade::setData($res)->respond();
    }

}
