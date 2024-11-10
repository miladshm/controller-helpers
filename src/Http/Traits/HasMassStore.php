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
     * @param Request $request
     * @return RedirectResponse|JsonResponse
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse|JsonResponse
    {
        $request->validate($this->requestClass()->rules(), $this->requestClass()->messages());

        $this->requestClass()->passedValidation();
        DB::beginTransaction();
        try {
            $data = $request->collect($this->getInputName());

            $items = collect();
            foreach ($data as $datum) {
                $item = $this->model()->query()->create($datum);
                $this->storeCallback($request, $item);
                $items = $items->push($item);
            }


        } catch (\Exception $exception) {
            DB::rollBack();
            return ResponderFacade::setExceptionMessage($exception->getMessage())->respondError();
        }
        DB::commit();
        if ($request->expectsJson()) {
            if ($this->getApiResource()) {
                $resource = get_class($this->getApiResource());
                return ResponderFacade::setData(forward_static_call([$resource, 'collection'], $items)->toArray($request))->setMessage(Lang::get('responder::messages.success_store.status'))->respond();
            }
            return ResponderFacade::setData($items->toArray())->setMessage(Lang::get('responder::messages.success_store.status'))->respond();
        }
        return Redirect::back()->with(Lang::get('responder::messages.success_status'));
    }


    private function getInputName()
    {
        return $this->setInputName();
    }
}
