<?php

namespace Miladshm\ControllerHelpers\Libraries\DataTableBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class SortFilter
{
    private static array $schemaCache = [];

    public function __construct(private Request $request, private string $order = 'desc')
    {
    }

    /**
     * Apply sorting to the query builder.
     *
     * This method applies sorting to the query builder based on the request
     * parameters. It takes the sort parameters from the request and applies
     * them to the query builder. If no sort parameters are provided, it
     * defaults to sorting by the order column if it exists in the table.
     *
     * @param Builder $builder The query builder instance.
     * @param callable $next The next filter in the pipeline.
     * @return Builder The modified query builder.
     */
    public function __invoke(Builder $builder, $next): Builder
    {
        $sortParam = $this->getConfigValue('params.sort');

        // Apply custom sorting if provided
        if ($this->request->filled($sortParam)) {
            $this->applyCustomSort($builder, $sortParam);
        } else {
            // Apply default sorting by order column if it exists
            $this->applyDefaultSort($builder);
        }

        return $next($builder);
    }

    /**
     * Apply custom sorting based on request parameters
     */
    private function applyCustomSort(Builder $builder, string $sortParam): void
    {
        $sortData = $this->request->input($sortParam);
        
        if (is_array($sortData) && isset($sortData['column'])) {
            $column = $sortData['column'];
            $direction = $sortData['dir'] ?? $this->order;
            
            // Validate column exists before applying sort
            if ($this->hasColumn($builder, $column)) {
                $builder->orderBy($builder->qualifyColumn($column), $direction);
            }
        }
    }

    /**
     * Apply default sorting by order column
     */
    private function applyDefaultSort(Builder $builder): void
    {
        $orderColumn = $this->getConfigValue('order_column');
        
        if ($this->hasColumn($builder, $orderColumn)) {
            $builder->orderBy($builder->qualifyColumn($orderColumn), $this->order);
        }
    }

    /**
     * Check if column exists in the model's table using cache
     */
    private function hasColumn(Builder $builder, string $column): bool
    {
        $table = $builder->getModel()->getTable();
        $connection = $builder->getModel()->getConnectionName();
        $cacheKey = ($connection ?? 'default') . '.' . $table . '.' . $column;
        
        if (!isset(self::$schemaCache[$cacheKey])) {
            self::$schemaCache[$cacheKey] = Schema::connection($connection)
                ->hasColumn($table, $column);
        }
        
        return self::$schemaCache[$cacheKey];
    }

    /**
     * Get configuration value with caching
     */
    private function getConfigValue(string $key): mixed
    {
        static $configCache = [];
        
        if (!isset($configCache[$key])) {
            $configCache[$key] = config("controller-helpers.{$key}");
        }
        
        return $configCache[$key];
    }

    /**
     * Clear static cache (useful for testing or long-running processes)
     */
    public static function clearCache(): void
    {
        self::$schemaCache = [];
    }
}
