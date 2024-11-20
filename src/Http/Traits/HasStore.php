<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\Model;
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

trait HasStore
{
    use WithModel, WithValidation;

    /**
     * Handles the creation of a new model instance.
     *
     * @param Request $request The incoming request.
     * @return RedirectResponse|JsonResponse The response to be sent back to the client.
     * @throws ValidationException If the request data fails validation.
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->prepareForStore($request);
            $data = $this->getValidationData($request);
            $item = $this->model()->query()->create($data);
            $this->storeCallback($request, $item);

        } catch (ValidationException|HttpException $exception) {
            throw $exception;
        } catch (\Exception $exception) {
            DB::rollBack();
            return ResponderFacade::setExceptionMessage($exception->getMessage())->setMessage($exception->getMessage())->respondError();
        }
        DB::commit();
        if ($request->expectsJson()) {
            if ($this->getApiResource()) {
                $resource = get_class($this->getApiResource());
                return ResponderFacade::setData((new $resource($item->fresh($this->relations())))->jsonSerialize())->setMessage(Lang::get('responder::messages.success_store.status'))->respond();
            }
            return ResponderFacade::setData($item->load($this->relations())->toArray())->setMessage(Lang::get('responder::messages.success_store.status'))->respond();
        }
        return Redirect::back()->with(Lang::get('responder::messages.success_status'));
    }


    /**
     * A callback method that can be overridden to perform additional actions after a new model instance is created.
     *
     * @param Request $request The incoming request.
     * @param Model $item The newly created model instance.
     * @return void
     */
    protected function storeCallback(Request $request, Model $item): void
    {

    }

    /**
     * A method that can be overridden to perform any necessary preparations before a new model instance is created.
     * This method also allows you to modify the request object before it is validated.
     * And you can implement the authorization logic here.
     *
     * @param Request $request The incoming request.
     * @return void
     */
    protected function prepareForStore(Request &$request)
    {

    }


}
