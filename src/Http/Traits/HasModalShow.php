<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Http\JsonResponse;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;

trait HasModalShow
{
    use HasViewShow;

    /**
     * @param $id
     * @return JsonResponse
     * @throws \Throwable
     */
    public function show($id): JsonResponse
    {
        $item = $this->getModel($id);

        $html = $this->showView()->with(compact('item'))->render();

        return ResponderFacade::setData(compact('html'))->respond();
    }
}
