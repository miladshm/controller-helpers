<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithFilters;
use Miladshm\ControllerHelpers\Traits\WithModel;

trait HasGetCount
{
    use WithModel, WithFilters;

    /**
     * @param Request $request
     * @param string|null $group_by
     * @return JsonResponse
     */
    public function getCount(Request $request, ?string $group_by = null): JsonResponse
    {
        if (isset($group_by)) {
            $request->validate(['sort' => 'in:asc,desc']);
            $count = $this->model()->query()
                ->select($group_by)
                ->selectSub('count(*)', 'count')
                ->when(true, function (Builder $builder) {
                    return $this->filters($builder);
                })
                ->groupBy($group_by)
                ->orderBy('count', $request->sort ?? 'desc')
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
