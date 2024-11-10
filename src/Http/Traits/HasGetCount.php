<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithModel;
use Miladshm\ControllerHelpers\Traits\WithValidation;

trait HasGetCount
{
    use WithModel, WithValidation;

    /**
     * Retrieves the count of the records in the database based on the provided filters.
     * If the `$group_by` argument is provided, the result is grouped by the provided column.
     * @param Request $request The incoming request containing the column and value to check.
     * @param string|null $group_by The column to group the result by.
     * @return JsonResponse The response containing the count of the records.
     */
    public function getCount(Request $request, ?string $group_by = null): JsonResponse
    {
        // Validate the sort parameter
        if (isset($group_by)) {
            $this->setRules(['sort' => 'in:asc,desc'])->getValidationData($request);
        }

        // Build the query
        $query = $this->model()->query()
            ->when(true, function (Builder $builder) {
                return $this->filters($builder);
            });

        // If the group by argument is provided
        if (isset($group_by)) {
            // Group the result by the provided column
            $count = $query->select($group_by)
                ->selectSub('count(*)', 'count')
                ->groupBy($group_by)
                // Sort the result by the count
                ->orderBy('count', $request->sort ?? 'desc')
                // Retrieve the result
                ->pluck('count', $group_by);
        } else {
            // Retrieve the count of the records
            $count = $query->count();
        }

        // Return the response
        return ResponderFacade::setData(compact('count'))->respond();
    }
}
