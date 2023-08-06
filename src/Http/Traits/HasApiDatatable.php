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

    /**
     * @var string|null
     */
    private static ?string $order;

    /**
     * @var string|null
     */
    private static ?string $paginator;

    /**
     * @var int|null
     */
    private static ?int $pageLength;

    /**
     * @var array|string[]
     */
    private static array $searchable = [];

    /**
     * @var array|string[]
     */
    private static array $columns = ['*'];

    /**
     * @return string|null
     */
    protected function getPaginator(): ?string
    {
        return self::$paginator ?? getConfigNames('default_pagination_type');
    }

    /**
     * @param string|null $paginator
     */
    protected function setPaginator(?string $paginator): void
    {
        self::$paginator = $paginator;
    }

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
            ->setRequest($request)
            ->setBuilder($this->getItems())
            ->setSearchable($this->getSearchable())
            ->setPageLength($this->getPageLength())
            ->setPaginator($this->getPaginator())
            ->setFields($this->getColumns())
            ->setOrder($this->getOrder())
            ->search()
            ->sortResult()
            ->paginate();

        $filters = Request::query();
        $data = compact('items', 'filters') + $this->extraData();

        return ResponderFacade::setData($data)->respond();
    }

    protected function setPageLength(int $pageLength): void
    {
        static::$pageLength = $pageLength;
    }

    private function getItems(): Builder
    {
        return $this->model()->query()
            ->select($this->getColumns())
            ->when(count($this->relations()), function ($q) {
                $q->with($this->relations());
            })
            ->when(true, function (Builder $builder) {
                return $this->filters($builder);
            });
    }

    /**
     * Order can be either 'asc' or 'desc', default value in 'desc'
     * @return string
     */
    protected function getOrder(): string
    {
        return static::$order ?? getConfigNames('sort_direction');
    }

    /**
     * @return int
     */
    protected function getPageLength(): int
    {
        return static::$pageLength ?? getConfigNames('default_page_length');
    }

    /**
     * @return array
     */
    protected function getSearchable(): array
    {
        return static::$searchable ?? getConfigNames('search.default_searchable');
    }

    /**
     * @return array|string[]
     */
    public function getColumns(): array
    {
        return static::$columns;
    }
}

