<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Config;
use Illuminate\Support\Facades\Schema;
use Illuminate\Validation\ValidationException;
use Miladshm\ControllerHelpers\Http\Requests\MarkRequest;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithModel;

trait HasMarkDate
{
    use WithModel;


    /**
     * Marks a record in the database by updating a specified field with the current date and time.
     *
     * @param MarkRequest $request The request object containing the date and time to be marked.
     * @param int $id The ID of the record to be marked.
     * @return JsonResponse A JSON response containing the updated record data.
     */
    public function mark(MarkRequest $request, int $id): JsonResponse
    {
        // Retrieve the record to be marked
        $item = $this->getItem($id);

        // Determine the field to be updated
        $field = $request->input('field') ?? $this->getMarkField();

        // Check if the specified field exists in the database
        if (!Schema::connection($this->model()->getConnectionName())->hasColumn($this->model()->getTable(), $field)) {
            throw ValidationException::withMessages([
                $field => trans('responder::messages.field_not_exists'),
            ]);
        }

        // Check if the field is already marked
        if ($item->{$field}) {
            throw ValidationException::withMessages([
                $field => trans('responder::messages.field_already_marked'),
            ]);
        }

        // Update the record with the provided date and time
        $item->update([
            $field => $request->date('time') ?? now(Config::get('app.timezone'))->toDateTimeString(),
        ]);

        // Return a JSON response containing the updated record data
        return ResponderFacade::setData($item)->respond();
    }


    /**
     * Returns the default field name to be used for marking records.
     *
     * @return string The default field name.
     */
    protected function getMarkField(): string
    {
        return 'changed_at';
    }

}
