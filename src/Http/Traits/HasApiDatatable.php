<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Miladshm\ControllerHelpers\Helpers\DatatableBuilder;
use Miladshm\ControllerHelpers\Http\Requests\ApiListRequest;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;

trait HasApiDatatable
{
    use HasIndex;
    private function indexView(): View
    {

    }


    /**
     * Display a listing of the resource.
     *
     * @param ApiListRequest $request
     * @param DatatableBuilder $datatable
     * @return JsonResponse
     */
    public function index(ApiListRequest $request, DatatableBuilder $datatable): JsonResponse
    {
        $items = $datatable->setBuilder($this->getItems())
            ->setSearchable($this->setSearchable())
            ->setPageLength($this->setPageLength())
            ->setFields($this->setFields())
            ->grid($request);
        $filters = $request->only('q','searchable','sort','page','pageLength');
        $data = compact('items','filters') + $this->extraData();

        return ResponderFacade::setData($data)->respond();
    }



    protected function setSearchable(): ?array
    {
        return null;
    }

    protected function setPageLength(): ?int
    {
        return null;
    }
    protected function setFields(): ?array
    {
        return ['*'];
    }
}

