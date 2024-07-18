<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Exception;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Validation\ValidationException;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithModel;
use Miladshm\ControllerHelpers\Traits\WithRelations;
use Miladshm\ControllerHelpers\Traits\WithRequestClass;
use Miladshm\ControllerHelpers\Traits\WithValidation;

trait HasUpdate
{
    use WithModel, WithValidation, WithRequestClass, WithRelations;

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse|JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, $id): RedirectResponse|JsonResponse
    {
        $requestClass = $this->updateRequestClass() ?? $this->requestClass();

        DB::beginTransaction();
        try {
            $request = $this->prepareForUpdate($request);
            $data = $this->setRules($requestClass->rules())->setMessages($requestClass->messages())->getValidationData($request);
            $item = $this->model()->query()->findOrFail($id);
            $item->update($data);
            $this->updateCallback($request, $item);
        } catch (ValidationException $exception) {
            throw $exception;
        } catch (Exception $exception) {
            DB::rollBack();
            return ResponderFacade::setMessage($exception->getMessage())->respondError();
        }
        DB::commit();
        if ($request->expectsJson())
            return ResponderFacade::setData($item->load($this->relations())->toArray())->setMessage(Lang::get('responder::messages.success_update.status'))->respond();
        return Redirect::back()->with(Lang::get('responder::messages.success_update'));

    }

    protected function updateRequestClass(): ?FormRequest
    {
        return null;
    }

    protected function prepareForUpdate(Request $request): Request
    {
        return $request;
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
