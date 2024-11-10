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
     * Retrieves the sum of a specified column in the database based on the provided filters.
     * If the `group_by` argument is provided, the result is grouped by the provided column.
     * @param Request $request The incoming request containing the column and value to check.
     * @param string $column The column to retrieve the sum of.
     * @return JsonResponse The response containing the sum of the records.
     */
    public function getSum(Request $request, string $column): JsonResponse
    {
        $data = $this->setRules([
            'group_by' => ['nullable']
        ])
            ->getValidationData($request);

        // Initialize the sum
        $sum = 0;
        // Check if the column exists in the database
        if (Schema::connection($this->model()->getConnectionName())->hasColumn($this->model()->getTable(), $column)) {
            // Build the query
            $sum = $this->model()
                ->query()
                ->when(true, function (Builder $builder) {
                    return $this->filters($builder);
                })
                ->when($request->filled('group_by'), function (Builder $builder) use ($column, $request) {
                    // Group the result by the provided column
                    $builder
                        ->select($request->input('group_by'))
                        ->selectSub("sum({$column})", 'sum')
                        ->groupBy($request->input('group_by'))
                        // Sort the result by the sum
                        ->orderByDesc('sum')
                        // Retrieve the result
                        ->pluck('sum', $request->input('group_by'));
                });
            // If the group by argument is provided
            if ($request->filled('group_by')) {
                // Pluck the sum
                $sum = $sum->pluck('sum', $request->input('group_by'));
            } else {
                // Retrieve the sum
                $sum = $sum->sum($column);
            }

        }
        // Return the response
        return ResponderFacade::setData(compact('sum'))->respond();

    }
}
