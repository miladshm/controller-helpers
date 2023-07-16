<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\HasFilters;
use Miladshm\ControllerHelpers\Traits\HasModel;
use Miladshm\ControllerHelpers\Traits\HasValidation;

trait HasGetColumn
{
    use HasModel, HasFilters, HasValidation;

    /**
     * @param Request $request
     * @return JsonResponse
     * @throws ValidationException
     */
    public function getColumn(Request $request): JsonResponse
    {
        $this->getValidationData($request);
        $res = $this->model()
            ->query()
            ->select($request->collect('column')->toArray())
            ->when(true, function (Builder $builder) {
                return $this->filters($builder);
            })
            ->get();

        if ($request->collect('column')->count() == 1)
            $res = $res->map->{$request->string('column')->value()}->unique()->values()->toArray();

        else
            $res = $res
                ->map(function ($item) use ($request) {
                    return $item->only($request->collect('column')->toArray());
                })
                ->unique(fn($item) => implode($item))
                ->reject(function ($item) {
                    return array_search(null, $item);
                })
                ->values();
//
        return ResponderFacade::setData($res)->respond();
    }


    protected function rules(): array
    {
        return [
            'column' => ['required'],
            'column.*' => ['string']
        ];
    }

}
