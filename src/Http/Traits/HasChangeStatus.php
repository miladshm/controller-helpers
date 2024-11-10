<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Response;
use Miladshm\ControllerHelpers\Traits\WithModel;

/**
 * This trait provides a method to change the status of a model item.
 *
 * @package Miladshm\ControllerHelpers\Http\Traits
 */
trait HasChangeStatus
{
    use WithModel;

    /**
     * Change the status of a model item.
     *
     * @param int $id The ID of the model item to change the status for.
     * @param string $statusColumn The name of the column in the database that holds the status. Default is 'status'.
     *
     * @return JsonResponse|RedirectResponse
     * @throws ModelNotFoundException
     */
    public function changeStatus(int $id, string $statusColumn = 'status')
    {
        // Find the model item by ID
        $item = $this->model()->query()->findOrFail($id);

        // Toggle the status
        $item->{$statusColumn} = !$item->{$statusColumn};

        // Save the change
        $item->save();

        // Return a JSON response if the request expects JSON
        if (Request::expectsJson())
            return Response::json(Lang::get('responder::messages.success_change_status'));

        // Otherwise, return a redirect response with a success message
        return Redirect::back()->with(Lang::get('responder::messages.success_change_status'));
    }

}
