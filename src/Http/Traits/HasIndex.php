<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Illuminate\View\View;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithExtraData;
use Miladshm\ControllerHelpers\Traits\WithModel;

trait HasIndex
{
    use WithModel, WithExtraData;

    abstract private function indexView(): View;

    /**
     * Retrieves and returns items either as a JSON response or a view.
     *
     * @return View|JsonResponse The response containing the items and extra data.
     */
    public function index(): View|JsonResponse
    {
        // Fetch items using the query builder
        $items = $this->query()->get();

        // Check if the request expects a JSON response
        if (Request::expectsJson()) {
            // If an API resource is available, transform items using it
            if ($this->getApiResource()) {
                $resource = get_class($this->getApiResource());
                $items = forward_static_call([$resource, 'collection'], $items)->toArray(\request());
            }
            // Return a JSON response with items and extra data
            return ResponderFacade::setData(compact('items') + $this->extraData())->respond();
        }

        // Return a view with items and extra data
        return $this->indexView()->with(compact('items') + $this->extraData());
    }
}
