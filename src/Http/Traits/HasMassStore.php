<?php

namespace App\Http\Traits;

use App\Libraries\Responder\Facades\ResponderFacade;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\ValidationException;

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
        if ($request->expectsJson())
            return ResponderFacade::setData($items->toArray())->setMessage(trans('messages.success_store.status'))->respond();
        return redirect()->back()->with(trans('messages.success_status'));
    }


    private function getInputName()
    {
        return $this->setInputName();
    }
}
