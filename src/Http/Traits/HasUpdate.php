<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Miladshm\ControllerHelpers\Exceptions\ApiValidationException;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

trait HasUpdate
{
    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse|JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, $id): RedirectResponse|JsonResponse
    {
        $requestClass = $this->updateRequestClass() ?? $this->requestClass();
        $data = Validator::make($request->all(),$requestClass->rules(),$requestClass->messages())
            ->setException(ApiValidationException::class)->validate();
        $item = $this->model()->query()->findOrFail($id);
        DB::beginTransaction();
        try {
            $item->update($data);
            $this->updateCallback($request, $item);
        } catch (\Exception $exception) {
            DB::rollBack();
            return ResponderFacade::setMessage($exception->getMessage())->respondError();
        }
        DB::commit();
        if ($request->expectsJson())
            return ResponderFacade::setData($item->toArray())->setMessage(trans('messages.success_update.status'))->respond();
        return redirect()->back()->with(trans('messages.success_update'));

    }

    protected function updateRequestClass(): ?FormRequest
    {
        return null;
    }

    abstract private function requestClass(): FormRequest;

    abstract private function model(): Model;

    protected function updateCallback(Request $request, Model $item)
    {

    }


}
