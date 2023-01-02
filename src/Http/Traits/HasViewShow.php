<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

trait HasViewShow
{
    use HasShow;

    abstract private function showView(): View;

    /**
     * @param $id
     * @return View|JsonResponse
     */
    public function show($id): View|JsonResponse
    {
        $item = $this->getModel($id);
        if (request()->expectsJson())
            return \response()->json(compact('item'));
        return $this->showView()->with(compact('item'));
    }
}
