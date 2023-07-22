<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Miladshm\ControllerHelpers\Helpers\DatatableBuilder;
use Miladshm\ControllerHelpers\Http\Requests\ListRequest;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithExtraData;
use Miladshm\ControllerHelpers\Traits\WithFilters;
use Miladshm\ControllerHelpers\Traits\WithModel;
use Miladshm\ControllerHelpers\Traits\WithRelations;

trait HasApiDatatable
{
    use WithExtraData, WithRelations, WithModel, WithFilters;

    private string $order = 'desc';

    /**
     * Display a listing of the resource.
     *
     * @param ListRequest $request
     * @param DatatableBuilder $datatable
     * @return JsonResponse
     */
    public function index(ListRequest $request, DatatableBuilder $datatable): JsonResponse
    {
        $items = $datatable
            ->setBuilder($this->getItems())
            ->setSearchable($this->setSearchable())
            ->setPageLength($this->setPageLength())
            ->setRequest($request)
            ->setOrder($this->getOrder())
            ->search()
            ->sortResult()
            ->paginate();

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


    /**
     * Order can be either 'asc' or 'desc', default value in 'desc'
     * @return string
     */
    protected function getOrder(): string
    {
        return $this->order;
    }
}

