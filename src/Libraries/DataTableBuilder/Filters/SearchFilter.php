<?php

namespace Miladshm\ControllerHelpers\Libraries\DataTableBuilder\Filters;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Miladshm\ControllerHelpers\Libraries\DataTableBuilder\DatatableBuilder;

class SearchFilter
{
    private static array $schemaCache = [];
    private static array $relationCache = [];

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
        $searchParameter = $this->getConfigValue('params.search');
        $q = $this->request->{$searchParameter};

        // Early return if no search query
        if (!$this->request->filled($searchParameter) || empty($this->searchable)) {
            return $next($builder);
        }

        // Apply search filters to the query builder.
        $builder->where(function (Builder $query) use ($builder, $q) {
            $this->applySearchFilters($query, $builder, $q);
        });

        return $next($builder);
    }

    /**
     * Apply search filters for all searchable fields
     */
    private function applySearchFilters(Builder $query, Builder $builder, string $searchTerm): void
    {
        $modelClass = get_class($builder->getModel());
        $tableName = $builder->getModel()->getTable();
        $connectionName = $builder->getModel()->getConnectionName();

        foreach ($this->searchable as $item) {
            if (Str::contains($item, '.')) {
                $this->applyRelationshipSearch($query, $item, $searchTerm, $modelClass);
            } else {
                $this->applyColumnSearch($query, $item, $searchTerm, $tableName, $connectionName);
            }
        }
    }

    /**
     * Apply search filter for relationship fields
     */
    private function applyRelationshipSearch(Builder $query, string $field, string $searchTerm, string $modelClass): void
    {
        $rel = Str::beforeLast($field, '.');
        $column = Str::afterLast($field, '.');
        $relationName = Str::before($rel, '.');

        // Check if relationship exists using cache
        if ($this->hasRelationship($modelClass, $relationName)) {
            $query->orWhereHas($rel, function (Builder $subQuery) use ($column, $searchTerm) {
                $relatedTable = $subQuery->getModel()->getTable();
                $relatedConnection = $subQuery->getModel()->getConnectionName();

                // Check if column exists in related table using cache
                if ($this->hasColumn($relatedTable, $column, $relatedConnection)) {
                    $subQuery->where($column, 'LIKE', "%{$searchTerm}%");
                }
            });
        }
    }

    /**
     * Apply search filter for direct column fields
     */
    private function applyColumnSearch(Builder $query, string $column, string $searchTerm, string $tableName, ?string $connectionName): void
    {
        if ($this->hasColumn($tableName, $column, $connectionName)) {
            $query->orWhere($column, 'LIKE', "%{$searchTerm}%");
        }
    }

    /**
     * Check if column exists in table using cache
     */
    private function hasColumn(string $table, string $column, ?string $connection = null): bool
    {
        $cacheKey = ($connection ?? 'default') . '.' . $table . '.' . $column;

        if (!isset(self::$schemaCache[$cacheKey])) {
            self::$schemaCache[$cacheKey] = Schema::connection($connection)
                ->hasColumn($table, $column);
        }

        return self::$schemaCache[$cacheKey];
    }

    /**
     * Check if relationship exists on model using cache
     */
    private function hasRelationship(string $modelClass, string $relationName): bool
    {
        $cacheKey = $modelClass . '.' . $relationName;

        if (!isset(self::$relationCache[$cacheKey])) {
            self::$relationCache[$cacheKey] = method_exists($modelClass, $relationName);
        }

        return self::$relationCache[$cacheKey];
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
     * Clear static caches (useful for testing or long-running processes)
     */
    public static function clearCache(): void
    {
        self::$schemaCache = [];
        self::$relationCache = [];
    }
}
