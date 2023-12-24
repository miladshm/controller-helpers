<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

trait HasCreate
{
    abstract private function createView(): View;
    abstract private function extraData(Model $item = null): ?array;


    public function create(): View|JsonResponse
    {
        if (request()->expectsJson())
            return \response()->json($this->extraData());
        return $this->createView()->with($this->extraData());
    }

}
