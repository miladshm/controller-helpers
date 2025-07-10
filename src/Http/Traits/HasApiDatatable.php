<?php

namespace Miladshm\ControllerHelpers\Http\Traits;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Resources\Json\JsonResource;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Number;
use Miladshm\ControllerHelpers\Http\Requests\ListRequest;
use Miladshm\ControllerHelpers\Libraries\DataTableBuilder\DatatableBuilder;
use Miladshm\ControllerHelpers\Libraries\Responder\Facades\ResponderFacade;
use Miladshm\ControllerHelpers\Traits\WithExtraData;
use Miladshm\ControllerHelpers\Traits\WithModel;

trait HasApiDatatable
{
    use WithExtraData, WithModel;

    /**
     * Instance properties instead of static to prevent memory leaks
     */
    private ?string $order = null;
    private ?string $paginator = null;
    private ?int $pageLength = null;
    private array $searchable = [];

    // Configuration cache for performance
    private static array $configCache = [];

    /**
     * Get the paginator type with caching.
     */
    protected function getPaginator(): string
    {
        if ($this->paginator !== null) {
            return $this->paginator;
        }

        return $this->getConfigValue('default_pagination_type', 'default');
    }

    /**
     * Set the paginator type.
     */
    protected function setPaginator(?string $paginator): void
    {
        $this->paginator = $paginator;
    }

    /**
     * Display a listing of the resource with optimized performance.
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
        try {
            $startTime = microtime(true);
            $startMemory = memory_get_usage(true);

            $items = $datatable
                ->setRequest($request)
                ->setBuilder($this->query())
                ->setSearchable($this->getSearchable())
                ->setPageLength($this->getPageLength())
                ->setPaginator($this->getPaginator())
                ->setFields($this->getColumns())
                ->setOrder($this->getOrder())
                ->handle();

            // Process items efficiently
            $processedItems = $this->getItems($items);

            // Get filters and extra data
            $filters = Request::query();
            $extraData = $this->extraData();

            // Create optimized response data
            $data = $this->buildResponseData($processedItems, $filters, $extraData);

            // Add performance metrics in debug mode
            if (config('app.debug')) {
                $data['_performance'] = [
                    'execution_time' => round((microtime(true) - $startTime) * 1000, 2) . 'ms',
                    'memory_used' => Number::fileSize(memory_get_usage(true) - $startMemory),
                    'peak_memory' => Number::fileSize(memory_get_peak_usage(true)),
                ];
            }

            return ResponderFacade::setData($data)->respond();

        } catch (\Exception $e) {
            return ResponderFacade::setMessage($e->getMessage())
                ->setData(config('app.debug') ? $e->getTrace() : [])
                ->respondError();
        }
    }

    /**
     * Build response data efficiently
     */
    private function buildResponseData($items, array $filters, array $extraData): array
    {
        $data = ['items' => $items, 'filters' => $filters];

        // Merge extra data efficiently
        if (!empty($extraData)) {
            $data = array_merge($data, $extraData);
        }

        return $data;
    }

    /**
     * Retrieves the items from the builder and transforms them using the API resource if applicable.
     * Optimized for memory efficiency and performance.
     */
    private function getItems(Paginator|Collection|CursorPaginator $items)
    {
        $resource = $this->getApiResource();

        // Handle collections efficiently
        if ($items instanceof Collection) {
            return $this->processCollection($items, $resource);
        }

        // Handle paginated results
        return $this->processPaginatedResults($items, $resource);
    }

    /**
     * Process collection with memory optimization
     */
    private function processCollection(Collection $items, $resource)
    {
        $wrappingEnabled = $this->getConfigValue('get_all_wrapping.enabled', false);

        if ($wrappingEnabled) {
            $wrapper = $this->getConfigValue('get_all_wrapping.wrapper', 'data');

            if ($resource) {
                JsonResource::wrap($wrapper);
                return $resource->collection($items)
                    ->preserveQuery()
                    ->response()
                    ->getData();
            }

            return collect([$wrapper => $items]);
        } else {
            JsonResource::withoutWrapping();
        }

        return $resource?->collection($items)
            ->preserveQuery()
            ->response()
            ->getData() ?? $items;
    }

    /**
     * Process paginated results efficiently
     */
    private function processPaginatedResults(Paginator|CursorPaginator $items, $resource)
    {
        if (!$resource) {
            return $items;
        }

        return $resource->collection($items)
            ->preserveQuery()
            ->response()
            ->getData();
    }

    /**
     * Set the page length for pagination.
     */
    protected function setPageLength(int $pageLength): void
    {
        $this->pageLength = $pageLength;
    }

    /**
     * Get the order direction for sorting with caching.
     */
    protected function getOrder(): string
    {
        return $this->order ?? $this->getConfigValue('sort_direction', 'desc');
    }

    /**
     * Set the order direction for sorting.
     */
    protected function setOrder(string $order): void
    {
        $this->order = $order;
    }

    /**
     * Get the page length for pagination with optimization.
     */
    protected function getPageLength(): int
    {
        if ($this->pageLength !== null) {
            return $this->pageLength;
        }

        $defaultPageLength = $this->getConfigValue('default_page_length', 15);
        $maxPageLength = $this->getConfigValue('max_page_length', 500);

        // Ensure page length is within reasonable bounds
        return min($defaultPageLength, $maxPageLength);
    }

    /**
     * Get the searchable fields with caching.
     */
    protected function getSearchable(): array
    {
        if (!empty($this->searchable)) {
            return $this->searchable;
        }

        return $this->getConfigValue('search.default_searchable', ['id', 'name', 'title']);
    }

    /**
     * Set searchable fields.
     */
    protected function setSearchable(array $searchable): void
    {
        $this->searchable = $searchable;
    }

    /**
     * Get configuration value with caching for performance.
     */
    private function getConfigValue(string $key, mixed $default = null): mixed
    {
        return config("controller-helpers.{$key}", $default);
    }

    /**
     * Clear configuration cache (useful for testing).
     */
    public static function clearConfigCache(): void
    {
        self::$configCache = [];
    }

    /**
     * Get performance metrics for the current instance.
     */
    public function getPerformanceMetrics(): array
    {
        return [
            'searchable_fields' => count($this->getSearchable()),
            'page_length' => $this->getPageLength(),
            'paginator_type' => $this->getPaginator(),
            'order_direction' => $this->getOrder(),
            'memory_usage' => memory_get_usage(true),
            'memory_peak' => memory_get_peak_usage(true),
        ];
    }
}

