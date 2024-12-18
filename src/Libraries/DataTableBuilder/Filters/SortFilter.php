<?php

namespace Miladshm\ControllerHelpers\Libraries\DataTableBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Miladshm\ControllerHelpers\Libraries\DataTableBuilder\DatatableBuilder;

class SortFilter
{


    public function __construct(private Request $request, private string $order = 'desc')
    {
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
    public function __invoke(Builder $builder, $next)
    {
        $sortParam = getConfigNames('params.sort');

        // Check if the request contains sort parameters for column and direction.
        if ($this->request->filled($sortParam) . ".column") {
            // Retrieve sort details from the request.
            $sort = $this->request->{$sortParam};
            // Apply sorting using the specified column and direction or defaults.
            $builder = $builder->orderBy($sort['column'] ?? $builder->getModel()->getKeyName(), $sort['dir'] ?? $this->order);

            // Check if a default order column is configured in the schema.
        } elseif (Schema::hasColumn($builder->getModel()->getTable(), getConfigNames('order_column'))) {
            // Apply sorting using the configured order column.
            $builder = $builder->orderBy(getConfigNames('order_column'));

            // Default sorting by the primary key of the model with a specified direction.
        } else {
            $builder = $builder->orderBy($builder->getModel()->getKeyName(), $this->order);
        }

        return $next($builder);
    }
}
