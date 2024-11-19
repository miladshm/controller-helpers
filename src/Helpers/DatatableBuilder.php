<?php

namespace Miladshm\ControllerHelpers\Helpers;

use Illuminate\Contracts\Pagination\Paginator;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Pagination\CursorPaginator;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Support\Traits\Conditionable;
use Miladshm\ControllerHelpers\Http\Requests\ListRequest;

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

    /**
     * Sets the request object for the DatatableBuilder.
     *
     * This method sets the request object used for building the datatable. The request
     * object contains parameters that influence the datatable's behavior, such as
     * pagination, sorting, and searching.
     *
     * @param FormRequest|ListRequest $request The request object containing datatable parameters.
     *                                         It can be either a FormRequest or a ListRequest instance.
     *
     * @return DatatableBuilder Returns the current DatatableBuilder instance, allowing for method chaining.
     */
    public function setRequest(FormRequest|ListRequest $request): DatatableBuilder
    {
        $this->request = $request;
        return $this;
    }


    /**
     * Paginates the query builder results.
     *
     * This method paginates the query builder results based on the request parameters.
     * It returns a Paginator, Collection, or CursorPaginator object depending on the request's 'all' parameter.
     *
     * @return Paginator|Collection|CursorPaginator The paginated query builder results.
     */
    public function paginate(): Paginator|Collection|CursorPaginator
    {
        $this->builder = $this->builder->select($this->fields);
        return $this->request->boolean('all')
            ? $this->builder->get()
            : $this->builder
                ->{$this->getPaginatorMethodName()}($this->getPageLength())
                ->withQueryString();
    }

    /**
     * Returns the paginator method name based on the paginator type.
     *
     * This method returns the paginator method name based on the paginator type set in the
     * request or the default paginator type. The supported paginator types are 'default',
     * 'simple', and 'cursor'.
     *
     * @return string The paginator method name.
     */
    private function getPaginatorMethodName(): string
    {
        return match ($this->getPaginator()) {
            // The default paginator method name is 'paginate'.
            default => 'paginate',
            // The 'simple' paginator type uses the 'simplePaginate' method.
            'simple' => 'simplePaginate',
            // The 'cursor' paginator type uses the 'cursorPaginate' method.
            'cursor' => 'cursorPaginate',
        };
    }


    /**
     * Returns the paginator type.
     *
     * This method returns the paginator type based on the request or the default paginator type.
     * The supported paginator types are 'default', 'simple', and 'cursor'.
     *
     * @return string The paginator type.
     */
    public function getPaginator(): string
    {
        return $this->paginator ?? 'default';
    }


    /**
     * Sets the paginator type.
     *
     * This method sets the paginator type based on the provided argument. The supported
     * paginator types are 'default', 'simple', and 'cursor'. If no paginator type is
     * provided, it defaults to 'default'.
     *
     * @param string|null $paginator The paginator type to set.
     * @return DatatableBuilder Returns the current DatatableBuilder instance, allowing for method chaining.
     */
    public function setPaginator(?string $paginator = 'default'): DatatableBuilder
    {
        $this->paginator = $paginator;
        return $this;
    }


    /**
     * Returns the page length based on the request or the default page length.
     *
     * This method returns the page length based on the request parameter 'page_length'
     * or the default page length defined in the configuration. If the request parameter
     * is not defined, it defaults to the default page length.
     *
     * @return int The page length.
     */
    public function getPageLength(): int
    {
        return $this->request->{getConfigNames('params.page_length')} ?? $this->pageLength ?? getConfigNames('default_page_length');
    }


    /**
     * Sets the page length for pagination.
     *
     * This method sets the page length for pagination. If no page length is provided, it
     * defaults to the default page length defined in the configuration.
     *
     * @param int|null $pageLength The page length to set.
     * @return DatatableBuilder Returns the current DatatableBuilder instance, allowing for method chaining.
     */
    public function setPageLength(?int $pageLength): DatatableBuilder
    {
        $this->pageLength = $pageLength;
        return $this;
    }

    /**
     * Apply search filters to the query builder.
     *
     * This method applies search filters to the query builder based on the request
     * parameters. It takes the search query from the request and applies it to the
     * searchable fields defined in the configuration or the request.
     *
     * @return DatatableBuilder Returns the current DatatableBuilder instance, allowing for method chaining.
     */
    public function search(): static
    {
        $q = $this->request->{getConfigNames('params.search')};
        $searchable = $this->getSearchable();
        if ($this->request->filled(getConfigNames('params.search'))) {
            // Apply search filters to the query builder.
            $this->builder = $this->builder->where(function (Builder $s) use ($q, $searchable) {
                // Loop through the searchable fields and apply the search filter.
                foreach ($searchable ?? [] as $item) {
                    if (Str::contains($item, '.')) {
                        // If the field is a relationship, apply the search filter to the related table.
                        $rel = Str::beforeLast($item, '.');
                        $column = Str::afterLast($item, '.');
                        if (method_exists($this->builder->getModel(), Str::before($rel, '.'))) {
                            $s->orWhereHas($rel, function (Builder $s) use ($q, $column) {
                                // Check if the column exists in the related table.
                                if (Schema::connection($s->getModel()->getConnectionName())->hasColumn($s->getModel()->getTable(), $column)) {
                                    // Apply the search filter to the related table.
                                    $s->where($column, 'LIKE', "%$q%");
                                }
                            });
                        }
                    } elseif (Schema::connection($this->builder->getModel()->getConnectionName())->hasColumn($this->builder->getModel()->getTable(), $item)) {
                        // Apply the search filter to the current table.
                        $s->orWhere($item, 'LIKE', "%$q%");
                    }
                }
            });
        }

        return $this;
    }

    /**
     * Gets the searchable fields based on the request parameters or the default searchable fields.
     *
     * This method returns the searchable fields based on the request parameters or the default searchable fields.
     * It takes the searchable fields from the request parameter 'searchable_columns' and merges it with the default
     * searchable fields defined in the configuration. If the request parameter is not defined, it defaults to the
     * default searchable fields.
     *
     * @return array|mixed|null The searchable fields.
     */
    public function getSearchable(): mixed
    {
        return $this->request->{getConfigNames('params.searchable_columns')} ?? $this->searchable ?? getConfigNames('search.default_searchable');
    }

    /**
     * @param array|string[]|null $searchable
     * @return DatatableBuilder
     */
    public function setSearchable(?array $searchable): DatatableBuilder
    {
        $this->searchable = $searchable;
        return $this;
    }

    /**
     * Sorts the query builder results based on the request parameters or default settings.
     *
     * This method applies sorting to the query builder results using the column and direction
     * specified in the request parameters. If the parameters are not provided, it falls back to
     * default sorting using a configured order column or the primary key of the model.
     *
     * @return $this The current DatatableBuilder instance for method chaining.
     */
    public function sortResult(): static
    {
        // Check if the request contains sort parameters for column and direction.
        if ($this->request->filled(getConfigNames('params.sort') . ".column")) {
            // Retrieve sort details from the request.
            $sort = $this->request->{getConfigNames('params.sort')};
            // Apply sorting using the specified column and direction or defaults.
            $this->builder = $this->builder->orderBy($sort['column'] ?? $this->builder->getModel()->getKeyName(), $sort['dir'] ?? $this->getOrder());

            // Check if a default order column is configured in the schema.
        } elseif (Schema::hasColumn($this->builder->getModel()->getTable(), getConfigNames('order_column'))) {
            // Apply sorting using the configured order column.
            $this->builder = $this->builder->orderBy(getConfigNames('order_column'));

            // Default sorting by the primary key of the model with a specified direction.
        } else {
            $this->builder = $this->builder->orderBy($this->builder->getModel()->getKeyName(), $this->getOrder());
        }

        return $this;
    }

    /**
     * Get the order direction for sorting.
     *
     * This method returns the order direction to be used for sorting the query results.
     * It defaults to 'desc' if no order direction has been set.
     *
     * @return string The order direction ('asc' or 'desc').
     */
    public function getOrder(): string
    {
        // Return the order direction or default to 'desc' if not set.
        return $this->order ?? 'desc';
    }

    /**
     * Sets the order direction for sorting.
     *
     * This method sets the order direction to be used for sorting the query results.
     * It defaults to 'desc' if no order direction has been set.
     *
     * @param string|null $order The order direction to set, either 'asc' or 'desc'.
     * @return DatatableBuilder Returns the current DatatableBuilder instance, allowing for method chaining.
     */
    public function setOrder(?string $order): DatatableBuilder
    {
        $this->order = $order;
        return $this;
    }

    /**
     * Set the query builder instance.
     *
     * This method sets the query builder instance that will be used for constructing
     * the datatable query. It allows for method chaining by returning the current
     * DatatableBuilder instance.
     *
     * @param Builder $builder The query builder instance to set.
     * @return DatatableBuilder The current DatatableBuilder instance.
     */
    public function setBuilder(Builder $builder): static
    {
        $this->builder = $builder; // Assign the provided query builder to the instance variable.
        return $this; // Return the current DatatableBuilder instance for method chaining.
    }

    /**
     * Sets the fields to be selected in the query builder.
     *
     * This method sets the fields to be selected in the query builder. If no fields are provided,
     * it defaults to selecting all fields ('*').
     *
     * @param array|null $fields The fields to be selected in the query builder.
     *
     * @return DatatableBuilder Returns the current DatatableBuilder instance, allowing for method chaining.
     */
    public function setFields(?array $fields): DatatableBuilder
    {
        $this->fields = $fields;
        return $this;
    }
}
