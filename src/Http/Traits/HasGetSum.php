<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithModel;
use Miladshm\ControllerHelpers\Traits\WithValidation;

trait HasGetSum
{
    use WithModel, WithValidation;

    /**
     * @param string $column
     * @param Request $request
     * @return JsonResponse
     */
    public function getSum(Request $request, string $column): JsonResponse
    {
        $data = $this->setRules([
            'group_by' => ['nullable']
        ])
            ->getValidationData($request);

        $sum = 0;
        if (Schema::connection($this->model()->getConnectionName())->hasColumn($this->model()->getTable(), $column)) {
            $sum = $this->model()
                ->query()
                ->when(true, function (Builder $builder) {
                    return $this->filters($builder);
                })
                ->when($request->filled('group_by'), function (Builder $builder) use ($column, $request) {
                    $builder
                        ->select($request->input('group_by'))
                        ->selectSub("sum({$column})", 'sum')
                        ->groupBy($request->input('group_by'))
                        ->orderByDesc('sum')
                        ->pluck('sum', $request->input('group_by'));
                });
            if ($request->filled('group_by'))
                $sum = $sum->pluck('sum', $request->input('group_by'));
            else
                $sum = $sum->sum($column);

        }
        return ResponderFacade::setData(compact('sum'))->respond();

    }
}
