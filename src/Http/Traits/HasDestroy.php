<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Lang;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Request;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithModel;

trait HasDestroy
{
    use WithModel;


    /**
     * @param $id
     * @return JsonResponse|RedirectResponse
     * @throws \Throwable
     */
    public function destroy($id): JsonResponse|RedirectResponse
    {
        $item = $this->getItem($id, true);

        $this->prepareForDestroy($item);

        if ($item->deleted_at)
            $item->forceDelete();
        else
            $item->deleteOrFail();

        if (Request::expectsJson())
            return ResponderFacade::setMessage(Lang::get('responder::messages.success_delete.status'))->respond();
        return Redirect::back()->with(Lang::get('responder::messages.success_delete'));
    }

    protected function prepareForDestroy(Model $item): void
    {

    }
}
