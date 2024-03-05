<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithFilters;
use Miladshm\ControllerHelpers\Traits\WithModel;

trait HasGetCount
{
    use WithModel, WithFilters;

    /**
     * @param string|null $group_by
     * @return JsonResponse
     */
    public function getCount(?string $group_by = null): JsonResponse
    {
        if (isset($group_by)) {
            $count = $this->model()->query()
                ->select($group_by)
                ->selectSub('count(*)', 'count')
                ->when(true, function (Builder $builder) {
                    return $this->filters($builder);
                })
                ->groupBy($group_by)
                ->orderByDesc('count')
//                ->get()
                ->pluck('count', $group_by);
        } else
            $count = $this->model()
                ->query()
                ->when(true, function ($builder) {
                    return $this->filters($builder);
                })
                ->count();
        return ResponderFacade::setData(compact('count'))->respond();

    }
}
