<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;

trait HasMassStore
{
    use HasStore;

    abstract private function setInputName(): string;


    /**
     * Handles the mass storage of records in the database.
     *
     * @param Request $request The incoming request containing the data to be stored.
     * @return RedirectResponse|JsonResponse The response to be sent back to the client.
     * @throws ValidationException If the incoming request fails validation.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        // Validate the incoming request
        $request->validate($this->requestClass()->rules(), $this->requestClass()->messages());

        // Trigger any additional validation processing
        $this->requestClass()->passedValidation();

        // Begin a database transaction
        DB::beginTransaction();
        try {
            // Collect the input data using the specified input name
            $data = $request->collect($this->getInputName());

            // Initialize an empty collection to store created items
            $items = collect();
            foreach ($data as $datum) {
                // Create a new model instance with the input data
                $item = $this->model()->query()->create($datum);

                // Execute a callback after storing each item
                $this->storeCallback($request, $item);

                // Add the created item to the collection
                $items = $items->push($item);
            }

        } catch (\Exception $exception) {
            // Rollback the transaction in case of an error
            DB::rollBack();
            return ResponderFacade::setExceptionMessage($exception->getMessage())->respondError();
        }

        // Commit the transaction if no errors occurred
        DB::commit();

        // Check if the request expects a JSON response
        if ($request->expectsJson()) {
            if ($this->getApiResource()) {
                // Get the API resource class and return a collection response
                $resource = get_class($this->getApiResource());
                return ResponderFacade::setData(forward_static_call([$resource, 'collection'], $items)->toArray($request))->setMessage(Lang::get('responder::messages.success_store.status'))->respond();
            }
            // Return a plain JSON response with the created items
            return ResponderFacade::setData($items->toArray())->setMessage(Lang::get('responder::messages.success_store.status'))->respond();
        }

        // Redirect back with a success message if not expecting JSON
        return Redirect::back()->with(Lang::get('responder::messages.success_status'));
    }


    private function getInputName()
    {
        return $this->setInputName();
    }
}
