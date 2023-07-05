<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Miladshm\ControllerHelpers\Helpers\DatatableBuilder;
use Miladshm\ControllerHelpers\Http\Requests\ListRequest;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

trait HasApiDatatable
{
    use HasIndex;

    /**
     * Display a listing of the resource.
     *
     * @param ListRequest $request
     * @param DatatableBuilder $datatable
     * @return JsonResponse
     */
    public function index(ListRequest $request, DatatableBuilder $datatable): JsonResponse
    {
        $items = $datatable->setBuilder($this->getItems())
            ->setSearchable($this->setSearchable())
            ->setPageLength($this->setPageLength())
            ->grid($request);
        $filters = $request->query();
        $data = compact('items', 'filters') + $this->extraData();

        return ResponderFacade::setData($data)->respond();
    }

    protected function setPageLength(): ?int
    {
        return null;
    }

    protected function setSearchable(): ?array
    {
        return null;
    }

    private function indexView(): View
    {

    }
}

