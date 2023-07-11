<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Miladshm\ControllerHelpers\Helpers\DatatableBuilder;
use Miladshm\ControllerHelpers\Http\Requests\ListRequest;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\HasExtraData;
use Miladshm\ControllerHelpers\Traits\HasFilters;
use Miladshm\ControllerHelpers\Traits\HasModel;
use Miladshm\ControllerHelpers\Traits\HasRelations;

trait HasApiDatatable
{
    use HasExtraData, HasRelations, HasModel, HasFilters;

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
        $filters = Request::query();
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

    private function getItems(): Builder
    {
        return $this->model()->query()
            ->select($this->setColumns())
            ->when(count($this->relations()), function ($q) {
                $q->with($this->relations());
            })
            ->when(true, function (Builder $builder) {
                return $this->filters($builder);
            });
    }

    protected function setColumns(): array
    {
        return ['*'];
    }
}

