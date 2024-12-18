<?php

namespace Miladshm\ControllerHelpers\Libraries\DataTableBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Miladshm\ControllerHelpers\Libraries\DataTableBuilder\DatatableBuilder;

class SearchFilter
{


    public function __construct(private Request $request, private ?array $searchable = [])
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
        $searchParameter = getConfigNames('params.search');

        $q = $this->request->{$searchParameter};
        // Apply search filters to the query builder.
        $builder->when($this->request->filled($searchParameter), fn(Builder $query) => $query->where(function (Builder $s) use ($builder, $q) {
            // Loop through the searchable fields and apply the search filter.
            foreach ($this->searchable ?? [] as $item) {
                if (Str::contains($item, '.')) {
                    // If the field is a relationship, apply the search filter to the related table.
                    $rel = Str::beforeLast($item, '.');
                    $column = Str::afterLast($item, '.');
                    if (method_exists($builder->getModel(), Str::before($rel, '.'))) {
                        $s->orWhereHas($rel, function (Builder $s) use ($q, $column) {
                            // Check if the column exists in the related table.
                            if (Schema::connection($s->getModel()->getConnectionName())->hasColumn($s->getModel()->getTable(), $column)) {
                                // Apply the search filter to the related table.
                                $s->where($column, 'LIKE', "%$q%");
                            }
                        });
                    }
                } elseif (Schema::connection($builder->getModel()->getConnectionName())->hasColumn($builder->getModel()->getTable(), $item)) {
                    // Apply the search filter to the current table.
                    $s->orWhere($item, 'LIKE', "%$q%");
                }
            }
        }));


        return $next($builder);
    }
}
