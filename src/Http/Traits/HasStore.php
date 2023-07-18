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
use Miladshm\ControllerHelpers\Traits\WithRequestClass;
use Miladshm\ControllerHelpers\Traits\WithValidation;

trait HasStore
{
    use WithModel, WithValidation, WithRequestClass;

    /**
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = $this
            ->setRules($this->requestClass()->rules())
            ->setMessages($this->requestClass()->messages())
            ->getValidationData($request);

        DB::beginTransaction();
        try {
            $this->prepareForStore($request);
            $item = $this->model()->query()->create($data);
            $this->storeCallback($request, $item);

        } catch (\Exception $exception) {
            DB::rollBack();
            return ResponderFacade::setExceptionMessage($exception->getMessage())->respondError();
        }
        DB::commit();
        if ($request->expectsJson())
            return ResponderFacade::setData($item->toArray())->setMessage(Lang::get('responder::messages.success_store.status'))->respond();
        return Redirect::back()->with(Lang::get('responder::messages.success_status'));
    }


    /**
     * @param Request $request
     * @param Model $item
     * @return void
     */
    protected function storeCallback(Request $request, Model $item): void
    {

    }

    protected function prepareForStore(Request &$request)
    {

    }


}
