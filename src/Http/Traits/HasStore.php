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
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\HasModel;

trait HasStore
{
    use HasModel;

    abstract private function requestClass(): FormRequest;


    /**
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $data = Validator::make($request->all(), $this->requestClass()->rules(), $this->requestClass()->messages())
            ->validate();
        $this->requestClass()->passedValidation();
        DB::beginTransaction();
        try {
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


    protected function storeCallback(Request $request,Model $item)
    {

    }


}
