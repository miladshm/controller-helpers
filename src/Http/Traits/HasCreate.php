<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

/**
 * This trait provides a method for handling the creation of a new resource.
 * It checks the request type and returns either a JSON response or a view with extra data.
 */
trait HasCreate
{
    /**
     * Abstract method to return the view for creating a new resource.
     *
     * @return View The view for creating a new resource.
     */
    abstract private function createView(): View;

    /**
     * Abstract method to return extra data for creating a new resource.
     *
     * @param Model|null $item The model item for which extra data is required.
     * @return array|null The extra data for creating a new resource or null if no extra data is required.
     */
    abstract private function extraData(Model $item = null): ?array;


    /**
     * Handles the creation of a new resource.
     *
     * @return View|JsonResponse Returns a view if the request expects HTML, otherwise returns a JSON response.
     */
    public function create(): View|JsonResponse
    {
        if (request()->expectsJson())
            return \response()->json($this->extraData());
        return $this->createView()->with($this->extraData());
    }

}
