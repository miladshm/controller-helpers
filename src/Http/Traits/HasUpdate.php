<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

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
use Miladshm\ControllerHelpers\Traits\HasModel;
use Miladshm\ControllerHelpers\Traits\HasValidation;

trait HasUpdate
{
    use HasModel, HasValidation;

    /**
     * @param Request $request
     * @param $id
     * @return RedirectResponse|JsonResponse
     * @throws ValidationException
     */
    public function update(Request $request, $id): RedirectResponse|JsonResponse
    {
        $data = $this->getValidationData($request);
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
            return ResponderFacade::setData($item->toArray())->setMessage(Lang::get('responder::messages.success_update.status'))->respond();
        return Redirect::back()->with(Lang::get('responder::messages.success_update'));

    }

    protected function updateCallback(Request $request, Model $item)
    {

    }


}
