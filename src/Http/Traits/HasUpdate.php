<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Exception;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithModel;
use Miladshm\ControllerHelpers\Traits\WithValidation;
use Symfony\Component\HttpKernel\Exception\HttpException;

trait HasUpdate
{
    use WithModel, WithValidation;

    /**
     * Updates a specific item in the database.
     *
     * @param Request $request The incoming request object.
     * @param int|string $id The unique identifier of the item to be updated.
     * @return RedirectResponse|JsonResponse The response to be sent back to the client.
     */
    public function update(Request $request, int|string $id): RedirectResponse|JsonResponse
    {
        $item = $this->getItem($id, withFilters: false);
        DB::beginTransaction();
        try {
            $this->prepareForUpdate($request, $item);
            $data = $this->getValidationData($request);
            $item->update($data);
            $this->updateCallback($request, $item);

            DB::commit();
            if ($request->expectsJson()) {
                if ($this->getApiResource()) {
                    $resource = get_class($this->getApiResource());
                    return ResponderFacade::setData((new $resource($item->load($this->relations())))->jsonSerialize())->setMessage(Lang::get('responder::messages.success_update.status'))->respond();
                }
                return ResponderFacade::setData($item->load($this->relations())->toArray())->setMessage(Lang::get('responder::messages.success_update.status'))->respond();
            }
            return Redirect::back()->with(Lang::get('responder::messages.success_update'));
        } catch (ValidationException|HttpException|AuthorizationException|ModelNotFoundException $exception) {
            DB::rollBack();
            throw $exception;
        } catch (Exception $exception) {
            DB::rollBack();
            return ResponderFacade::setMessage($exception->getMessage())->respondError();
        }
    }

    /**
     * A method that can be overridden to perform any necessary preparations before updating a model instance.
     * This method also allows you to modify the request object before it is validated.
     * And you can implement the authorization logic here.
     *
     * @param Request $request The incoming request object.
     * @param Model $item
     * @return void
     */
    protected function prepareForUpdate(Request &$request, Model $item): void
    {
        // Implement any preparation logic needed before updating the model
        // Modify the request object as necessary
        // Implement authorization logic here if needed
        // Example: $this->authorize('update', $item);
        // Example: $request->merge(['updated_at' => now()]);
    }

    protected function rules(): array
    {
        return [];
    }

    protected function messages(): array
    {
        $requestClass = $this->updateRequestClass() ?? $this->requestClass();

        return $requestClass->messages();
    }

    /**
     * A callback method that can be overridden to perform additional actions after a model instance is updated.
     * This method is called after the model has been updated.
     *
     * @param Request $request The incoming request object.
     * @param Model $item The updated model instance.
     * @return void
     */
    protected function updateCallback(Request $request, Model $item): void
    {
        // Implement any additional logic needed after updating the model
    }
}
