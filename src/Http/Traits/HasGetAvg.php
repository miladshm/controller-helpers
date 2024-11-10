<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Schema;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithModel;

trait HasGetAvg
{
    use WithModel;

    /**
     * Retrieves the average value of a specified column from the model's table.
     *
     * @param string $column The name of the column for which to calculate the average.
     * @return JsonResponse A JSON response containing the average value of the specified column.
     *                      If the column does not exist in the table, the average will be 0.
     */
    public function getAvg(string $column): JsonResponse
    {
        // Check if the column exists in the model's table
        $columnExists = Schema::connection($this->model()->getConnectionName())->hasColumn(
            $this->model()->getTable(),
            $column
        );

        // If the column exists, calculate the average
        $avg = $columnExists
            ? $this
                ->query() // Use the query builder instance
                ->avg($column) // Use the avg() method to calculate the average
            : 0; // The column does not exist, average will be 0

        // Return the average in a JSON response
        return ResponderFacade::setData(compact('avg'))->respond();
    }
}
