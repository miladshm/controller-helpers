<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Contracts\View\View;
use Illuminate\Http\JsonResponse;

trait HasModalShow
{
    use HasViewShow;

    /**
     * @param $id
     * @return JsonResponse
     */
    public function show($id): JsonResponse
    {
        $item = $this->getModel($id);

        $html = $this->showView()->with(compact('item'))->render();

        return response()->json(compact('html'));
    }
}
