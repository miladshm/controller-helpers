<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Request;
use Miladshm\ControllerHelpers\Helpers\DatatableBuilder;
use Miladshm\ControllerHelpers\Http\Requests\ListRequest;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithExtraData;
use Miladshm\ControllerHelpers\Traits\WithModel;

trait HasApiDatatable
{
    use WithExtraData, WithModel;

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
     * Get the paginator type.
     *
     * @return string|null The paginator type or null if not set.
     */
    protected function getPaginator(): ?string
    {
        return self::$paginator ?? getConfigNames('default_pagination_type');
    }

    /**
     * Set the paginator type.
     *
     * @param string|null $paginator The paginator type to set.
     * @return void
     */
    protected function setPaginator(?string $paginator): void
    {
        self::$paginator = $paginator;
    }

    /**
     * Display a listing of the resource.
     *
     * This method handles the index action for API requests. It processes the request,
     * applies filters, sorting, and pagination to retrieve a list of items.
     *
     * @param ListRequest $request The incoming list request containing query parameters.
     * @param DatatableBuilder $datatable The datatable builder instance for constructing the query.
     * @return JsonResponse A JSON response containing the paginated items, applied filters, and any extra data.
     */
    public function index(ListRequest $request, DatatableBuilder $datatable): JsonResponse
    {
        $items = $datatable
            ->setRequest($request) // Set the request for the builder
            ->setBuilder($this->query()) // Set the builder to the getItems method
            ->setSearchable($this->getSearchable()) // Set the searchable fields
            ->setPageLength($this->getPageLength()) // Set the page length for pagination
            ->setPaginator($this->getPaginator()) // Set the paginator type
            ->setFields($this->getColumns()) // Set the fields to retrieve
            ->setOrder($this->getOrder()) // Set the order direction
            ->search() // Apply search filters
            ->sortResult() // Apply sorting
            ->paginate(); // Paginate the results

        $filters = Request::query(); // Get the applied filters
        $resource = $this->getApiResource(); // Get the API resource instance
        $items = $resource?->collection($items)->toResponse($request)->getData() ?? $items; // Transform the items using the API resource

        $data = compact('items', 'filters') + $this->extraData(); // Get any extra data

        return ResponderFacade::setData($data)->respond(); // Return the JSON response
    }

    /**
     * Set the page length for pagination.
     *
     * @param int $pageLength The number of items per page.
     * @return void
     */
    protected function setPageLength(int $pageLength): void
    {
        static::$pageLength = $pageLength;
    }

    /**
     * Get the order direction for sorting.
     *
     * @return string The order direction ('asc' or 'desc').
     */
    protected function getOrder(): string
    {
        return static::$order ?? getConfigNames('sort_direction');
    }

    /**
     * Get the page length for pagination.
     *
     * @return int The number of items per page.
     */
    protected function getPageLength(): int
    {
        return static::$pageLength ?? getConfigNames('default_page_length');
    }

    /**
     * Get the searchable fields.
     *
     * @return array An array of searchable field names.
     */
    protected function getSearchable(): array
    {
        return static::$searchable ?? getConfigNames('search.default_searchable');
    }
}

