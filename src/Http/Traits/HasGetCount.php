<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\HasFilters;
use Miladshm\ControllerHelpers\Traits\HasModel;

trait HasGetCount
{
    use HasModel, HasFilters;

    /**
     * @param string|null $group_by
     * @return JsonResponse
     */
    public function getCount(?string $group_by = null): JsonResponse
    {
        if (isset($group_by)) {

            $count = DB::table($this->model()->getTable())
                ->select($group_by, DB::raw('count(*) as count'))
                ->groupBy($group_by)
                ->get();
        } else
            $count = $this->model()
                ->query()
                ->when(true, function (Builder $builder) {
                    return $this->filters($builder);
                })
                ->count();
        return ResponderFacade::setData(compact('count'))->respond();

    }
}
