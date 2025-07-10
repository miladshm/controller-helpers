<?php

namespace Miladshm\ControllerHelpers\Libraries\DataTableBuilder;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Pipeline;
use Illuminate\Support\Traits\Conditionable;
use Miladshm\ControllerHelpers\Http\Requests\ListRequest;
use Miladshm\ControllerHelpers\Libraries\DataTableBuilder\Filters\SearchFilter;
use Miladshm\ControllerHelpers\Libraries\DataTableBuilder\Filters\SortFilter;

class DatatableBuilder
{
    use Conditionable;

    public Builder $builder;
    private ListRequest|FormRequest $request;
    private ?array $fields;
    private ?int $pageLength;
    private ?string $order;
    private ?array $searchable;
    private ?string $paginator;

    // Performance optimization: cache frequently accessed values
    private static array $configCache = [];
    private ?array $resolvedSearchable = null;
    private ?int $resolvedPageLength = null;
    private ?string $resolvedPaginator = null;

    /**
     * Handle the datatable building process with optimized performance
     */
    public function handle()
    {
        $pipes = [
            new SearchFilter($this->request, $this->getSearchable()),
            new SortFilter($this->request, $this->getOrder()),
        ];

        return Pipeline::send($this->builder)
            ->through($pipes)
            ->then(function ($builder) {
                return $this->request->boolean('all')
                    ? $this->handleGetAll($builder)
                    : $this->handlePagination($builder);
            });
    }

    /**
     * Handle "get all" requests with memory optimization
     */
    private function handleGetAll(Builder $builder): Collection
    {
        // For large datasets, add memory-efficient handling
        $maxRecords = $this->getConfigValue('max_records_without_pagination', 10000);

        // Use query chunking for very large datasets
        if ($builder->count() > $maxRecords) {
            $results = collect();
            $builder->chunk(1000, function ($records) use (&$results) {
                $results = $results->merge($records);
            });
            return $results;
        }

        return $builder->get();
    }

    /**
     * Handle pagination requests with query optimization
     */
    private function handlePagination(Builder $builder): Paginator|CursorPaginator
    {
        $pageLength = $this->getPageLength();
        $method = $this->getPaginatorMethodName();

        return $builder
            ->{$method}($pageLength)
            ->withQueryString();
    }

    /**
     * Gets the searchable fields with caching and optimization
     */
    public function getSearchable(): array
    {
        if ($this->resolvedSearchable !== null) {
            return $this->resolvedSearchable;
        }

        $searchableParam = $this->getConfigValue('params.searchable_columns');
        $this->resolvedSearchable = $this->request->{$searchableParam}
            ?? $this->searchable
            ?? $this->getConfigValue('search.default_searchable', []);

        return $this->resolvedSearchable;
    }

    /**
     * @param array|string[]|null $searchable
     * @return DatatableBuilder
     */
    public function setSearchable(?array $searchable): DatatableBuilder
    {
        $this->searchable = $searchable;
        $this->resolvedSearchable = null; // Clear cache
        return $this;
    }

    /**
     * Get the order direction for sorting with caching
     */
    public function getOrder(): string
    {
        return $this->order ?? $this->getConfigValue('sort_direction', 'desc');
    }

    /**
     * Sets the order direction for sorting.
     */
    public function setOrder(?string $order): DatatableBuilder
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Returns the paginator method name based on the paginator type.
     */
    private function getPaginatorMethodName(): string
    {
        return match ($this->getPaginator()) {
            'simple' => 'simplePaginate',
            'cursor' => 'cursorPaginate',
            default => 'paginate',
        };
    }

    /**
     * Returns the paginator type with caching
     */
    public function getPaginator(): string
    {
        if ($this->resolvedPaginator !== null) {
            return $this->resolvedPaginator;
        }

        $this->resolvedPaginator = $this->paginator ?? $this->getConfigValue('default_pagination_type', 'default');
        return $this->resolvedPaginator;
    }

    /**
     * Sets the paginator type.
     */
    public function setPaginator(?string $paginator = 'default'): DatatableBuilder
    {
        $this->paginator = $paginator;
        $this->resolvedPaginator = null; // Clear cache
        return $this;
    }

    /**
     * Returns the page length with caching and optimization
     */
    public function getPageLength(): int
    {
        if ($this->resolvedPageLength !== null) {
            return $this->resolvedPageLength;
        }

        $pageLengthParam = $this->getConfigValue('params.page_length');
        $this->resolvedPageLength = $this->request->{$pageLengthParam}
            ?? $this->pageLength
            ?? $this->getConfigValue('default_page_length', 15);

        // Add reasonable limits to prevent memory issues
        $maxPageLength = $this->getConfigValue('max_page_length', 500);
        if ($this->resolvedPageLength > $maxPageLength) {
            $this->resolvedPageLength = $maxPageLength;
        }

        return $this->resolvedPageLength;
    }

    /**
     * Sets the page length for pagination.
     */
    public function setPageLength(?int $pageLength): DatatableBuilder
    {
        $this->pageLength = $pageLength;
        $this->resolvedPageLength = null; // Clear cache
        return $this;
    }

    /**
     * Sets the request object for the DatatableBuilder.
     */
    public function setRequest(FormRequest|ListRequest $request): DatatableBuilder
    {
        $this->request = $request;
        // Clear caches when request changes
        $this->resolvedSearchable = null;
        $this->resolvedPageLength = null;
        $this->resolvedPaginator = null;
        return $this;
    }

    /**
     * Paginates the query builder results (kept for backward compatibility).
     */
    public function paginate(): Paginator|Collection|CursorPaginator
    {
        return $this->request->boolean('all')
            ? $this->handleGetAll($this->builder)
            : $this->handlePagination($this->builder);
    }

    /**
     * Set the query builder instance.
     */
    public function setBuilder(Builder $builder): static
    {
        $this->builder = $builder;
        return $this;
    }

    /**
     * Sets the fields to be selected in the query builder.
     */
    public function setFields(?array $fields): DatatableBuilder
    {
        $this->fields = $fields;
        return $this;
    }

    /**
     * Get configuration value with caching for performance
     */
    private function getConfigValue(string $key, mixed $default = null): mixed
    {
        return config("controller-helpers.{$key}", $default);
    }

    /**
     * Clear all caches (useful for testing or long-running processes)
     */
    public static function clearCache(): void
    {
        self::$configCache = [];
        SearchFilter::clearCache();
        SortFilter::clearCache();
    }
}
