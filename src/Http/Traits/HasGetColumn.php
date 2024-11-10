<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithModel;
use Miladshm\ControllerHelpers\Traits\WithValidation;

trait HasGetColumn
{
    use WithModel, WithValidation;

    /**
     * This method retrieves a given column (or columns) from the model's database table.
     * It uses the 'column' parameter from the request to determine which columns to retrieve.
     * If multiple columns are provided, the method will return a collection of arrays containing
     * the values of the specified columns. If only one column is provided, the method will return
     * a collection of the values of the specified column.
     *
     * @param Request $request The incoming request containing the 'column' parameter.
     * @return JsonResponse A JSON response containing the requested columns.
     * @throws ValidationException If the request does not meet the validation rules.
     */
    public function getColumn(Request $request): JsonResponse
    {
        // Set the validation rules for the 'column' parameter
        // The 'column' parameter must be an array of strings
        // The array must contain at least one string
        $this->setRules([
            'column' => ['required'],
            'column.*' => ['string']
        ])
            ->getValidationData($request);

        // Retrieve the requested columns from the database
        // Use the 'column' parameter to determine which columns to retrieve
        // If multiple columns are provided, the method will return a collection of arrays containing
        // the values of the specified columns. If only one column is provided, the method will return
        // a collection of the values of the specified column.
        // Use the 'filters' method to filter the results if necessary
        $res = $this->model()
            ->query()
            ->select($request->collect('column')->toArray())
            ->when(true, function (Builder $builder) {
                return $this->filters($builder);
            })
            ->get();

        // If only one column was requested, return a collection of the values of the specified column
        if ($request->collect('column')->count() == 1)
            $res = $res->map->{$request->string('column')->value()}->unique()->values()->toArray();

        // If multiple columns were requested, return a collection of arrays containing the values of the specified columns
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

        // Return the results as a JSON response
        return ResponderFacade::setData($res)->respond();
    }

}
