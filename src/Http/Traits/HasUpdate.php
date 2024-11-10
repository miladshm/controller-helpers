<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Exception;
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

trait HasUpdate
{
    use WithModel, WithValidation;

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse|JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, $id): RedirectResponse|JsonResponse
    {
        DB::beginTransaction();
        try {
            $this->prepareForUpdate($request);
            $data = $this->getValidationData($request);
            $item = $this->model()->query()->findOrFail($id);
            $item->update($data);
            $this->updateCallback($request, $item);
        } catch (ValidationException|HttpException $exception) {
            throw $exception;
        } catch (Exception $exception) {
            DB::rollBack();
            return ResponderFacade::setMessage($exception->getMessage())->respondError();
        }
        DB::commit();
        if ($request->expectsJson()) {
            if ($this->getApiResource()) {
                $resource = get_class($this->getApiResource());
                return ResponderFacade::setData(forward_static_call([$resource, 'make'], $item)->toArray($request))->setMessage(Lang::get('responder::messages.success_update.status'))->respond();
            }
            return ResponderFacade::setData($item->load($this->relations())->toArray())->setMessage(Lang::get('responder::messages.success_update.status'))->respond();
        }
        return Redirect::back()->with(Lang::get('responder::messages.success_update'));

    }

    protected function prepareForUpdate(Request &$request)
    {
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

    protected function updateCallback(Request $request, Model $item)
    {

    }

}
